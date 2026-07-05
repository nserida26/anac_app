{{-- resources/views/centre/simulateurs/index.blade.php --}}
@extends('centre.layouts.app')
@section('title', 'Gestion des simulateurs')
@section('contentheader', 'Dispositifs de formation')
@section('contentheaderlink')
    <a href="{{ route('centre.dashboard') }}">Tableau de bord</a> / Simulateurs
@endsection

@push('css')
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/plugins/sweetalert2/sweetalert2.min.css') }}">
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Liste des simulateurs</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modalAjoutSimulateur">
                            <i class="fas fa-plus"></i> Ajouter un simulateur
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <table id="tableSimulateurs" class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Libellé</th>
                                <th>Constructeur</th>
                                <th>Modèle</th>
                                <th>Niveau qualification</th>
                                <th>Statut validation</th>
                                <th>Date expiration</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($simulateurs as $simulateur)
                            <tr>
                                <td>{{ $simulateur->id }}</td>
                                <td>{{ $simulateur->libelle }}</td>
                                <td>{{ $simulateur->constructeur ?? '-' }}</td>
                                <td>{{ $simulateur->modele ?? '-' }}</td>
                                <td>{{ $simulateur->niveau_qualification ?? '-' }}</td>
                                <td>
                                    @if($simulateur->statut_validation == 'valide')
                                        <span class="badge badge-success">Validé</span>
                                    @elseif($simulateur->statut_validation == 'en_attente')
                                        <span class="badge badge-warning">En attente</span>
                                    @else
                                        <span class="badge badge-danger">Expiré</span>
                                    @endif
                                </td>
                                <td>{{ $simulateur->date_expiration ?? '-' }}</td>
                                <td>
                                    <button class="btn btn-info btn-sm btn-modifier" data-id="{{ $simulateur->id }}">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm btn-supprimer" data-id="{{ $simulateur->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Ajout/Modification Simulateur --}}
<div class="modal fade" id="modalAjoutSimulateur" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un simulateur</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="formSimulateur" action="{{ route('centre.simulateurs.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Libellé *</label>
                                <input type="text" name="libelle" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Constructeur *</label>
                                <input type="text" name="constructeur" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Modèle *</label>
                                <input type="text" name="modele" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Numéro de série</label>
                                <input type="text" name="numero_serie" class="form-control">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Niveau de qualification</label>
                                <select name="niveau_qualification" class="form-control">
                                    <option value="">Sélectionner</option>
                                    <option value="Niveau A">Niveau A</option>
                                    <option value="Niveau B">Niveau B</option>
                                    <option value="Niveau C">Niveau C</option>
                                    <option value="Niveau D">Niveau D</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date de délivrance initiale *</label>
                                <input type="date" name="date_delivrance_initiale" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Date d'expiration *</label>
                                <input type="date" name="date_expiration" class="form-control" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Compagnie</label>
                                <select name="compagnie" class="form-control">
                                    <option value="Global">Global</option>
                                    <option value="MAI">MAI</option>
                                    <option value="CLASS AVIATION">CLASS AVIATION</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description technique</label>
                                <textarea name="description_technique" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Document de certification</label>
                                <input type="file" name="document_certification" class="form-control" accept=".pdf,.doc,.docx">
                                <small class="text-muted">Joindre le certificat de qualification du simulateur</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('script')
    <script src="{{ asset('assets/admin/plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') }}"></script>
    <script src="{{ asset('assets/admin/plugins/sweetalert2/sweetalert2.min.js') }}"></script>
@endpush

@push('custom')
<script>
    $(function() {
        $('#tableSimulateurs').DataTable({
            "paging": true,
            "lengthChange": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "autoWidth": false,
            "responsive": true,
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.25/i18n/French.json"
            }
        });

        $('.btn-supprimer').click(function() {
            let id = $(this).data('id');
            Swal.fire({
                title: 'Confirmation',
                text: 'Voulez-vous vraiment supprimer ce simulateur ?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Oui, supprimer',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '/centre/simulateurs/' + id,
                        type: 'DELETE',
                        data: { _token: '{{ csrf_token() }}' },
                        success: function() {
                            location.reload();
                        }
                    });
                }
            });
        });
    });
</script>
@endpush