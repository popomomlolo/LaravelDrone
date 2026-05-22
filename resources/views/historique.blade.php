@extends('layouts.layout')

@section('title', 'Historique')

@section('content')

    <div class="page-title">Historique</div>
    <p class="page-sub">Résultats de toutes les sessions de vol par apprenti</p>

    <div class="card-dark">
        <table id="historiqueTable" class="table dataTable w-100">
            <thead>
                <tr>
                    <th>Apprenti</th>
                    <th>Classe</th>
                    <th>Résultat</th>
                    <th>Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($sessions as $session)
                    @php
                        $reussi = $session->objectifs->where('pivot.reussi', true)->count();
                        $total  = $session->objectifs->count();
                    @endphp
                    <tr>
                        <td>{{ $session->apprenti->nom }} {{ $session->apprenti->prenom }}</td>
                        <td>
                            <span class="badge-app badge-app-accent">
                                {{ $session->apprenti->classe->libelle_classe ?? '—' }}
                            </span>
                        </td>
                        <td>
                            @if ($total === 0)
                                <span class="result-none">Aucun objectif</span>
                            @elseif ($reussi === $total)
                                <span class="result-success">✓ Réussi ({{ $reussi }}/{{ $total }})</span>
                            @else
                                <span class="result-failure">✗ Échoué ({{ $reussi }}/{{ $total }})</span>
                            @endif
                        </td>
                        <td>{{ \Carbon\Carbon::parse($session->date_heure)->format('d/m/Y H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

@endsection

@push('scripts')
<script>
    $(document).ready(function () {
        $('#historiqueTable').DataTable({
            language  : { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/fr-FR.json' },
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            order     : [[3, 'desc']]
        });
    });
</script>
@endpush