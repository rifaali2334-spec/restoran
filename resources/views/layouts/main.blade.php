<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasty Food</title>
    <link rel="stylesheet" href="{{ asset('css/main.css') }}">
    <link rel="stylesheet" href="{{ asset('css/mobile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/responsive-fix.css') }}">
</head>
<body>
    @if(request()->is('/'))
        @include('partials.navbar-home')
    @else
        @include('partials.navbar-other')
    @endif

    @yield('content')

    <footer class="footer">
        <div class="footer-content">
            <div class="footer-brand">
                <h3>{{ $footer->company_name ?? 'Tasty Food' }}</h3>
                <p>{{ $footer->company_description ?? 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.' }}</p>
                <div class="social-icons">
                    <a href="{{ $footer->social_facebook ?? '#' }}" class="social-icon facebook">f</a>
                    <a href="{{ $footer->social_twitter ?? '#' }}" class="social-icon twitter">t</a>
                </div>
            </div>
            
            <div class="footer-column">
                <h4>Useful links</h4>
                <ul>
                    <li><a href="/berita">Berita</a></li>
                    <li><a href="/galeri">Galeri</a></li>
                    <li><a href="/tentang">Tentang</a></li>
                    <li><a href="/kontak">Kontak</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h4>Privacy</h4>
                <ul>
                    <li><a href="/tentang">Tentang Kami</a></li>
                    <li><a href="/kontak">Kontak Kami</a></li>
                    <li><a href="#">Privacy Policy</a></li>
                    <li><a href="#">Terms of Service</a></li>
                </ul>
            </div>
            
            <div class="footer-column">
                <h4>Contact Info</h4>
                <div class="contact-info">
                    <div>✉ {{ $footer->contact_email ?? 'tastyfood@gmail.com' }}</div>
                    <div>📞 {{ $footer->contact_phone ?? '+62 812 3456 7890' }}</div>
                    <div>📍 {{ $footer->contact_address ?? 'Kota Bandung, Jawa Barat' }}</div>
                </div>
            </div>
        </div>
        
        <div class="footer-bottom">
            <p>Copyright ©{{ date('Y') }} All rights reserved</p>
        </div>
    </footer>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Tasty Food website loaded');
        });
        
        function toggleMobileMenu() {
            const nav = document.getElementById('mobileNav');
            const toggle = document.querySelector('.mobile-menu-toggle');
            nav.classList.toggle('active');
            toggle.classList.toggle('active');
        }
        
        // Close mobile menu when clicking outside
        document.addEventListener('click', function(event) {
            const nav = document.getElementById('mobileNav');
            const toggle = document.querySelector('.mobile-menu-toggle');
            
            if (!nav.contains(event.target) && !toggle.contains(event.target)) {
                nav.classList.remove('active');
                toggle.classList.remove('active');
            }
        });
        
        // Close mobile menu when clicking on a link
        document.querySelectorAll('.nav a').forEach(link => {
            link.addEventListener('click', function() {
                const nav = document.getElementById('mobileNav');
                const toggle = document.querySelector('.mobile-menu-toggle');
                nav.classList.remove('active');
                toggle.classList.remove('active');
            });
        });
    </script>
    @yield('scripts')
</body>
</html>