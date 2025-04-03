@extends('layouts.admin')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des réclamations</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Entreprise</th>
                            <th>Contact</th>
                            <th>Type</th>
                            <th>Urgence</th>
                            <th>Statut</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($complaints as $complaint)
                            <tr>
                                <td>#{{ $complaint->id }}</td>
                                <td>{{ $complaint->company_name }}</td>
                                <td>
                                    {{ $complaint->first_name }} {{ $complaint->last_name }}<br>
                                    <small class="text-muted">{{ $complaint->email }}</small>
                                </td>
                                <td>{{ __('complaints.types.' . $complaint->complaint_type) }}</td>
                                <td>
                                    @if($complaint->urgency_level === 'high')
                                        <span class="badge bg-danger">{{ __('complaints.urgency.high') }}</span>
                                    @elseif($complaint->urgency_level === 'medium')
                                        <span class="badge bg-warning">{{ __('complaints.urgency.medium') }}</span>
                                    @else
                                        <span class="badge bg-success">{{ __('complaints.urgency.low') }}</span>
                                    @endif
                                </td>
                                <td>
                                    @if($complaint->status === 'résolu')
                                        <span class="badge bg-success">{{ __('complaints.status.résolu') }}</span>
                                    @elseif($complaint->status === 'en_attente')
                                        <span class="badge bg-warning">{{ __('complaints.status.en_attente') }}</span>
                                    @else
                                        <span class="badge bg-danger">{{ __('complaints.status.non_résolu') }}</span>
                                    @endif
                                </td>
                                <td>{{ $complaint->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.complaints.show', $complaint) }}"
                                           class="btn btn-sm btn-info"
                                           title="Voir les détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <form action="{{ route('admin.complaints.destroy', $complaint) }}"
                                              method="POST"
                                              class="d-inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette réclamation ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">Aucune réclamation trouvée</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $complaints->links() }}
            </div>
        </div>
    </div>
@endsection
