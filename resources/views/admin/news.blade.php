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
<style>
/* News Table Styles */
.table td {
    word-wrap: break-word;
    word-break: break-word;
    overflow-wrap: break-word;
    max-width: 200px;
    vertical-align: top;
}

.table td:nth-child(2) { /* Title column */
    max-width: 150px;
}

.table td:nth-child(3) { /* Excerpt column */
    max-width: 200px;
}

.table td:nth-child(4) { /* Berita Utama column */
    max-width: 120px;
}

/* Responsive Styles */
@media (max-width: 768px) {
    .card-header h5 { font-size: 16px; }
    .card-header .btn { font-size: 11px; padding: 5px 10px; }
    .table { font-size: 11px; }
    .table td, .table th { padding: 6px 4px; }
    .table img { width: 40px !important; height: 30px !important; }
    .btn-sm { font-size: 10px; padding: 4px 8px; }
    #addNewsModal > div, #editNewsModal > div { width: 95% !important; margin: 20px auto !important; padding: 15px !important; }
    #addNewsModal form > div, #editNewsModal form > div { flex-direction: column !important; }
    #addNewsModal form > div > div, #editNewsModal form > div > div { flex: none !important; width: 100% !important; }
}

@media (max-width: 480px) {
    .card-header h5 { font-size: 14px; }
    .table { font-size: 10px; }
    .btn-sm { font-size: 9px; padding: 3px 6px; }
    .form-control { font-size: 13px; }
    label { font-size: 12px; }
}
</style>

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5>News Management</h5>
                <button class="btn btn-primary btn-sm float-right" onclick="openAddModal()">Add News</button>
            </div>
            <div class="card-block">
                <div class="table-responsive">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Foto</th>
                                <th>Title</th>
                                <th>Excerpt</th>
                                <th>Berita Utama</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($news as $item)
                            <tr>
                                <td>
                                    <img src="{{ $item->image ? asset('storage/' . $item->image) : 'https://images.unsplash.com/photo-1498837167922-ddd27525d352?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80' }}" 
                                         alt="{{ $item->title }}" 
                                         style="width: 60px; height: 40px; object-fit: cover; border-radius: 5px;">
                                </td>
                                <td>{{ Str::limit($item->title, 40) }}</td>
                                <td>{{ Str::limit($item->excerpt ?? $item->content, 50) }}</td>
                                <td>
                                    <button class="btn btn-sm {{ $item->is_featured ? 'btn-success' : 'btn-danger' }}" onclick="toggleFeatured({{ $item->id }}, {{ $item->is_featured ? 'false' : 'true' }})">
                                        Berita Utama
                                    </button>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-warning" onclick="editItem({{ $item->id }})">Edit</button>
                                    <button class="btn btn-sm btn-danger" onclick="deleteItem({{ $item->id }})">Delete</button>
                                </td>
                            </tr>
                            @empty
                            <tr><td colspan="5" class="text-center">No data available</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add News Modal -->
<div id="addNewsModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div style="background-color: white; margin: 5% auto; padding: 20px; border-radius: 10px; width: 80%; max-width: 800px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h5>Tambah Berita Baru</h5>
            <button type="button" onclick="closeAddModal()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        <form method="POST" action="/admin/news/store" enctype="multipart/form-data">
            @csrf
            <div style="display: flex; gap: 20px;">
                <div style="flex: 2;">
                    <div style="margin-bottom: 15px;">
                        <label>Judul Berita</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label>Excerpt (Ringkasan)</label>
                        <textarea name="excerpt" class="form-control" rows="3"></textarea>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label>Konten Berita</label>
                        <textarea name="content" class="form-control" rows="8" required></textarea>
                    </div>
                </div>
                <div style="flex: 1;">
                    <div style="margin-bottom: 15px;">
                        <label>Gambar</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label><input type="checkbox" name="is_published" checked> Publish</label>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label><input type="checkbox" name="is_featured"> Featured</label>
                    </div>
                </div>
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="closeAddModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Edit News Modal -->
<div id="editNewsModal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5);">
    <div style="background-color: white; margin: 5% auto; padding: 20px; border-radius: 10px; width: 80%; max-width: 800px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h5>Edit Berita</h5>
            <button type="button" onclick="closeEditModal()" style="background: none; border: none; font-size: 24px; cursor: pointer;">&times;</button>
        </div>
        <form id="editNewsForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <input type="hidden" id="editNewsId" name="news_id">
            <div style="display: flex; gap: 20px;">
                <div style="flex: 2;">
                    <div style="margin-bottom: 15px;">
                        <label>Judul Berita</label>
                        <input type="text" id="editTitle" name="title" class="form-control" required>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label>Excerpt (Ringkasan)</label>
                        <textarea id="editExcerpt" name="excerpt" class="form-control" rows="3"></textarea>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label>Konten Berita</label>
                        <textarea id="editContent" name="content" class="form-control" rows="8" required></textarea>
                    </div>
                </div>
                <div style="flex: 1;">
                    <div style="margin-bottom: 15px;">
                        <label>Gambar</label>
                        <input type="file" name="image" class="form-control" accept="image/*">
                        <div id="currentImage" style="margin-top: 10px;"></div>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label><input type="checkbox" id="editIsPublished" name="is_published"> Publish</label>
                    </div>
                    <div style="margin-bottom: 15px;">
                        <label><input type="checkbox" id="editIsFeatured" name="is_featured"> Featured</label>
                    </div>
                </div>
            </div>
            <div style="text-align: right; margin-top: 20px;">
                <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Batal</button>
                <button type="submit" class="btn btn-primary">Update</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
function openAddModal() {
    document.getElementById('addNewsModal').style.display = 'block';
}

function closeAddModal() {
    document.getElementById('addNewsModal').style.display = 'none';
}

function editItem(id) {
    // Fetch news data
    fetch('/admin/news/' + id + '/edit')
        .then(response => response.json())
        .then(data => {
            document.getElementById('editNewsId').value = data.id;
            document.getElementById('editTitle').value = data.title;
            document.getElementById('editExcerpt').value = data.excerpt || '';
            document.getElementById('editContent').value = data.content;
            document.getElementById('editIsPublished').checked = data.is_published;
            document.getElementById('editIsFeatured').checked = data.is_featured;
            
            // Set form action
            document.getElementById('editNewsForm').action = '/admin/news/' + id + '/update';
            
            // Show current image if exists
            if (data.image) {
                document.getElementById('currentImage').innerHTML = '<img src="/storage/' + data.image + '" style="width: 100px; height: 60px; object-fit: cover; border-radius: 5px;">';
            } else {
                document.getElementById('currentImage').innerHTML = '';
            }
            
            document.getElementById('editNewsModal').style.display = 'block';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error loading news data');
        });
}

function closeEditModal() {
    document.getElementById('editNewsModal').style.display = 'none';
}

function deleteItem(id) {
    if(confirm('Delete this item?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '/admin/news/' + id;
        
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        const methodInput = document.createElement('input');
        methodInput.type = 'hidden';
        methodInput.name = '_method';
        methodInput.value = 'DELETE';
        form.appendChild(methodInput);
        
        document.body.appendChild(form);
        form.submit();
    }
}

function toggleFeatured(id, setFeatured) {
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '/admin/news/toggle-featured';
    
    const csrfInput = document.createElement('input');
    csrfInput.type = 'hidden';
    csrfInput.name = '_token';
    csrfInput.value = '{{ csrf_token() }}';
    form.appendChild(csrfInput);
    
    const idInput = document.createElement('input');
    idInput.type = 'hidden';
    idInput.name = 'news_id';
    idInput.value = id;
    form.appendChild(idInput);
    
    const featuredInput = document.createElement('input');
    featuredInput.type = 'hidden';
    featuredInput.name = 'is_featured';
    featuredInput.value = setFeatured ? '1' : '0';
    form.appendChild(featuredInput);
    
    document.body.appendChild(form);
    form.submit();
}
</script>
@endsection