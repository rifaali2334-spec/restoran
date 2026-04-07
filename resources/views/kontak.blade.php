@extends('layouts.main')
@section('content')
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-content">
            <h1 class="hero-title">KONTAK KAMI</h1>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section class="contact-form-section">
        <div class="contact-form-container">
            <h2 class="contact-form-title">KONTAK KAMI</h2>
            
            @if(session('success'))
                <div style="background: #d4edda; color: #155724; padding: 15px; border-radius: 5px; margin-bottom: 20px; border: 1px solid #c3e6cb;">
                    {{ session('success') }}
                </div>
            @endif
            
            <form action="/contact" method="POST">
                @csrf
                <div class="contact-form">
                    <div class="form-left">
                        <input type="text" name="subject" placeholder="Subject" class="form-input" required>
                        <input type="text" name="name" placeholder="Name" class="form-input" required>
                        <input type="email" name="email" placeholder="Email" class="form-input" required>
                    </div>
                    <textarea name="message" placeholder="Message" class="form-textarea" required></textarea>
                </div>
                <button type="submit" class="form-submit">KIRIM</button>
            </form>
        </div>
    </section>

    <!-- Contact Info Section -->
    <section class="contact-info-section">
        <div class="contact-info-container">
            <div class="contact-info-item">
                <div class="contact-icon">
                    <span>✉</span>
                </div>
                <h3>EMAIL</h3>
                <p>tastyfood@gmail.com</p>
            </div>
            <div class="contact-info-item">
                <div class="contact-icon">
                    <span>📞</span>
                </div>
                <h3>PHONE</h3>
                <p>+62 812 3456 7890</p>
            </div>
            <div class="contact-info-item">
                <div class="contact-icon">
                    <span>📍</span>
                </div>
                <h3>LOCATION</h3>
                <p>Kota Bandung, Jawa Barat</p>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section class="map-section">
        <div class="map-container">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d126748.56211042117!2d107.57311709726562!3d-6.903444399999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e68e6398252477f%3A0x146a1f93d3e815b2!2sBandung%2C%20Bandung%20City%2C%20West%20Java!5e0!3m2!1sen!2sid!4v1635123456789!5m2!1sen!2sid" 
                width="100%" 
                height="500" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy">
            </iframe>
        </div>
    </section>
@endsection