@extends('layouts.layout')

@section('title', 'Historique des sessions')

@section('content')

    <div class="page-header">Historique</div>
    <p class="page-sub">Résultats de toutes les sessions de vol par apprenti</p>

    <div class="card-dark">
        <table id="historiqueTable" class="table w-100">
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
                        {{-- Nom + Prénom de l'apprenti --}}
                        <td>
                            {{ $session->apprenti->nom }}
                            {{ $session->apprenti->prenom }}
                        </td>

                        {{-- Classe de l'apprenti --}}
                        <td>
                            <span class="badge-classe">
                                {{ $session->apprenti->classes->libelle_classes ?? '—' }}
                            </span>
                        </td>

                        {{-- Réussi : on regarde si au moins un objectif est réussi --}}
                        <td>
                            @php
                                $reussi = $session->objectifs->where('pivot.reussi', true)->count();
                                $total  = $session->objectifs->count();
                            @endphp

                            @if ($total === 0)
                                <span class="badge-echoue">Aucun objectif</span>
                            @elseif ($reussi === $total)
                                <span class="badge-reussi">✓ Réussi ({{ $reussi }}/{{ $total }})</span>
                            @else
                                <span class="badge-echoue">✗ Échoué ({{ $reussi }}/{{ $total }})</span>
                            @endif
                        </td>

                        {{-- Date/heure du test --}}
                        <td>
                            {{ \Carbon\Carbon::parse($session->date_heure)->format('d/m/Y H:i') }}
                        </td>
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
            language: {
                url: '//cdn.datatables.net/plug-ins/2.0.0/i18n/fr-FR.json'
            },
            pageLength: 10,
            lengthMenu: [5, 10, 25, 50],
            order: [[3, 'desc']], // tri par date décroissante par défaut
            columnDefs: [
                { orderable: false, targets: [] }
            ]
        });
    });
</script>
@endpush