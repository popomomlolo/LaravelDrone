@extends('layouts.base')

@section('title', 'Historique')

@section('content')

{{-- jQuery + DataTables (absent du layout, on les charge ici) --}}
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

<div style="text-align:left; width:100%; padding: 2rem;">

    <h2 style="font-family: 'Raleway', sans-serif; font-weight: 600; font-size: 1.8rem; margin-bottom: 0.25rem;">
        Historique
    </h2>
    <p style="color: #636b6f; font-size: 0.875rem; margin-bottom: 1.5rem;">
        Résultats de toutes les sessions de vol par apprenti
    </p>

    <table id="historiqueTable" class="display" style="width:100%">
        <thead>
            <tr>
                <th>Apprenti</th>
                <th>Classe</th>
                <th>Résultat</th>
                <th>Date du test</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($sessions as $session)
                <tr>
                    <td>
                        {{ $session->apprenti->nom }}
                        {{ $session->apprenti->prenom }}
                    </td>

                    <td>
                        {{ $session->apprenti->classes->libelle_classes ?? '—' }}
                    </td>

                    <td>
                        @php
                            $reussi = $session->objectifs->where('pivot.reussi', true)->count();
                            $total  = $session->objectifs->count();
                        @endphp

                        @if ($total === 0)
                            <span style="color:#636b6f;">Aucun objectif</span>
                        @elseif ($reussi === $total)
                            <span style="color: green; font-weight: 600;">
                                ✓ Réussi ({{ $reussi }}/{{ $total }})
                            </span>
                        @else
                            <span style="color: red; font-weight: 600;">
                                ✗ Échoué ({{ $reussi }}/{{ $total }})
                            </span>
                        @endif
                    </td>

                    <td>
                        {{ \Carbon\Carbon::parse($session->date_heure)->format('d/m/Y H:i') }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

</div>

<script>
    $(document).ready(function () {
        $('#historiqueTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json'
            },
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            order: [[3, 'desc']]
        });
    });
</script>

@endsection