@extends('layouts.main')
@section('content')
<style>
body {
    background-color: #f5f5f5 !important;
}

.news-detail-section {
    padding: 80px;
    background-color: #f5f5f5;
    min-height: 60vh;
}

.news-detail-container {
    max-width: 900px;
    margin: 0 auto;
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 10px 40px rgba(0,0,0,0.1);
}

.news-detail-image {
    width: 100%;
    height: 500px;
    object-fit: cover;
}

.news-detail-content {
    padding: 50px 60px;
}

.news-detail-date {
    font-size: 14px;
    color: #999;
    margin-bottom: 25px;
    font-weight: 500;
}

.news-detail-title {
    font-size: 36px;
    font-weight: bold;
    color: #333;
    margin-bottom: 30px;
    line-height: 1.3;
}

.news-detail-text {
    font-size: 16px;
    color: #555;
    line-height: 1.9;
    text-align: justify;
    white-space: pre-wrap;
    word-wrap: break-word;
}

/* Responsive untuk detail berita */
@media (max-width: 768px) {
    .news-detail-section {
        padding: 40px 20px;
    }
    
    .news-detail-container {
        border-radius: 15px;
    }
    
    .news-detail-image {
        height: 300px;
    }
    
    .news-detail-content {
        padding: 30px 25px;
    }
    
    .news-detail-title {
        font-size: 26px;
        margin-bottom: 20px;
    }
    
    .news-detail-text {
        font-size: 15px;
        line-height: 1.8;
    }
    
    .news-grid-section {
        padding: 40px 20px;
    }
    
    .news-grid-title {
        font-size: 24px;
        margin-bottom: 30px;
    }
    
    .news-grid-layout {
        grid-template-columns: 1fr !important;
        gap: 20px;
    }
    
    .news-item-img {
        height: 200px;
    }
}

@media (max-width: 576px) {
    .news-detail-section {
        padding: 30px 15px;
    }
    
    .news-detail-container {
        border-radius: 10px;
    }
    
    .news-detail-image {
        height: 220px;
    }
    
    .news-detail-content {
        padding: 25px 20px;
    }
    
    .news-detail-date {
        font-size: 12px;
        margin-bottom: 15px;
    }
    
    .news-detail-title {
        font-size: 22px;
        margin-bottom: 20px;
    }
    
    .news-detail-text {
        font-size: 14px;
        line-height: 1.7;
    }
    
    .news-grid-section {
        padding: 30px 15px;
    }
    
    .news-grid-title {
        font-size: 20px;
        margin-bottom: 25px;
    }
    
    .news-item-img {
        height: 180px;
    }
    
    .news-item-content {
        padding: 15px;
    }
    
    .news-item-title {
        font-size: 14px;
    }
    
    .news-item-text {
        font-size: 12px;
    }
}
</style>

<!-- Hero Section -->
<section class="hero-section">
    <div class="hero-content">
        <h1 class="hero-title">BERITA</h1>
    </div>
</section>

<!-- News Detail Section -->
<section class="news-detail-section">
    <div class="news-detail-container">
        <img src="{{ $news->image ? asset('storage/' . $news->image) : 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}" 
             alt="{{ $news->title }}" 
             class="news-detail-image">
        
        <div class="news-detail-content">
            <p class="news-detail-date">
                {{ $news->published_at ? $news->published_at->format('d F Y') : $news->created_at->format('d F Y') }}
            </p>
            
            <h2 class="news-detail-title">{{ $news->title }}</h2>
            
            <div class="news-detail-text">
{!! nl2br(e($news->content)) !!}
            </div>
        </div>
    </div>
</section>

<!-- Related News Section -->
@if($relatedNews->count() > 0)
<section class="news-grid-section">
    <div class="news-grid-container">
        <h2 class="news-grid-title">BERITA TERKAIT</h2>
        <div class="news-grid-layout">
            @foreach($relatedNews as $item)
            <div class="news-item">
                <img src="{{ $item->image ? asset('storage/' . $item->image) : 'https://images.unsplash.com/photo-1498837167922-ddd27525d352?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80' }}" 
                     alt="{{ $item->title }}" 
                     class="news-item-img">
                <div class="news-item-content">
                    <h3 class="news-item-title">{{ $item->title }}</h3>
                    <p class="news-item-text">
                        {{ $item->excerpt ?? Str::limit($item->content, 100) }}
                    </p>
                    <div class="news-item-meta">
                        <a href="/berita/{{ $item->slug }}" class="news-item-link">Baca selengkapnya</a>
                        <span class="news-item-dots">•••</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection
