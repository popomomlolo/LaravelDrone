@extends('layouts.base')

@section('title', 'Statistiques')

@section('content')

<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="{{ asset('js/Radialchart.js') }}"></script>
<style>

</style>

<div class="page-wrap">

    <div class="page-title">Statistiques</div>
    <p class="page-sub">Filtrez par classe et/ou par objectif pour afficher les apprentis</p>

    {{-- ── Combobox ── --}}
    <div class="filtres">

        <div class="filtre-group">
            <label for="selectClasse">Classe</label>
            <select id="selectClasse">
                <option value="">-- Toutes les classes --</option>
                @foreach ($classes as $classe)
                    <option value="{{ $classe->id_classes }}">
                        {{ $classe->libelle_classes }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="filtre-group">
            <label for="selectObjectif">Objectif</label>
            <select id="selectObjectif">
                <option value="">-- Tous les objectifs --</option>
                @foreach ($objectifs as $objectif)
                    <option value="{{ $objectif->id_objectifs }}">
                        {{ $objectif->libelle_objectifs }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="spinner" id="spinner"></div>
    </div>

    {{-- ── Zone tableau (injectée par AJAX) ── --}}
    <div id="tableZone">
        <p class="msg-info">Sélectionnez au moins un filtre pour afficher les résultats.</p>
    </div>

</div>

{{-- ── Modale ── --}}
<div class="modal-overlay" id="modalOverlay">
    <div class="modal-box" id="modalBox">
        <button class="modal-close" onclick="fermerModale()">✕</button>
        <div class="modal-identite">
            <div class="modal-nom"    id="modalNom"></div>
            <div class="modal-classe" id="modalClasse"></div>
        </div>
        <div id="modalSessions"></div>
    </div>
</div>

{{-- ── Scripts (tous en bas) ── --}}
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<script>
    let tableInstance = null;
    let tousLesApprentis = []; // stocke les données pour la modale

    const urlFiltrer = '{{ route('statistique.filtrer') }}';

    // ── Écoute les changements des combobox ──
    $('#selectClasse, #selectObjectif').on('change', function () {
        const idClasse   = $('#selectClasse').val();
        const idObjectif = $('#selectObjectif').val();

        if (!idClasse && !idObjectif) {
            $('#tableZone').html('<p class="msg-info">Sélectionnez au moins un filtre pour afficher les résultats.</p>');
            if (tableInstance) { tableInstance.destroy(); tableInstance = null; }
            return;
        }

        chargerDonnees(idClasse, idObjectif);
    });

    // ── Appel AJAX ──
    function chargerDonnees(idClasse, idObjectif) {
        $('#spinner').show();

        $.ajax({
            url: urlFiltrer,
            method: 'GET',
            data: { id_classes: idClasse, id_objectifs: idObjectif },
            success: function (data) {
                tousLesApprentis = data;
                afficherTableau(data);
            },
            error: function () {
                $('#tableZone').html('<p class="msg-info" style="color:red;">Erreur lors du chargement des données.</p>');
            },
            complete: function () {
                $('#spinner').hide();
            }
        });
    }

    // ── Construit et affiche le DataTable ──
    function afficherTableau(data) {

        // Détruit l'ancienne instance si elle existe
        if (tableInstance) { tableInstance.destroy(); }

        if (data.length === 0) {
            $('#tableZone').html('<p class="msg-info">Aucun apprenti trouvé pour ces filtres.</p>');
            return;
        }

        // Construit le HTML du tableau
        let rows = '';
        data.forEach((a, index) => {
            rows += `<tr data-index="${index}" style="cursor:pointer">
                <td>${a.nom}</td>
                <td>${a.prenom}</td>
                <td>${a.classe}</td>
                <td>${a.nb_sessions}</td>
            </tr>`;
        });

        $('#tableZone').html(`
            <table id="apprentisTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Classe</th>
                        <th>Nb sessions</th>
                    </tr>
                </thead>
                <tbody>${rows}</tbody>
            </table>
        `);

        // Initialise DataTables
        tableInstance = $('#apprentisTable').DataTable({
            language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' },
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            order: [[0, 'asc']]
        });

        // Clic sur une ligne → ouvre la modale
        $('#apprentisTable tbody').on('click', 'tr', function () {
            const index = $(this).data('index');
            ouvrirModale(tousLesApprentis[index]);
        });
    }

    // ── Ouvre la modale avec les détails de l'apprenti ──
    function ouvrirModale(apprenti) {
        document.getElementById('modalNom').textContent    = apprenti.nom + ' ' + apprenti.prenom;
        document.getElementById('modalClasse').textContent = '📚 ' + apprenti.classe;

        let html = '';

        if (apprenti.sessions.length === 0) {
            html = '<p class="empty-obj">Aucune session réalisée.</p>';
        } else {
            apprenti.sessions.forEach((session, i) => {
                const reussis = session.objectifs.filter(o => o.reussi);
                const echoues = session.objectifs.filter(o => !o.reussi);

                // Objectifs réussis
                let htmlReussis = reussis.length === 0
                    ? '<p class="empty-obj">Aucun objectif réussi</p>'
                    : reussis.map(o => `
                        <div class="objectif-item">
                            <span class="dot reussi"></span>
                            <span>${o.libelle}</span>
                            <span class="quantite">${o.quantite_realisee} / ${o.quantite_a_atteindre}</span>
                        </div>`).join('');

                // Objectifs échoués
                let htmlEchoues = echoues.length === 0
                    ? '<p class="empty-obj">Aucun objectif échoué</p>'
                    : echoues.map(o => `
                        <div class="objectif-item">
                            <span class="dot echoue"></span>
                            <span>${o.libelle}</span>
                            <span class="quantite">${o.quantite_realisee} / ${o.quantite_a_atteindre}</span>
                        </div>`).join('');

                html += `
                    <div class="session-block">
                        <div class="session-header">
                            <span class="session-titre">Session ${i + 1} — ${session.date}</span>
                            <span class="session-meta">${session.type_drone} · ${session.type_environnement} · ${session.conditions_meteo}</span>
                        </div>
                        <div class="session-body">
                            <div class="section-label">✓ Objectifs réussis</div>
                            ${htmlReussis}
                            <div class="section-label">✗ Objectifs échoués</div>
                            ${htmlEchoues}
                        </div>
                    </div>`;
            });
        }

        document.getElementById('modalSessions').innerHTML = html;
        document.getElementById('modalOverlay').classList.add('active');
    }

    // ── Ferme la modale ──
    function fermerModale() {
        document.getElementById('modalOverlay').classList.remove('active');
    }

    // Ferme en cliquant en dehors
    document.getElementById('modalOverlay').addEventListener('click', function (e) {
        if (e.target === this) fermerModale();
    });
</script>


@endsection