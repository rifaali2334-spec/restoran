@extends('layouts.main')
@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">GALERI KAMI</h1>
        </div>
    </section>

    <!-- Carousel Section -->
    <section class="carousel-section">
        <div class="carousel-container">
            <div class="carousel-wrapper">
                <button class="carousel-btn prev-btn">‹</button>
                @foreach($publishedGalleries->take(5) as $index => $gallery)
                <div class="carousel-slide {{ $index == 0 ? 'active' : '' }}">
                    <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}" class="carousel-image">
                </div>
                @endforeach
                <button class="carousel-btn next-btn">›</button>
            </div>
        </div>
    </section>

    <!-- Gallery Content Section -->
    <section class="gallery-content-section">
        <div class="gallery-container">
            <div class="gallery-grid">
                @foreach($allGalleries as $index => $gallery)
                <div class="gallery-item {{ $index >= 8 ? 'hidden' : '' }}">
                    <img src="{{ $gallery->image_url }}" alt="{{ $gallery->title }}" class="gallery-image">
                </div>
                @endforeach
            </div>
            @if($allGalleries->count() > 8)
            <div class="text-center mt-4">
                <button id="loadMoreBtn" class="btn-load-more">Lihat Lebih Banyak</button>
            </div>
            @endif
        </div>
    </section>
@endsection

@section('scripts')
<script>
    let currentSlide = 0;
    const slides = document.querySelectorAll('.carousel-slide');
    const totalSlides = slides.length;
    let isTransitioning = false;

    function showSlide(index) {
        if (isTransitioning) return;
        isTransitioning = true;
        
        slides.forEach(slide => slide.classList.remove('active'));
        slides[index].classList.add('active');
        
        setTimeout(() => {
            isTransitioning = false;
        }, 600);
    }

    function nextSlide() {
        if (isTransitioning) return;
        currentSlide = (currentSlide + 1) % totalSlides;
        showSlide(currentSlide);
    }

    function prevSlide() {
        if (isTransitioning) return;
        currentSlide = (currentSlide - 1 + totalSlides) % totalSlides;
        showSlide(currentSlide);
    }

    document.querySelector('.next-btn').addEventListener('click', nextSlide);
    document.querySelector('.prev-btn').addEventListener('click', prevSlide);

    // Auto-play carousel
    setInterval(nextSlide, 5000);

    // Load more functionality
    const loadMoreBtn = document.getElementById('loadMoreBtn');
    if (loadMoreBtn) {
        let isExpanded = false;
        
        loadMoreBtn.addEventListener('click', function() {
            const allItems = document.querySelectorAll('.gallery-item');
            
            if (!isExpanded) {
                // Tampilkan semua foto
                allItems.forEach(item => item.classList.remove('hidden'));
                loadMoreBtn.textContent = 'Lihat Lebih Sedikit';
                isExpanded = true;
            } else {
                // Sembunyikan foto setelah index 7 (foto ke-9 dst)
                allItems.forEach((item, index) => {
                    if (index >= 8) {
                        item.classList.add('hidden');
                    }
                });
                loadMoreBtn.textContent = 'Lihat Lebih Banyak';
                isExpanded = false;
            }
        });
    }
</script>
@endsection