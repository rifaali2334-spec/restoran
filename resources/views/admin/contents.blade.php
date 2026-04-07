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
.hero-section {
    background: linear-gradient(135deg, rgba(255,255,255,0.9), rgba(240,240,240,0.9));
    min-height: 400px;
    display: flex;
    align-items: center;
    position: relative;
    border-radius: 15px;
    margin: 20px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    overflow: hidden;
}

/* Scrollbar for card text - visible on all devices */
.card-text-scroll {
    overflow-y: auto;
    overflow-x: hidden;
}

.card-text-scroll::-webkit-scrollbar {
    width: 6px;
}

.card-text-scroll::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.card-text-scroll::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 10px;
}

.card-text-scroll::-webkit-scrollbar-thumb:hover {
    background: #555;
}

.hero-content {
    flex: 1;
    padding: 40px 50px;
    z-index: 2;
    max-width: 60%;
}

.hero-subtitle {
    font-size: 14px;
    color: #666;
    margin-bottom: 15px;
    position: relative;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.hero-subtitle::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: -8px;
    width: 50px;
    height: 2px;
    background-color: #333;
}

.hero-title {
    font-size: 36px;
    font-weight: bold;
    color: #333;
    margin: 25px 0 20px 0;
    line-height: 1.2;
}

.hero-description {
    font-size: 14px;
    color: #666;
    line-height: 1.6;
    margin-bottom: 30px;
    max-width: 400px;
}

.hero-button {
    background-color: #333;
    color: white;
    padding: 12px 25px;
    text-decoration: none;
    font-weight: bold;
    font-size: 14px;
    display: inline-block;
    transition: background-color 0.3s;
    border: none;
    cursor: pointer;
}

.hero-button:hover {
    background-color: #555;
    color: white;
    text-decoration: none;
}

.hero-image {
    position: absolute;
    right: -80px;
    top: 35%;
    transform: translateY(-50%);
    width: 450px;
    height: 450px;
    border-radius: 50%;
    background: url('https://images.unsplash.com/photo-1512621776951-a57141f2eefd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80') center/cover;
    z-index: 1;
}

.edit-btn {
    position: absolute;
    top: 20px;
    right: 20px;
    background: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    font-weight: 600;
    cursor: pointer;
    z-index: 3;
}

.edit-btn:hover {
    background: #0056b3;
}

.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #fff;
    margin: 5% auto;
    padding: 0;
    border-radius: 15px;
    width: 85%;
    max-width: 800px;
    position: relative;
    box-shadow: 0 10px 40px rgba(0,0,0,0.2);
    display: flex;
    overflow: hidden;
    z-index: 1001;
}

.modal-left {
    flex: 1;
    padding: 30px;
    background: white;
}

.modal-right {
    flex: 1;
    background: #f8f9fa;
    padding: 30px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: flex-start;
}

.modal-content h2 {
    margin: 0 0 25px 0;
    color: #333;
    font-size: 20px;
    font-weight: 600;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 6px;
    color: #333;
    font-weight: 500;
    font-size: 13px;
}

.form-group input,
.form-group textarea {
    width: 100%;
    padding: 10px 12px;
    border: 2px solid #e0e0e0;
    border-radius: 6px;
    font-size: 13px;
    transition: border-color 0.3s;
    box-sizing: border-box;
}

.form-group input:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #007bff;
}

.form-group textarea {
    resize: vertical;
    min-height: 80px;
}

.image-section h3 {
    color: #333;
    font-size: 16px;
    font-weight: 600;
    margin-bottom: 12px;
    text-align: center;
}

.image-upload-area {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: url('https://images.unsplash.com/photo-1512621776951-a57141f2eefd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80') center/cover;
    margin: 0 auto 15px;
    border: 3px solid #e0e0e0;
    position: relative;
    overflow: hidden;
}

.upload-btn {
    background: #6c757d;
    color: white;
    padding: 8px 16px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-size: 12px;
    margin-bottom: 8px;
    width: 100%;
    max-width: 150px;
}

.upload-btn:hover {
    background: #5a6268;
}

.upload-text {
    font-size: 11px;
    color: #666;
    text-align: center;
    margin-bottom: 20px;
    max-width: 150px;
}

.current-image-label {
    font-weight: 600;
    color: #333;
    margin-bottom: 12px;
    text-align: center;
    font-size: 14px;
}

.action-buttons {
    display: flex;
    gap: 12px;
    margin-top: 25px;
    justify-content: flex-end;
    padding-top: 15px;
    border-top: 1px solid #e0e0e0;
}

.btn-cancel {
    background: #6c757d;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    font-size: 13px;
    transition: background-color 0.3s;
}

.btn-cancel:hover {
    background: #5a6268;
}

.btn-save {
    background: #007bff;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 500;
    font-size: 13px;
    transition: background-color 0.3s;
}

.btn-save:hover {
    background: #0056b3;
}

.close {
    color: #999;
    position: absolute;
    top: 15px;
    right: 20px;
    font-size: 28px;
    font-weight: normal;
    cursor: pointer;
    line-height: 1;
    transition: color 0.3s;
    z-index: 10;
}

.close:hover {
    color: #333;
}

/* News Table Styles */
.table {
    table-layout: fixed;
    width: 100%;
}

.table td {
    vertical-align: middle;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.table td:nth-child(1) { /* Foto column */
    width: 10%;
}

.table td:nth-child(2) { /* Title column */
    width: 20%;
}

.table td:nth-child(3) { /* Excerpt column */
    width: 30%;
}

.table td:nth-child(4) { /* No.Berita column */
    width: 20%;
}

.table td:nth-child(5) { /* Publis column */
    width: 10%;
}



/* Responsive Styles */
@media (max-width: 768px) {
    .hero-section { min-height: 250px !important; margin: 10px !important; padding: 15px 10px 15px 25px !important; margin-top: 20px !important; }
    .hero-content { max-width: 45% !important; padding: 15px 10px 15px 0 !important; margin-right: auto !important; }
    .hero-image { position: absolute !important; right: -70px !important; top: 30% !important; transform: translateY(-50%) !important; width: 260px !important; height: 260px !important; }
    .hero-subtitle { font-size: 11px !important; }
    .hero-title { font-size: 20px !important; margin: 15px 0 12px 0 !important; }
    .hero-description { font-size: 11px !important; max-width: 220px !important; margin-bottom: 20px !important; }
    .hero-button { padding: 8px 16px !important; font-size: 11px !important; }
    .edit-btn { padding: 6px 12px !important; font-size: 11px !important; top: 10px !important; right: 10px !important; }
    div[style*="grid-template-columns: repeat(4, 1fr)"] { display: flex !important; overflow-x: auto !important; gap: 20px !important; -webkit-overflow-scrolling: touch !important; grid-template-columns: none !important; }
    div[style*="grid-template-columns: repeat(4, 1fr)"] > div { min-width: 250px !important; margin-top: 100px !important; height: 320px !important; padding: 100px 20px 20px !important; flex-shrink: 0 !important; }
    div[style*="grid-template-columns: repeat(4, 1fr)"] img { width: 150px !important; height: 150px !important; top: -75px !important; }
    div[style*="grid-template-columns: repeat(4, 1fr)"] h3 { font-size: 16px !important; }
    div[style*="grid-template-columns: repeat(4, 1fr)"] p { font-size: 13px !important; }
    div[style*="grid-template-columns: repeat(4, 1fr)"] button { padding: 8px 15px !important; font-size: 12px !important; }
    .modal-content { flex-direction: column !important; width: 95% !important; max-width: 95% !important; margin: 20px auto !important; }
    .modal-left, .modal-right { flex: none !important; width: 100% !important; padding: 20px !important; }
    .table-responsive { overflow-x: auto; }
    .table { font-size: 11px; }
    .table td, .table th { padding: 6px 4px; white-space: nowrap; }
    .btn-sm { font-size: 10px; padding: 4px 8px; }
    .content-header { margin-top: 15px; }
    div[style*="padding: 30px 20px"] { padding: 20px 15px !important; }
    div[style*="font-size: 36px"] { font-size: 20px !important; }
    div[style*="font-size: 16px"] { font-size: 13px !important; }
    div[style*="max-width: 600px"] { max-width: 90% !important; }
    div[style*="width: 60px; height: 3px"] { width: 40px !important; margin: 0 auto 30px !important; }
    div[style*="padding: 80px 20px"] { padding: 40px 15px !important; }
    div[style*="background: white; padding: 20px"] { padding: 15px 10px !important; }
    div[style*="background: white; padding: 20px"] h2 { font-size: 18px !important; margin-bottom: 15px !important; }
}

@media (max-width: 480px) {
    .hero-section { min-height: 200px !important; padding: 10px 10px 10px 20px !important; }
    .hero-content { max-width: 42% !important; padding: 10px 8px 10px 0 !important; margin-right: auto !important; }
    .hero-image { position: absolute !important; right: -75px !important; top: 30% !important; transform: translateY(-50%) !important; width: 200px !important; height: 200px !important; }
    .hero-subtitle { font-size: 10px !important; }
    .hero-title { font-size: 16px !important; margin: 10px 0 8px 0 !important; }
    .hero-description { font-size: 10px !important; max-width: 160px !important; margin-bottom: 15px !important; }
    .hero-button { padding: 6px 12px !important; font-size: 10px !important; }
    h2 { font-size: 18px !important; }
    div[style*="font-size: 36px"] { font-size: 18px !important; }
    div[style*="font-size: 16px"] { font-size: 12px !important; }
    div[style*="padding: 30px 20px"] { padding: 15px 10px !important; }
    div[style*="max-width: 600px"] { max-width: 95% !important; }
    div[style*="width: 60px; height: 3px"] { width: 35px !important; margin: 0 auto 20px !important; }
    div[style*="background: white; padding: 20px"] { padding: 12px 8px !important; }
    div[style*="background: white; padding: 20px"] h2 { font-size: 16px !important; margin-bottom: 12px !important; }
    .form-group input, .form-group textarea { font-size: 13px; padding: 8px; }
    .action-buttons { flex-direction: column; gap: 8px !important; }
    .action-buttons button { width: 100%; }
    div[style*="grid-template-columns: repeat(4, 1fr)"] { display: flex !important; overflow-x: auto !important; gap: 15px !important; -webkit-overflow-scrolling: touch !important; }
    div[style*="grid-template-columns: repeat(4, 1fr)"] > div { min-width: 220px !important; margin-top: 90px !important; height: 320px !important; padding: 100px 20px 20px !important; flex-shrink: 0 !important; }
    div[style*="grid-template-columns: repeat(4, 1fr)"] img { width: 140px !important; height: 140px !important; top: -70px !important; }
    div[style*="grid-template-columns: repeat(4, 1fr)"] h3 { font-size: 15px !important; margin-bottom: 10px !important; }
    div[style*="grid-template-columns: repeat(4, 1fr)"] p { font-size: 12px !important; max-height: 80px !important; }
    div[style*="grid-template-columns: repeat(4, 1fr)"] button { padding: 6px 10px !important; font-size: 11px !important; }
    div[style*="font-size: 36px"] { font-size: 20px !important; }
}
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Contents</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('admin.index') }}">Home</a></li>
                    <li class="breadcrumb-item active">Contents</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<section class="content">
    <div class="container-fluid">
        <!-- Hero Section -->
        <div class="hero-section">
            <button class="edit-btn" onclick="editContent()">EDIT</button>
            <div class="hero-content">
                <div class="hero-subtitle">{{ $heroTitle }}</div>
                <h1 class="hero-title">{{ $heroSubtitle }}</h1>
                <p class="hero-description">
                    {{ $heroDescription }}
                </p>
                <button class="hero-button">TENTANG KAMI</button>
            </div>
            <div class="hero-image" style="background-image: url('{{ $heroImage ? asset('storage/' . $heroImage) : 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}')"></div>
        </div>

        <!-- About Section -->
        <div style="background: white; padding: 30px 20px; margin: 20px; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.08); text-align: center; position: relative;">
            <button class="edit-btn" onclick="editAbout()" style="position: absolute; top: 20px; right: 20px; z-index: 10;">EDIT</button>
            <h2 style="font-size: 36px; font-weight: bold; color: #333; margin-bottom: 30px;">{{ $aboutTitle }}</h2>
            <p style="font-size: 16px; color: #666; line-height: 1.6; max-width: 600px; max-height: 200px; overflow-y: auto; margin: 0 auto 20px; padding: 0 10px;">
                {{ $aboutDescription }}
            </p>
            <div style="width: 60px; height: 3px; background-color: #333; margin: 0 auto 60px;"></div>
        </div>

        <!-- Cards Section -->
        <div style="background-image: url('{{ asset('img/hero1.png') }}'); background-size: cover; background-position: center; padding: 80px 20px; margin: 20px; border-radius: 15px; position: relative; overflow: hidden;">
            <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.3); border-radius: 15px;"></div>
            <div style="position: absolute; top: 20px; right: 20px; z-index: 10;">
                <button class="edit-btn" onclick="addCard()" style="background: #28a745;">TAMBAH</button>
            </div>
            <div style="display: flex; gap: 30px; position: relative; z-index: 1; max-width: 1200px; margin: 0 auto; overflow-x: auto; padding-bottom: 20px;">
                @if($cards->count() > 0)
                    @foreach($cards as $card)
                    <div style="background: white !important; border-radius: 10px; text-align: center; margin-top: 120px; padding: 100px 20px 20px; position: relative; overflow: visible; height: 350px; display: flex; flex-direction: column; justify-content: flex-start; min-width: 280px; flex-shrink: 0;">
                        <img src="{{ $card->image ? asset('storage/' . $card->image) : 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80' }}" alt="{{ $card->title }}" style="width: 180px; height: 180px; border-radius: 50%; object-fit: cover; position: absolute; top: -90px; left: 50%; transform: translateX(-50%);">
                        <div style="padding: 0; margin-top: 20px; flex-grow: 1; display: flex; flex-direction: column; justify-content: flex-start;">
                            <h3 style="font-size: 18px; font-weight: bold; color: #333; margin-bottom: 12px;">{{ $card->title }}</h3>
                            <p class="card-text-scroll" style="font-size: 13px; color: #666; line-height: 1.4; max-width: 180px; margin: 0 auto; padding: 0 10px; text-align: center; max-height: 80px; word-wrap: break-word; overflow-y: auto;">
                                {{ $card->content }}
                            </p>
                        </div>
                        <div style="display: flex; gap: 10px; justify-content: center; padding: 12px 20px; margin-top: auto;">
                            <button onclick="showEditForm({{ $card->id }}, '{{ addslashes($card->title) }}', '{{ addslashes($card->content) }}', '{{ $card->image ? asset('storage/' . $card->image) : 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80' }}')" style="padding: 8px 15px; font-size: 12px; background: #007bff; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">EDIT</button>
                            <button onclick="deleteCard({{ $card->id }})" style="padding: 8px 15px; font-size: 12px; background: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">HAPUS</button>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>

        <!-- News Section -->
        <div style="background: white; padding: 20px; border-radius: 10px; margin: 20px 0;">
            <h2>BERITA KAMI</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Title</th>
                        <th>Excerpt</th>
                        <th>No.Berita</th>
                        <th>Publis</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allNews as $item)
                    <tr>
                        <td>
                            <img src="{{ $item->image ? asset('storage/' . $item->image) : 'https://images.unsplash.com/photo-1498837167922-ddd27525d352?ixlib=rb-4.0.3&auto=format&fit=crop&w=100&q=80' }}" 
                                 alt="{{ $item->title }}" 
                                 style="width: 60px; height: 40px; object-fit: cover; border-radius: 5px;">
                        </td>
                        <td>{{ Str::limit($item->title, 40) }}</td>
                        <td>{{ Str::limit($item->excerpt ?? $item->content, 50) }}</td>
                        <td>
                            @php
                                $positions = [];
                                for($i = 1; $i <= 5; $i++) {
                                    $pos = \App\Models\Content::where('key', 'news_position_' . $i)->where('content', $item->id)->first();
                                    if($pos) {
                                        $positions[] = 'Berita ' . $i;
                                    }
                                }
                            @endphp
                            @if(count($positions) > 0)
                                @foreach($positions as $pos)
                                    <span class="badge badge-success" style="margin-right: 5px;">{{ $pos }}</span>
                                @endforeach
                            @else
                                <span class="badge badge-secondary">Belum dipublish</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary" onclick="openPublishModal({{ $item->id }})">
                                Publis
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="text-center">No data available</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Gallery Section -->
        <div style="background: white; padding: 20px; border-radius: 10px; margin: 20px 0;">
            <h2>GALERI KAMI</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Title</th>
                        <th>No.Gallery</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($galleries as $gallery)
                    <tr>
                        <td>
                            <img src="{{ asset('storage/' . $gallery->image) }}" 
                                 alt="{{ $gallery->title }}" 
                                 style="width: 60px; height: 40px; object-fit: cover; border-radius: 5px;">
                        </td>
                        <td>{{ $gallery->title }}</td>
                        <td>
                            @php
                                $positions = [];
                                for($i = 1; $i <= 6; $i++) {
                                    $pos = \App\Models\Content::where('key', 'gallery_position_' . $i)->where('content', $gallery->id)->first();
                                    if($pos) {
                                        $positions[] = 'Gallery ' . $i;
                                    }
                                }
                            @endphp
                            @if(count($positions) > 0)
                                @foreach($positions as $pos)
                                    <span class="badge badge-success" style="margin-right: 5px;">{{ $pos }}</span>
                                @endforeach
                            @else
                                <span class="badge badge-secondary">Belum dipublish</span>
                            @endif
                        </td>
                        <td>
                            <button type="button" class="btn btn-sm btn-primary" onclick="openGalleryPublishModal({{ $gallery->id }})">
                                Publis
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="text-center">No gallery data available</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</section>

<!-- Edit Content Modal -->
<div id="editContentModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editContentModal')">&times;</span>
        <div class="modal-left">
            <h2>Edit Hero Content</h2>
            <form id="editContentForm" method="POST" action="{{ route('admin.content.update') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="type" value="hero">
                <div class="form-group">
                    <label>Subtitle</label>
                    <input type="text" name="hero_title" value="{{ $heroTitle }}" required>
                </div>
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="hero_subtitle" value="{{ $heroSubtitle }}" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="hero_description" required>{{ $heroDescription }}</textarea>
                </div>
                <div class="form-group">
                    <label>Hero Image</label>
                    <input type="file" name="hero_image" accept="image/*" id="heroImageInput" onchange="previewHeroImage(this)">
                </div>
                <div class="action-buttons">
                    <button type="button" class="btn-cancel" onclick="closeModal('editContentModal')">Cancel</button>
                    <button type="submit" class="btn-save">Save Changes</button>
                </div>
            </form>
        </div>
        <div class="modal-right">
            <div class="image-section">
                <h3>Hero Image Preview</h3>
                <div class="image-upload-area" id="heroImagePreview" style="background-image: url('{{ $heroImage ? asset('storage/' . $heroImage) : 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}')"></div>
                <p class="upload-text">Current hero image</p>
            </div>
        </div>
    </div>
</div>

<!-- Edit About Modal -->
<div id="editAboutModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editAboutModal')">&times;</span>
        <div class="modal-left">
            <h2>Edit About Section</h2>
            <form method="POST" action="{{ route('admin.content.update') }}">
                @csrf
                <input type="hidden" name="type" value="about">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" name="about_title" value="{{ $aboutTitle }}" required>
                </div>
                <div class="form-group">
                    <label>Description</label>
                    <textarea name="about_description" required>{{ $aboutDescription }}</textarea>
                </div>
                <div class="action-buttons">
                    <button type="button" class="btn-cancel" onclick="closeModal('editAboutModal')">Cancel</button>
                    <button type="submit" class="btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Add/Edit Card Modal -->
<div id="cardModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('cardModal')">&times;</span>
        <div class="modal-left">
            <h2 id="cardModalTitle">Add New Card</h2>
            <form id="cardForm" method="POST" action="{{ route('admin.card.store') }}" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="cardId" name="card_id">
                <input type="hidden" name="_method" id="cardMethod" value="POST">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" id="cardTitle" name="title" required>
                </div>
                <div class="form-group">
                    <label>Content</label>
                    <textarea id="cardContent" name="content" required></textarea>
                </div>
                <div class="form-group">
                    <label>Image</label>
                    <input type="file" name="image" accept="image/*" id="cardImageInput" onchange="previewCardImage(this)">
                </div>
                <div class="action-buttons">
                    <button type="button" class="btn-cancel" onclick="closeModal('cardModal')">Cancel</button>
                    <button type="submit" class="btn-save">Save</button>
                </div>
            </form>
        </div>
        <div class="modal-right">
            <div class="image-section">
                <h3>Card Image</h3>
                <div class="image-upload-area" id="cardImagePreview"></div>
                <p class="upload-text">Current card image</p>
            </div>
        </div>
    </div>
</div>
<!-- Edit Gallery Modal -->
<div id="editGalleryModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('editGalleryModal')">&times;</span>
        <div class="modal-left">
            <h2>Edit Gallery</h2>
            <form id="editGalleryForm" method="POST" enctype="multipart/form-data">
                @csrf
                <input type="hidden" id="galleryId" name="gallery_id">
                <div class="form-group">
                    <label>Title</label>
                    <input type="text" id="galleryTitle" name="title" required>
                </div>
                <div class="form-group">
                    <label>Image</label>
                    <input type="file" name="image" accept="image/*" id="galleryImageInput" onchange="previewGalleryImage(this)">
                </div>
                <div class="action-buttons">
                    <button type="button" class="btn-cancel" onclick="closeModal('editGalleryModal')">Cancel</button>
                    <button type="submit" class="btn-save">Save Changes</button>
                </div>
            </form>
        </div>
        <div class="modal-right">
            <div class="image-section">
                <h3>Gallery Image</h3>
                <div class="image-upload-area" id="galleryImagePreview"></div>
                <p class="upload-text">Current gallery image</p>
            </div>
        </div>
    </div>
</div>

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
                        <label>Title</label>
                        <input type="text" name="title" class="form-control" required>
                    </div>
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

<!-- Modal Publis Gallery ke Index -->
<div id="publishGalleryModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center;">
    <div style="background-color: white; padding: 20px; border-radius: 10px; width: 400px; position: relative;">
        <span onclick="closeGalleryPublishModal()" style="position: absolute; top: 10px; right: 15px; font-size: 28px; cursor: pointer; color: #999;">&times;</span>
        <h3 style="margin-bottom: 20px; color: #333;">Pilih Posisi Publikasi Gallery</h3>
        <form id="publishGalleryForm" method="POST" action="{{ route('admin.gallery.publishToIndex') }}">
            @csrf
            <input type="hidden" id="selectedGalleryId" name="gallery_id">
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; cursor: pointer; padding: 8px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 5px;">
                    <input type="radio" name="position" value="1" style="margin-right: 10px;"> 
                    Gallery 1
                </label>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; cursor: pointer; padding: 8px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 5px;">
                    <input type="radio" name="position" value="2" style="margin-right: 10px;"> 
                    Gallery 2
                </label>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; cursor: pointer; padding: 8px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 5px;">
                    <input type="radio" name="position" value="3" style="margin-right: 10px;"> 
                    Gallery 3
                </label>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; cursor: pointer; padding: 8px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 5px;">
                    <input type="radio" name="position" value="4" style="margin-right: 10px;"> 
                    Gallery 4
                </label>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; cursor: pointer; padding: 8px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 5px;">
                    <input type="radio" name="position" value="5" style="margin-right: 10px;"> 
                    Gallery 5 
                </label>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; cursor: pointer; padding: 8px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 5px;">
                    <input type="radio" name="position" value="6" style="margin-right: 10px;"> 
                    Gallery 6 
                </label>
            </div>
            
            <div style="text-align: right; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 15px;">
                <button type="button" onclick="closeGalleryPublishModal()" style="background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 5px; margin-right: 10px; cursor: pointer;">Batal</button>
                <button type="submit" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Publis</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Publis ke Index -->
<div id="publishModal" style="display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); justify-content: center; align-items: center;">
    <div style="background-color: white; padding: 20px; border-radius: 10px; width: 400px; position: relative;">
        <span onclick="closePublishModal()" style="position: absolute; top: 10px; right: 15px; font-size: 28px; cursor: pointer; color: #999;">&times;</span>
        <h3 style="margin-bottom: 20px; color: #333;">Pilih Posisi Publikasi</h3>
        <form id="publishForm" method="POST" action="/admin/news/publish-to-index">
            @csrf
            <input type="hidden" id="selectedNewsId" name="news_id">
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; cursor: pointer; padding: 8px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 5px;">
                    <input type="radio" name="position" value="1" style="margin-right: 10px;"> 
                    Berita 1
                </label>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; cursor: pointer; padding: 8px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 5px;">
                    <input type="radio" name="position" value="2" style="margin-right: 10px;"> 
                    Berita 2
                </label>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; cursor: pointer; padding: 8px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 5px;">
                    <input type="radio" name="position" value="3" style="margin-right: 10px;"> 
                    Berita 3
                </label>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; cursor: pointer; padding: 8px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 5px;">
                    <input type="radio" name="position" value="4" style="margin-right: 10px;"> 
                    Berita 4
                </label>
            </div>
            <div style="margin-bottom: 15px;">
                <label style="display: block; cursor: pointer; padding: 8px; border: 1px solid #ddd; border-radius: 5px; margin-bottom: 5px;">
                    <input type="radio" name="position" value="5" style="margin-right: 10px;"> 
                    Berita 5 
                </label>
            </div>
            
            <div style="text-align: right; margin-top: 20px; border-top: 1px solid #ddd; padding-top: 15px;">
                <button type="button" onclick="closePublishModal()" style="background: #6c757d; color: white; padding: 10px 20px; border: none; border-radius: 5px; margin-right: 10px; cursor: pointer;">Batal</button>
                <button type="submit" style="background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer;">Publis</button>
            </div>
        </form>
    </div>
</div>

<script>
function openGalleryPublishModal(galleryId) {
    document.getElementById('selectedGalleryId').value = galleryId;
    const modal = document.getElementById('publishGalleryModal');
    modal.style.display = 'flex';
}

function closeGalleryPublishModal() {
    document.getElementById('publishGalleryModal').style.display = 'none';
    document.querySelectorAll('#publishGalleryForm input[type="radio"]').forEach(rb => rb.checked = false);
}

function openPublishModal(newsId) {
    document.getElementById('selectedNewsId').value = newsId;
    const modal = document.getElementById('publishModal');
    modal.style.display = 'flex';
}

function closePublishModal() {
    document.getElementById('publishModal').style.display = 'none';
    document.querySelectorAll('#publishForm input[type="radio"]').forEach(rb => rb.checked = false);
}

function editContent() {
    document.getElementById('editContentModal').style.display = 'block';
}

function editAbout() {
    document.getElementById('editAboutModal').style.display = 'block';
}

function addCard() {
    document.getElementById('cardModalTitle').textContent = 'Add New Card';
    document.getElementById('cardForm').action = '{{ route('admin.card.store') }}';
    document.getElementById('cardMethod').value = 'POST';
    document.getElementById('cardId').value = '';
    document.getElementById('cardTitle').value = '';
    document.getElementById('cardContent').value = '';
    document.getElementById('cardImagePreview').style.backgroundImage = '';
    document.getElementById('cardImageInput').value = '';
    document.getElementById('cardModal').style.display = 'block';
}

function showEditForm(id, title, content, imageUrl) {
    document.getElementById('cardModalTitle').textContent = 'Edit Card';
    document.getElementById('cardForm').action = '/admin/card/' + id + '/update';
    document.getElementById('cardMethod').value = 'POST';
    document.getElementById('cardId').value = id;
    document.getElementById('cardTitle').value = title;
    document.getElementById('cardContent').value = content;
    document.getElementById('cardImagePreview').style.backgroundImage = 'url(' + imageUrl + ')';
    document.getElementById('cardModal').style.display = 'block';
}

function deleteCard(id) {
    if(confirm('Delete this card?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/admin/card/${id}`;
        
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

function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

// Preview image functions
function previewHeroImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('heroImagePreview').style.backgroundImage = `url(${e.target.result})`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function previewCardImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('cardImagePreview').style.backgroundImage = `url(${e.target.result})`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function debugFormSubmit() {
    const title = document.getElementById('cardTitle').value;
    const content = document.getElementById('cardContent').value;
    const cardId = document.getElementById('cardId').value;
    
    if (!title || !content) {
        alert('Title and Content are required!');
        return false;
    }
    
    return true;
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modals = ['editContentModal', 'editAboutModal', 'cardModal', 'publishModal', 'publishGalleryModal', 'editGalleryModal'];
    modals.forEach(modalId => {
        const modal = document.getElementById(modalId);
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    });
}

function editItem(id) {
    alert('Edit news ' + id);
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

function editGallery(id, title) {
    document.getElementById('galleryId').value = id;
    document.getElementById('galleryTitle').value = title;
    document.getElementById('editGalleryForm').action = '/admin/gallery/' + id + '/update';
    
    // Set current image preview
    const currentImage = document.querySelector(`img[alt="${title}"]`);
    if (currentImage) {
        document.getElementById('galleryImagePreview').style.backgroundImage = `url(${currentImage.src})`;
    }
    
    document.getElementById('editGalleryModal').style.display = 'block';
}

function previewGalleryImage(input) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('galleryImagePreview').style.backgroundImage = `url(${e.target.result})`;
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection