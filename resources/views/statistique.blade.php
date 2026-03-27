@extends('layouts.base')

@section('title', 'Statistiques')

@section('content')

{{-- jQuery + DataTables --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<style>

</style>

<div class="page-wrap">

    <div class="page-title">Statistiques</div>
    <p class="page-sub">Sélectionnez une classe pour afficher ses apprentis</p>

    {{-- ── Combobox ── --}}
    <div class="select-wrap">
        <label for="classeSelect">Classe</label>
        <select id="classeSelect" onchange="filtrerClasse(this.value)">
            <option value="">-- Choisir une classe --</option>
            @foreach ($classes as $classe)
                <option value="{{ $classe->id_classes }}"
                    {{ $classeSelectee == $classe->id_classes ? 'selected' : '' }}>
                    {{ $classe->libelle_classes }}
                </option>
            @endforeach
        </select>
    </div>

    {{-- ── Tableau (visible uniquement si une classe est sélectionnée) ── --}}
    @if ($classeSelectee && $apprentis->isNotEmpty())

        <table id="apprentisTable" class="display" style="width:100%">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Classe</th>
                    <th>Sessions</th>
                    <th>Taux de réussite</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($apprentis as $apprenti)
                    @php
                        // Calcul global sur toutes ses sessions
                        $totalObjectifs  = 0;
                        $totalReussis    = 0;
                        $objectifsDetail = []; // pour la modale

                        foreach ($apprenti->sessions as $session) {
                            foreach ($session->objectifs as $obj) {
                                $key = $obj->id_objectifs . '_' . $session->id_sessions;
                                $objectifsDetail[] = [
                                    'libelle' => $obj->libelle_objectifs,
                                    'reussi'  => (bool) $obj->pivot->reussi,
                                ];
                                $totalObjectifs++;
                                if ($obj->pivot->reussi) $totalReussis++;
                            }
                        }

                        $taux = $totalObjectifs > 0
                            ? round(($totalReussis / $totalObjectifs) * 100)
                            : null;
                    @endphp

                    <tr style="cursor:pointer"
                        onclick="ouvrirModale(
                            '{{ addslashes($apprenti->nom) }}',
                            '{{ addslashes($apprenti->prenom) }}',
                            '{{ addslashes($apprenti->classes->libelle_classes ?? '—') }}',
                            {{ json_encode($objectifsDetail) }}
                        )">
                        <td>{{ $apprenti->nom }}</td>
                        <td>{{ $apprenti->prenom }}</td>
                        <td>{{ $apprenti->classes->libelle_classes ?? '—' }}</td>
                        <td>{{ $apprenti->sessions->count() }}</td>
                        <td>
                            @if ($taux === null)
                                <span style="color:#636b6f;">—</span>
                            @elseif ($taux >= 75)
                                <span style="color:green; font-weight:600;">{{ $taux }}%</span>
                            @elseif ($taux >= 50)
                                <span style="color:orange; font-weight:600;">{{ $taux }}%</span>
                            @else
                                <span style="color:red; font-weight:600;">{{ $taux }}%</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    @elseif ($classeSelectee && $apprentis->isEmpty())
        <p class="empty-msg">Aucun apprenti dans cette classe.</p>
    @endif

</div>

{{-- ── Modale ── --}}
<div class="modal-overlay" id="modalOverlay" onclick="fermerModale(event)">
    <div class="modal-box">
        <button class="modal-close" onclick="fermerModaleBtn()">✕</button>

        <div class="modal-name" id="modalNom"></div>
        <div class="modal-classe" id="modalClasse"></div>

        <div class="modal-section-title">✓ Objectifs réussis</div>
        <div id="modalReussis"></div>

        <div class="modal-section-title">✗ Objectifs échoués</div>
        <div id="modalEchoues"></div>
    </div>
</div>

<script>
    // ── Redirection sur changement de classe ──
    function filtrerClasse(idClasse) {
        window.location.href = '{{ route('statistique.index') }}?id_classes=' + idClasse;
    }

    // ── Ouvrir la modale ──
    function ouvrirModale(nom, prenom, classe, objectifs) {
        document.getElementById('modalNom').textContent    = nom + ' ' + prenom;
        document.getElementById('modalClasse').textContent = classe;

        const reussisEl  = document.getElementById('modalReussis');
        const echouesEl  = document.getElementById('modalEchoues');

        reussisEl.innerHTML = '';
        echouesEl.innerHTML = '';

        const reussis = objectifs.filter(o => o.reussi);
        const echoues = objectifs.filter(o => !o.reussi);

        if (reussis.length === 0) {
            reussisEl.innerHTML = '<p class="empty-msg">Aucun objectif réussi</p>';
        } else {
            reussis.forEach(o => {
                reussisEl.innerHTML += `
                    <div class="objectif-item">
                        <span class="dot reussi"></span>
                        <span>${o.libelle}</span>
                    </div>`;
            });
        }

        if (echoues.length === 0) {
            echouesEl.innerHTML = '<p class="empty-msg">Aucun objectif échoué</p>';
        } else {
            echoues.forEach(o => {
                echouesEl.innerHTML += `
                    <div class="objectif-item">
                        <span class="dot echoue"></span>
                        <span>${o.libelle}</span>
                    </div>`;
            });
        }

        document.getElementById('modalOverlay').classList.add('active');
    }

    // ── Fermer la modale ──
    function fermerModaleBtn() {
        document.getElementById('modalOverlay').classList.remove('active');
    }

    function fermerModale(event) {
        if (event.target === document.getElementById('modalOverlay')) {
            document.getElementById('modalOverlay').classList.remove('active');
        }
    }

    // ── DataTables ──
    $(document).ready(function () {
        if ($('#apprentisTable').length) {
            $('#apprentisTable').DataTable({
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
                },
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50],
                order: [[0, 'asc']]
            });
        }
    });
</script>

@endsection