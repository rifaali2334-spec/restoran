@extends('layouts.main')
@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">TENTANG KAMI</h1>
        </div>
    </section>

    <!-- TASTY FOOD Section -->
    <section class="tasty-food-section">
        <div class="tasty-food-container">
            <div class="tasty-food-content">
                <h2 class="tasty-food-title">{{ $tentang->judul }}</h2>
                <div class="tasty-food-text">
                    {!! nl2br(e($tentang->konten)) !!}
                </div>
            </div>
            <div class="tasty-food-images">
                @if($tentang->gambar)
                    <img src="{{ asset('storage/' . $tentang->gambar) }}" alt="{{ $tentang->judul }}" class="tasty-food-img">
                @else
                    <img src="https://images.unsplash.com/photo-1498837167922-ddd27525d352?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Salad Bowl" class="tasty-food-img">
                @endif
                @if($tentang->gambar2)
                    <img src="{{ asset('storage/' . $tentang->gambar2) }}" alt="Gambar 2" class="tasty-food-img">
                @else
                    <img src="https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Chef Cooking" class="tasty-food-img">
                @endif
            </div>
        </div>
    </section>

    <!-- VISI Section -->
    <section class="visi-section">
        <div class="visi-container">
            <div class="visi-images">
                @if($tentang->visi_gambar1)
                    <img src="{{ asset('storage/' . $tentang->visi_gambar1) }}" alt="Visi Image 1" class="visi-img">
                @else
                    <img src="https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Breakfast" class="visi-img">
                @endif
                @if($tentang->visi_gambar2)
                    <img src="{{ asset('storage/' . $tentang->visi_gambar2) }}" alt="Visi Image 2" class="visi-img">
                @else
                    <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Ramen Bowl" class="visi-img">
                @endif
            </div>
            <div class="visi-content">
                <h2 class="visi-title">VISI</h2>
                <div class="visi-text">
                    {{ $tentang->visi }}
                </div>
            </div>
        </div>
    </section>

    <!-- MISI Section -->
    <section class="misi-section">
        <div class="misi-container">
            <div class="misi-content">
                <h2 class="misi-title">MISI</h2>
                <div class="misi-text">
                    {{ $tentang->misi }}
                </div>
            </div>
            <div class="misi-image">
                @if($tentang->misi_gambar)
                    <img src="{{ asset('storage/' . $tentang->misi_gambar) }}" alt="Misi Image" class="misi-img">
                @else
                    <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Fresh Ingredients" class="misi-img">
                @endif
            </div>
        </div>
    </section>
@endsection