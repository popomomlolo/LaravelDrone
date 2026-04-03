<?php

namespace App\Exports;

use App\Models\Apprentis;
use App\Models\Classes;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Illuminate\Support\Collection;

class statistiqueExport implements FromCollection, WithHeadings, ShouldAutoSize
{
    protected $idApprentis;
    protected $idClasses;

    public function __construct($idApprentis = null, $idClasses = null)
    {
        $this->idApprentis = $idApprentis;
        $this->idClasses   = $idClasses;
    }

    /**
     * Retourne les données à exporter
     */
    public function collection(): Collection
    {
        $query = Apprentis::with(['classes', 'sessions.objectifs']);

        if ($this->idApprentis) {
            $query->whereIn('id_apprentis', (array) $this->idApprentis);
        }

        if ($this->idClasses) {
            $query->where('id_classes', $this->idClasses);
        }

        $apprentis = $query->orderBy('nom')->get();

        $rows = collect();

        foreach ($apprentis as $apprenti) {
            // Récupère tous les objectifs de toutes ses sessions
            $resultatsObjectifs = [];
            foreach ($apprenti->sessions as $session) {
                foreach ($session->objectifs as $obj) {
                    $libelle = $obj->libelle_objectifs;
                    // Si déjà réussi une fois → on garde réussi
                    if (!isset($resultatsObjectifs[$libelle]) || !$resultatsObjectifs[$libelle]) {
                        $resultatsObjectifs[$libelle] = (bool) $obj->pivot->reussi;
                    }
                }
            }

            $rows->push([
                'Nom'                    => $apprenti->nom,
                'Prénom'                 => $apprenti->prenom,
                'Classe'                 => $apprenti->classes->libelle_classes ?? '—',
                'Cerceaux'               => isset($resultatsObjectifs['Cerceaux'])            ? ($resultatsObjectifs['Cerceaux']            ? 'Réussi' : 'Échoué') : '—',
                'Atterrissage'           => isset($resultatsObjectifs['Atterrissage'])         ? ($resultatsObjectifs['Atterrissage']         ? 'Réussi' : 'Échoué') : '—',
                'Positionnement'         => isset($resultatsObjectifs['Positionnement'])       ? ($resultatsObjectifs['Positionnement']       ? 'Réussi' : 'Échoué') : '—',
                "Maintien d'Altitude"    => isset($resultatsObjectifs["Maintien d'Altitude"])  ? ($resultatsObjectifs["Maintien d'Altitude"]  ? 'Réussi' : 'Échoué') : '—',
                'Tours'                  => isset($resultatsObjectifs['Tours'])                ? ($resultatsObjectifs['Tours']                ? 'Réussi' : 'Échoué') : '—',
                'Nombre de sessions'     => $apprenti->sessions->count(),
            ]);
        }

        return $rows;
    }

    /**
     * En-têtes des colonnes
     */
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

    /**
     * Génère le HTML pour l'export PDF (une page par classe)
     */
    public static function genererHtmlPdf(): string
    {
        $classes = Classes::with(['apprentis.sessions.objectifs'])->orderBy('libelle_classes')->get();

        $objectifsNoms = [
            'Cerceaux',
            'Atterrissage',
            'Positionnement',
            "Maintien d'Altitude",
            'Tours',
        ];

        $html = '
        <style>
            body { font-family: Arial, sans-serif; font-size: 11px; }
            h1   { font-size: 16px; margin-bottom: 4px; }
            h2   { font-size: 13px; color: #444; margin-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
            th { background: #2d3748; color: #fff; padding: 6px 8px; text-align: left; font-size: 10px; }
            td { padding: 5px 8px; border-bottom: 1px solid #e2e8f0; }
            tr:nth-child(even) { background: #f7fafc; }
            .reussi { color: #22543d; font-weight: bold; }
            .echoue { color: #c53030; font-weight: bold; }
            .page-break { page-break-after: always; }
        </style>';

        foreach ($classes as $index => $classe) {
            $isLast = $index === $classes->count() - 1;

            $html .= '<div class="' . ($isLast ? '' : 'page-break') . '">';
            $html .= '<h1>Drone Academy</h1>';
            $html .= '<h2>' . htmlspecialchars($classe->libelle_classes) . '</h2>';
            $html .= '<table>';
            $html .= '<thead><tr>';
            $html .= '<th>Nom</th><th>Prénom</th>';
            foreach ($objectifsNoms as $obj) {
                $html .= '<th>' . htmlspecialchars($obj) . '</th>';
            }
            $html .= '<th>Sessions</th>';
            $html .= '</tr></thead><tbody>';

            foreach ($classe->apprentis()->with('sessions.objectifs')->orderBy('nom')->get() as $apprenti) {
                $resultats = [];
                foreach ($apprenti->sessions as $session) {
                    foreach ($session->objectifs as $obj) {
                        $libelle = $obj->libelle_objectifs;
                        if (!isset($resultats[$libelle]) || !$resultats[$libelle]) {
                            $resultats[$libelle] = (bool) $obj->pivot->reussi;
                        }
                    }
                }

                $html .= '<tr>';
                $html .= '<td>' . htmlspecialchars($apprenti->nom) . '</td>';
                $html .= '<td>' . htmlspecialchars($apprenti->prenom) . '</td>';

                foreach ($objectifsNoms as $obj) {
                    if (!isset($resultats[$obj])) {
                        $html .= '<td>—</td>';
                    } elseif ($resultats[$obj]) {
                        $html .= '<td class="reussi">✓ Réussi</td>';
                    } else {
                        $html .= '<td class="echoue">✗ Échoué</td>';
                    }
                }

                $html .= '<td>' . $apprenti->sessions->count() . '</td>';
                $html .= '</tr>';
            }

            $html .= '</tbody></table></div>';
        }

        return $html;
    }
}