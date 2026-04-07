@extends('admin.layout')

@section('content')
@if(session('success'))
<div class="alert alert-success alert-dismissible" role="alert" style="margin-bottom: 20px; padding: 15px; background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; border-radius: 4px;">
    <strong>Success!</strong> {{ session('success') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="float: right; font-size: 21px; font-weight: 700; line-height: 1; color: #000; opacity: .5; background: transparent; border: 0; cursor: pointer;">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif
@if(session('error'))
<div class="alert alert-danger alert-dismissible" role="alert" style="margin-bottom: 20px; padding: 15px; background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; border-radius: 4px;">
    <strong>Error!</strong> {{ session('error') }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close" style="float: right; font-size: 21px; font-weight: 700; line-height: 1; color: #000; opacity: .5; background: transparent; border: 0; cursor: pointer;">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
@endif

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>Gallery Management</h5>
                <button class="btn btn-primary" data-toggle="modal" data-target="#addGalleryModal">Tambah Gallery</button>
            </div>
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($galleries as $gallery)
                            <tr>
                                <td>{{ $gallery->id }}</td>
                                <td>
                                    <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}" style="width: 60px; height: 40px; object-fit: cover; border-radius: 5px;">
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editGallery({{ $gallery->id }}, '{{ $gallery->title }}', '{{ $gallery->image_url }}')">Edit</button>
                                    <button class="btn btn-sm {{ $gallery->is_published ? 'btn-success' : 'btn-secondary' }}" onclick="toggleCarousel({{ $gallery->id }})">
                                        Carousel
                                    </button>
                                    <form action="{{ route('admin.gallery.delete', $gallery->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Hapus gallery ini?')">Hapus</button>
                                    </form>
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

<!-- Add Gallery Modal -->
<div class="modal fade" id="addGalleryModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.gallery.add') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tambah Gallery</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Image</label>
                        <input type="file" name="image" class="form-control" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Gallery Modal -->
<div class="modal fade" id="editGalleryModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="editGalleryForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Edit Gallery</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label>Current Image</label>
                        <div>
                            <img id="currentImage" src="" alt="Current" style="width: 100px; height: 60px; object-fit: cover; border-radius: 5px;">
                        </div>
                    </div>
                    <div class="form-group">
                        <label>New Image (optional)</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function editGallery(id, title, imageUrl) {
    document.getElementById('currentImage').src = imageUrl;
    document.getElementById('editGalleryForm').action = '/admin/gallery/' + id + '/update';
    $('#editGalleryModal').modal('show');
}

function toggleCarousel(id) {
    // Cek jumlah gallery yang sudah published
    const publishedCount = {{ $galleries->where('is_published', true)->count() }};
    
    // Jika sudah ada 4 foto published dan foto ini belum published, tampilkan peringatan
    const currentButton = event.target;
    const isCurrentlyPublished = currentButton.classList.contains('btn-success');
    
    if (!isCurrentlyPublished && publishedCount >= 5) {
        alert('Maksimal 5 foto untuk carousel!');
        return;
    }
    
    // Submit form untuk toggle status
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/gallery/' + id + '/toggle-carousel';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection