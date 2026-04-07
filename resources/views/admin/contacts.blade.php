@extends('admin.layout')

@section('content')
<style>
@media (max-width: 768px) {
    .card {
        overflow-x: auto;x $_COOKIE
    }
    .table {
        font-size: 12px;
        min-width: 600px;
    }
    .table th, .table td {
        padding: 8px 4px;
        white-space: nowrap;
    }
    .btn-sm {
        font-size: 10px;
        padding: 4px 8px;
    }
    .badge {
        font-size: 10px;
    }
}

@media (max-width: 480px) {
    .table {
        font-size: 11px;
        min-width: 550px;
    }
    .table th, .table td {
        padding: 6px 3px;
    }
    .btn-sm {
        font-size: 9px;
        padding: 3px 6px;
    }
}
</style>
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Contacts</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Contacts</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        @if($contacts->count() > 0)
            <div class="card">
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nama</th>
                                <th>Email</th>
                                <th>Subject</th>
                                <th>Tanggal</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($contacts as $contact)
                            <tr>
                                <td>{{ $contact->name }}</td>
                                <td>{{ $contact->email }}</td>
                                <td>{{ $contact->subject }}</td>
                                <td>{{ $contact->created_at->format('d/m/Y H:i') }}</td>
                                <td>
                                    @if($contact->is_read)
                                        <span class="badge badge-success">Dibaca</span>
                                    @else
                                        <span class="badge badge-warning">Belum dibaca</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="viewMessage({{ $contact->id }})">Lihat</button>
                                    <form method="POST" action="/admin/contacts/{{ $contact->id }}" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Yakin hapus pesan ini?')">Hapus</button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @else
            <div class="row justify-content-center">
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-body text-center p-2">
                            <i class="fas fa-envelope text-muted mb-2" style="font-size: 2rem;"></i>
                            <p class="text-muted mb-0">No contacts available</p>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

<!-- Modal untuk melihat pesan -->
<div class="modal fade" id="messageModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detail Pesan</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <p><strong>Nama:</strong> <span id="modalName"></span></p>
                <p><strong>Email:</strong> <span id="modalEmail"></span></p>
                <p><strong>Subject:</strong> <span id="modalSubject"></span></p>
                <p><strong>Pesan:</strong></p>
                <div id="modalMessage" style="background:#f8f9fa;padding:10px;border-radius:5px;"></div>
            </div>
        </div>
    </div>
</div>

<script>
function viewMessage(id) {
    fetch(`/admin/contacts/${id}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('modalName').textContent = data.name;
            document.getElementById('modalEmail').textContent = data.email;
            document.getElementById('modalSubject').textContent = data.subject;
            document.getElementById('modalMessage').textContent = data.message;
            $('#messageModal').modal('show');
        });
}
</script>
@endsection