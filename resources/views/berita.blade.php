@extends('layouts.main')
@section('content')
<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content">
        <h1 class="hero-title">BERITA KAMI</h1>
    </div>
</section>

<!-- Featured Article Section -->
<section class="featured-section">
    <div class="featured-container">
        <!-- Debug: Tampilkan semua berita -->

        
        @if($featuredNews)
        <div class="featured-image">
            <img src="{{ $featuredNews->image ? asset('storage/' . $featuredNews->image) : 'https://images.unsplash.com/photo-1498837167922-ddd27525d352?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80' }}" alt="{{ $featuredNews->title }}" class="featured-img">
        </div>
        <div class="featured-content">
            <h2 class="featured-title">{{ strtoupper($featuredNews->title) }}</h2>
            <div class="featured-text">
                {!! nl2br(e($featuredNews->excerpt ?? Str::limit($featuredNews->content, 400))) !!}
            </div>
            <a href="javascript:void(0)" onclick="openNewsModal('{{ $featuredNews->slug }}')" class="featured-btn">BACA SELENGKAPNYA</a>
        </div>
        @else
        <div class="featured-image">
            <img src="https://images.unsplash.com/photo-1498837167922-ddd27525d352?ixlib=rb-4.0.3&auto=format&fit=crop&w=600&q=80" alt="Featured Article" class="featured-img">
        </div>
        <div class="featured-content">
            <h2 class="featured-title">BELUM ADA BERITA FEATURED</h2>
            <p class="featured-text">
                Silakan tambahkan berita dan pilih sebagai featured di halaman admin.
            </p>
            <a href="/admin/news" class="featured-btn">KELOLA BERITA</a>
        </div>
        @endif
    </div>
</section>

<!-- News Grid Section -->
<section class="news-grid-section">
    <div class="news-grid-container">
        <h2 class="news-grid-title">BERITA LAINNYA</h2>
        <div class="news-grid-layout">
            @foreach($otherNews as $news)
            <div class="news-item">
                <img src="{{ $news->image ? asset('storage/' . $news->image) : 'https://images.unsplash.com/photo-1498837167922-ddd27525d352?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80' }}" alt="{{ $news->title }}" class="news-item-img">
                <div class="news-item-content">
                    <h3 class="news-item-title">{{ strtoupper(Str::limit($news->title, 30)) }}</h3>
                    <p class="news-item-text">
                        {{ $news->excerpt ?? Str::limit($news->content, 120) }}
                    </p>
                    <div class="news-item-meta">
                        <a href="javascript:void(0)" onclick="openNewsModal('{{ $news->slug }}')" class="news-item-link">Baca selengkapnya</a>
                        <span class="news-item-dots">•••</span>
                    </div>
                </div>
            </div>
            @endforeach
            
            @if($otherNews->count() == 0)
            <div class="news-item" style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                <h3 style="color: #666; margin-bottom: 15px;">BELUM ADA BERITA LAINNYA</h3>
                <p style="color: #999;">Tambahkan berita baru di halaman admin untuk menampilkan konten di sini.</p>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection


<!-- Modal Berita Detail -->
<div id="newsModal" class="news-modal-overlay">
    <div class="news-modal-content">
        <span onclick="closeNewsModal()" class="news-modal-close">&times;</span>
        <img id="modalImage" src="" alt="" class="news-modal-image">
        <div class="news-modal-body">
            <p id="modalDate" class="news-modal-date"></p>
            <h2 id="modalTitle" class="news-modal-title"></h2>
            <div id="modalText" class="news-modal-text"></div>
        </div>
    </div>
</div>

<style>
.news-modal-overlay {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.85);
    overflow-y: auto;
    padding: 40px 20px;
}
.news-modal-overlay.active {
    display: flex;
    align-items: center;
    justify-content: center;
}
.news-modal-content {
    background-color: white;
    max-width: 900px;
    width: 100%;
    border-radius: 20px;
    position: relative;
    box-shadow: 0 10px 50px rgba(0,0,0,0.5);
    overflow: hidden;
    margin: auto;
}
.news-modal-close {
    position: absolute;
    top: 15px;
    right: 25px;
    font-size: 40px;
    font-weight: bold;
    color: #fff;
    cursor: pointer;
    z-index: 10001;
    line-height: 1;
    text-shadow: 0 2px 8px rgba(0,0,0,0.8);
    transition: opacity 0.3s;
}
.news-modal-close:hover {
    opacity: 0.7;
}
.news-modal-image {
    width: 100%;
    height: 280px;
    object-fit: cover;
    display: block;
}
.news-modal-body {
    padding: 50px 60px 60px;
}
.news-modal-date {
    font-size: 14px;
    color: #999;
    margin: 0 0 25px 0;
    font-weight: 500;
}
.news-modal-title {
    font-size: 32px;
    font-weight: bold;
    color: #333;
    margin: 0 0 30px 0;
    line-height: 1.3;
}
.news-modal-text {
    font-size: 16px;
    color: #555;
    line-height: 1.9;
    text-align: justify;
    white-space: pre-wrap;
    word-wrap: break-word;
}
@media (max-width: 768px) {
    .news-modal-overlay {
        padding: 20px 15px;
    }
    .news-modal-content {
        max-width: 100%;
        border-radius: 15px;
    }
    .news-modal-close {
        font-size: 35px;
        top: 10px;
        right: 20px;
    }
    .news-modal-image {
        height: 220px;
    }
    .news-modal-body {
        padding: 30px 25px 40px;
    }
    .news-modal-title {
        font-size: 24px;
        margin-bottom: 20px;
    }
    .news-modal-text {
        font-size: 15px;
        line-height: 1.8;
    }
}
@media (max-width: 576px) {
    .news-modal-overlay {
        padding: 15px 10px;
    }
    .news-modal-content {
        border-radius: 10px;
    }
    .news-modal-close {
        font-size: 30px;
        top: 8px;
        right: 15px;
    }
    .news-modal-image {
        height: 180px;
    }
    .news-modal-body {
        padding: 25px 20px 35px;
    }
    .news-modal-date {
        font-size: 12px;
        margin-bottom: 15px;
    }
    .news-modal-title {
        font-size: 20px;
        margin-bottom: 20px;
    }
    .news-modal-text {
        font-size: 14px;
        line-height: 1.7;
    }
}
</style>

<script>
function openNewsModal(slug) {
    fetch('/api/berita/' + slug)
        .then(response => response.json())
        .then(data => {
            document.getElementById('modalImage').src = data.image ? '/storage/' + data.image : 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80';
            document.getElementById('modalTitle').textContent = data.title;
            document.getElementById('modalText').innerHTML = data.content.replace(/\n/g, '<br>');
            
            const date = new Date(data.published_at || data.created_at);
            const options = { year: 'numeric', month: 'long', day: 'numeric' };
            document.getElementById('modalDate').textContent = date.toLocaleDateString('id-ID', options);
            
            document.getElementById('newsModal').classList.add('active');
            document.body.style.overflow = 'hidden';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Gagal memuat berita');
        });
}

function closeNewsModal() {
    document.getElementById('newsModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

window.onclick = function(event) {
    const modal = document.getElementById('newsModal');
    if (event.target == modal) {
        closeNewsModal();
    }
}
</script>
