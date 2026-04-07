@extends('admin.layout')

@section('content')
<style>
@media (max-width: 768px) {
    .card-block { padding: 50px 15px !important; }
    .card-block h1 { font-size: 2rem !important; }
    .card-block p { font-size: 1rem !important; }
}

@media (max-width: 480px) {
    .card-block { padding: 30px 10px !important; }
    .card-block h1 { font-size: 1.5rem !important; }
    .card-block p { font-size: 0.9rem !important; }
}
</style>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-block text-center" style="padding: 100px 20px;">
                <h1 style="font-size: 3rem; color: #333; margin-bottom: 20px;">Selamat Datang Admin</h1>
                <p style="font-size: 1.2rem; color: #666;">Kelola website Anda dengan mudah melalui panel admin ini</p>
            </div>
        </div>
    </div>
</div>
@endsection