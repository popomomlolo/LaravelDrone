<?php

namespace App\Exports;

use App\Models\Apprentis;
use App\Models\Classes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class statistiqueExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected ?string $idClasse;
    protected ?string $idObjectif;

    public function __construct(?string $idClasse = null, ?string $idObjectif = null)
    {
        $this->idClasse   = $idClasse;
        $this->idObjectif = $idObjectif;
    }

    // ════════════════════════════════════════════════════════════════
    // DONNÉES CSV — applique les 4 cas de filtre
    //
    // Cas 1 : aucun filtre     → tous les apprentis de toutes les classes
    // Cas 2 : classe seule     → apprentis de la classe filtrée
    // Cas 3 : objectif seul    → apprentis ayant fait cet objectif
    // Cas 4 : classe+objectif  → apprentis de la classe + ayant fait cet objectif
    // ════════════════════════════════════════════════════════════════
    public function collection(): Collection
    {
        $query = Apprentis::with(['classe', 'sessions.objectifs'])
            ->orderBy('nom');

        // Filtre par classe si renseigné (cas 2 et 4)
        if ($this->idClasse) {
            $query->where('id_classe', $this->idClasse);
        }

        // Filtre par objectif si renseigné (cas 3 et 4)
        if ($this->idObjectif) {
            $query->whereHas('sessions.objectifs', function ($q) {
                $q->where('objectifs.id_objectif', $this->idObjectif);
            });
        }

        return $query->get()->map(function ($apprenti) {
            return $this->formaterLigne($apprenti);
        });
    }

    // ════════════════════════════════════════════════════════════════
    // FORMATE UNE LIGNE pour le CSV
    // Pour chaque objectif : Réussi si réussi au moins 1 fois, sinon Échoué
    // ════════════════════════════════════════════════════════════════
    private function formaterLigne($apprenti): array
    {
        // Collecte le meilleur résultat par objectif sur toutes les sessions
        $resultats = [];
        foreach ($apprenti->sessions as $session) {
            foreach ($session->objectifs as $obj) {
                $libelle = $obj->libelle_objectif;
                // Une réussite suffit pour marquer l'objectif comme réussi
                if (!isset($resultats[$libelle]) || !$resultats[$libelle]) {
                    $resultats[$libelle] = (bool) $obj->pivot->reussi;
                }
            }
        }

        // Convertit vrai/faux en texte lisible
        $statut = function (string $nom) use ($resultats): string {
            if (!array_key_exists($nom, $resultats)) return '—';
            return $resultats[$nom] ? 'Réussi' : 'Échoué';
        };

        return [
            'Nom'                 => $apprenti->nom,
            'Prénom'              => $apprenti->prenom,
            'Classe'              => $apprenti->classe->libelle_classe ?? '—',
            'Cerceaux'            => $statut('Cerceaux'),
            'Atterrissage'        => $statut('Atterrissage'),
            'Positionnement'      => $statut('Positionnement'),
            "Maintien d'Altitude" => $statut("Maintien d'Altitude"),
            'Tours'               => $statut('Tours'),
            'Nombre de sessions'  => $apprenti->sessions->count(),
        ];
    }

    public function headings(): array
    {
        return [
            'Nom',
            'Prénom',
            'Classe',
            'Cerceaux',
            'Atterrissage',
            'Positionnement',
            "Maintien d'Altitude",
            'Tours',
            'Nombre de sessions',
        ];
    }

    // ════════════════════════════════════════════════════════════════
    // GÉNÈRE LE HTML POUR LE PDF
    //
    // Cas 2 : classe seule     → 1 page pour la classe filtrée, tous les objectifs
    // Cas 3 : objectif seul    → toutes les classes, 1 page chacune, objectif filtré mis en avant
    // Cas 4 : classe+objectif  → 1 page pour la classe filtrée avec objectif filtré mis en avant
    // ════════════════════════════════════════════════════════════════
    public static function genererHtmlPdf(
        ?string $idClasses   = null,
        ?string $idObjectifs = null
    ): string {

        // Charge les classes concernées
        $queryClasses = Classes::with(['apprentis.sessions.objectifs'])
            ->orderBy('libelle_classe');

        if ($idClasses) {
            $queryClasses->where('id_classe', $idClasses);
        }

        $classes = $queryClasses->get();

        // Noms de tous les objectifs à afficher dans les colonnes
        $tousLesObjectifs = [
            'Cerceaux',
            'Atterrissage',
            'Positionnement',
            "Maintien d'Altitude",
            'Tours',
        ];

        $html  = self::stylePdf();
        $total = $classes->count();

        foreach ($classes as $index => $classe) {

            $isLastPage = ($index === $total - 1);

            // Récupère les apprentis en appliquant le filtre objectif si besoin
            $queryApprentis = $classe->apprentis()->with('sessions.objectifs')->orderBy('nom');

            if ($idObjectifs) {
                $queryApprentis->whereHas('sessions.objectifs', function ($q) use ($idObjectifs) {
                    $q->where('objectifs.id_objectif', $idObjectifs);
                });
            }

            $apprentis = $queryApprentis->get();

            $html .= '<div class="' . ($isLastPage ? '' : 'page-break') . '">';
            $html .= '<h1>Drone Academy</h1>';
            $html .= '<h2>' . htmlspecialchars($classe->libelle_classe) . '</h2>';

            // En-têtes du tableau
            $html .= '<table><thead><tr>';
            $html .= '<th>Nom</th><th>Prénom</th>';
            foreach ($tousLesObjectifs as $obj) {
                // Mise en évidence de l'objectif filtré (cas 3 et 4)
                $estFiltre = $idObjectifs && self::objectifCorrespondAuFiltre($obj, $idObjectifs);
                $style     = $estFiltre ? ' style="background:#1a365d;"' : '';
                $html .= '<th' . $style . '>' . htmlspecialchars($obj) . '</th>';
            }
            $html .= '<th>Sessions</th>';
            $html .= '</tr></thead><tbody>';

            foreach ($apprentis as $apprenti) {
                $resultats = self::collecterResultats($apprenti);

                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($apprenti->nom) . '</td>';
                $html .= '<td>' . htmlspecialchars($apprenti->prenom) . '</td>';

                foreach ($tousLesObjectifs as $obj) {
                    if (!array_key_exists($obj, $resultats)) {
                        $html .= '<td>—</td>';
                    } elseif ($resultats[$obj]) {
                        $html .= '<td class="reussi">Réussi</td>';
                    } else {
                        $html .= '<td class="echoue">Échoué</td>';
                    }
                }

                $html .= '<td>' . $apprenti->sessions->count() . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody></table></div>';
        }

        return $html;
    }

    // ════════════════════════════════════════════════════════════════
    // HELPERS PRIVÉS
    // ════════════════════════════════════════════════════════════════

    /**
     * Collecte le meilleur résultat par objectif pour un apprenti
     */
    private static function collecterResultats($apprenti): array
    {
        $resultats = [];
        foreach ($apprenti->sessions as $session) {
            foreach ($session->objectifs as $obj) {
                $libelle = $obj->libelle_objectif;
                if (!isset($resultats[$libelle]) || !$resultats[$libelle]) {
                    $resultats[$libelle] = (bool) $obj->pivot->reussi;
                }
            }
        }
        return $resultats;
    }

    /**
     * Vérifie si le nom d'un objectif correspond à l'id filtré
     * (comparaison simple via la table objectifs)
     */
    private static function objectifCorrespondAuFiltre(string $libelle, string $idObjectifs): bool
    {
        return \App\Models\Objectifs::where('id_objectif', $idObjectifs)
            ->where('libelle_objectif', $libelle)
            ->exists();
    }

    /**
     * CSS du PDF
     */
    private static function stylePdf(): string
    {
        return '
        <style>
            body  { font-family: Arial, sans-serif; font-size: 11px; }
            h1    { font-size: 15px; margin-bottom: 4px; }
            h2    { font-size: 12px; color: #444; margin-bottom: 8px; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
            th    { background:rgb(0, 0, 0); color: #fff; padding: 5px 7px; text-align: left; font-size: 10px; }
            td    { padding: 4px 7px; border-bottom: 1px solid #e2e8f0; }
            tr:nth-child(even) { background: #f7fafc; }
            .reussi     { color:rgb(30, 255, 0); font-weight: bold; }
            .echoue     { color:rgb(255, 0, 0); font-weight: bold; }
            .page-break { page-break-after: always; }
        </style>';
    }
}
