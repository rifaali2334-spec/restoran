  @extends('layouts.main')
  @section('content') 
   <div class="container">
        <div class="left-section">
            <div class="content">
                <div class="subtitle">{{ $heroTitle }}</div>
                <h1 class="title">{{ $heroSubtitle }}</h1>
                <p class="description">
                    {{ $heroDescription }}
                </p>
                <a href="/tentang" class="btn">{{ $heroButtonText }}</a>
            </div>
        </div>
        
        <div class="right-section">
            <img src="{{ $heroImage ? asset('storage/' . $heroImage) : asset('img/hero.png') }}" 
                 alt="Healthy Food Platter" 
                 class="food-image">
        </div>
    </div>

    <section class="about-section">
        <h2 class="about-title">{{ $aboutTitle }}</h2>
        <p class="about-description">
            {{ $aboutDescription }}
        </p>
        <div class="about-line"></div>
    </section>

    <section class="cards-section">
        <div class="cards-container">
            @if($cards->count() > 0)
                @foreach($cards as $card)
                <div class="card">
                    <img src="{{ $card->image ? asset('storage/' . $card->image) : 'https://images.unsplash.com/photo-1512621776951-a57141f2eefd?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80' }}" 
                         alt="{{ $card->title }}" 
                         class="card-image" 
                         style="width: 160px !important; height: 160px !important; min-width: 160px !important; min-height: 160px !important; max-width: 160px !important; max-height: 160px !important; object-fit: cover !important; border-radius: 50% !important;">
                    <div class="card-content">
                        <h3 class="card-title">{{ $card->title }}</h3>
                        <p class="card-text">
                            {{ $card->content }}
                        </p>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </section>

    <section class="news-section">
        <h2 class="news-title">{{ $newsTitle }}</h2>
        <div class="news-grid">
            @if($newsPosition1)
            <div class="news-main">
                <img src="{{ $newsPosition1->image ? asset('storage/' . $newsPosition1->image) : 'https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80' }}" alt="{{ $newsPosition1->title }}" class="news-main-image">
                <div class="news-main-content">
                    <h3 class="news-main-title">{{ $newsPosition1->title }}</h3>
                    <p class="news-main-text">
                        {{ $newsPosition1->excerpt ?? Str::limit($newsPosition1->content, 200) }}
                    </p>
                    <div class="news-meta">
                        <a href="javascript:void(0)" onclick="openNewsModal('{{ $newsPosition1->slug }}')" class="news-link">Baca selengkapnya</a>
                        <span class="news-dots">•••</span>
                    </div>
                </div>
            </div>
            @else
            <div class="news-main">
                <img src="https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Main News" class="news-main-image">
                <div class="news-main-content">
                    <h3 class="news-main-title">LOREM IPSUM DOLOR SIT AMET, CONSECTETUR ADIPISCING ELIT</h3>
                    <p class="news-main-text">
                        Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce scelerisque 
                        magna aliquet cursus tempus. Duis viverra metus et turpis elementum 
                        elementum. Aliquam rutrum placerat tellus et suscipit.
                    </p>
                    <div class="news-meta">
                        <a href="#" class="news-link">Baca selengkapnya</a>
                        <span class="news-dots">•••</span>
                    </div>
                </div>
            </div>
            @endif
            
            <div class="news-sidebar">
                <div class="news-row">
                    <div class="news-small">
                        @if($newsPosition2)
                        <img src="{{ $newsPosition2->image ? asset('storage/' . $newsPosition2->image) : 'https://images.unsplash.com/photo-1498837167922-ddd27525d352?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80' }}" alt="{{ $newsPosition2->title }}" class="news-small-image">
                        <div class="news-small-content">
                            <h4 class="news-small-title">{{ strtoupper(Str::limit($newsPosition2->title, 20)) }}</h4>
                            <p class="news-small-text">
                                {{ $newsPosition2->excerpt ?? Str::limit($newsPosition2->content, 100) }}
                            </p>
                            <div class="news-meta">
                                <a href="javascript:void(0)" onclick="openNewsModal('{{ $newsPosition2->slug }}')" class="news-link">Baca selengkapnya</a>
                                <span class="news-dots">•••</span>
                            </div>
                        </div>
                        @else
                        <img src="https://images.unsplash.com/photo-1498837167922-ddd27525d352?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="News" class="news-small-image">
                        <div class="news-small-content">
                            <h4 class="news-small-title">LOREM IPSUM</h4>
                            <p class="news-small-text">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                                Phasellus ornare, augue eu rutrum commodo.
                            </p>
                            <div class="news-meta">
                                <a href="#" class="news-link">Baca selengkapnya</a>
                                <span class="news-dots">•••</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="news-small">
                        @if($newsPosition3)
                        <img src="{{ $newsPosition3->image ? asset('storage/' . $newsPosition3->image) : 'https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80' }}" alt="{{ $newsPosition3->title }}" class="news-small-image">
                        <div class="news-small-content">
                            <h4 class="news-small-title">{{ strtoupper(Str::limit($newsPosition3->title, 20)) }}</h4>
                            <p class="news-small-text">
                                {{ $newsPosition3->excerpt ?? Str::limit($newsPosition3->content, 100) }}
                            </p>
                            <div class="news-meta">
                                <a href="javascript:void(0)" onclick="openNewsModal('{{ $newsPosition3->slug }}')" class="news-link">Baca selengkapnya</a>
                                <span class="news-dots">•••</span>
                            </div>
                        </div>
                        @else
                        <img src="https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="News" class="news-small-image">
                        <div class="news-small-content">
                            <h4 class="news-small-title">LOREM IPSUM</h4>
                            <p class="news-small-text">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                                Phasellus ornare, augue eu rutrum commodo.
                            </p>
                            <div class="news-meta">
                                <a href="#" class="news-link">Baca selengkapnya</a>
                                <span class="news-dots">•••</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
                
                <div class="news-row">
                    <div class="news-small">
                        @if($newsPosition4)
                        <img src="{{ $newsPosition4->image ? asset('storage/' . $newsPosition4->image) : 'https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80' }}" alt="{{ $newsPosition4->title }}" class="news-small-image">
                        <div class="news-small-content">
                            <h4 class="news-small-title">{{ strtoupper(Str::limit($newsPosition4->title, 20)) }}</h4>
                            <p class="news-small-text">
                                {{ $newsPosition4->excerpt ?? Str::limit($newsPosition4->content, 100) }}
                            </p>
                            <div class="news-meta">
                                <a href="javascript:void(0)" onclick="openNewsModal('{{ $newsPosition4->slug }}')" class="news-link">Baca selengkapnya</a>
                                <span class="news-dots">•••</span>
                            </div>
                        </div>
                        @else
                        <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="News" class="news-small-image">
                        <div class="news-small-content">
                            <h4 class="news-small-title">LOREM IPSUM</h4>
                            <p class="news-small-text">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                                Phasellus ornare, augue eu rutrum commodo.
                            </p>
                            <div class="news-meta">
                                <a href="#" class="news-link">Baca selengkapnya</a>
                                <span class="news-dots">•••</span>
                            </div>
                        </div>
                        @endif
                    </div>
                    
                    <div class="news-small">
                        @if($newsPosition5)
                        <img src="{{ $newsPosition5->image ? asset('storage/' . $newsPosition5->image) : 'https://images.unsplash.com/photo-1565958011703-44f9829ba187?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80' }}" alt="{{ $newsPosition5->title }}" class="news-small-image">
                        <div class="news-small-content">
                            <h4 class="news-small-title">{{ strtoupper(Str::limit($newsPosition5->title, 20)) }}</h4>
                            <p class="news-small-text">
                                {{ $newsPosition5->excerpt ?? Str::limit($newsPosition5->content, 100) }}
                            </p>
                            <div class="news-meta">
                                <a href="javascript:void(0)" onclick="openNewsModal('{{ $newsPosition5->slug }}')" class="news-link">Baca selengkapnya</a>
                                <span class="news-dots">•••</span>
                            </div>
                        </div>
                        @else
                        <img src="https://images.unsplash.com/photo-1565958011703-44f9829ba187?ixlib=rb-4.0.3&auto=format&fit=crop&w=300&q=80" alt="News" class="news-small-image">
                        <div class="news-small-content">
                            <h4 class="news-small-title">LOREM IPSUM</h4>
                            <p class="news-small-text">
                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. 
                                Phasellus ornare, augue eu rutrum commodo.
                            </p>
                            <div class="news-meta">
                                <a href="#" class="news-link">Baca selengkapnya</a>
                                <span class="news-dots">•••</span>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="gallery-section">
        <h2 class="gallery-title">GALERI KAMI</h2>
        <div class="gallery-grid">
            @if($galleryPosition1)
            <div class="gallery-item">
                <img src="{{ $galleryPosition1->image_url }}" alt="{{ $galleryPosition1->title }}" class="gallery-image">
            </div>
            @endif
            @if($galleryPosition2)
            <div class="gallery-item">
                <img src="{{ $galleryPosition2->image_url }}" alt="{{ $galleryPosition2->title }}" class="gallery-image">
            </div>
            @endif
            @if($galleryPosition3)
            <div class="gallery-item">
                <img src="{{ $galleryPosition3->image_url }}" alt="{{ $galleryPosition3->title }}" class="gallery-image">
            </div>
            @endif
            @if($galleryPosition4)
            <div class="gallery-item">
                <img src="{{ $galleryPosition4->image_url }}" alt="{{ $galleryPosition4->title }}" class="gallery-image">
            </div>
            @endif
            @if($galleryPosition5)
            <div class="gallery-item">
                <img src="{{ $galleryPosition5->image_url }}" alt="{{ $galleryPosition5->title }}" class="gallery-image">
            </div>
            @endif
            @if($galleryPosition6)
            <div class="gallery-item">
                <img src="{{ $galleryPosition6->image_url }}" alt="{{ $galleryPosition6->title }}" class="gallery-image">
            </div>
            @endif
        </div>
        <a href="/galeri" class="gallery-btn">LIHAT LEBIH BANYAK</a>
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
