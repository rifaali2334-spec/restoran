@extends('admin.layout')

@section('content')
<div class="row">
    <div class="col-sm-12">
        <div class="card">
            <div class="card-block p-0">
                @if(session('success'))
                    <div class="alert alert-success m-3">{{ session('success') }}</div>
                @endif
                
                @if($tentang)
                    <!-- TASTY FOOD Section -->
                    <section style="padding: 60px 20px; background: white; position: relative;">
                        <button class="btn btn-sm btn-primary" style="position: absolute; top: 20px; right: 20px; z-index: 10;" onclick="editSection('tasty_food')">Edit</button>
                        <div style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; gap: 40px;">
                            <div style="flex: 1;">
                                <h2 style="font-size: 2.5rem; font-weight: bold; color: #333; margin-bottom: 20px;">{{ $tentang->judul }}</h2>
                                <div style="font-size: 1rem; line-height: 1.6; color: #666;">
                                    {!! nl2br(e($tentang->konten)) !!}
                                </div>
                            </div>
                            <div style="flex: 1; display: flex; gap: 15px;">
                                @if($tentang->gambar)
                                    <img src="{{ asset('storage/' . $tentang->gambar) }}" alt="{{ $tentang->judul }}" style="width: 48%; height: 250px; object-fit: cover; border-radius: 8px;">
                                @else
                                    <img src="https://images.unsplash.com/photo-1498837167922-ddd27525d352?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Salad Bowl" style="width: 48%; height: 250px; object-fit: cover; border-radius: 8px;">
                                @endif
                                @if($tentang->gambar2)
                                    <img src="{{ asset('storage/' . $tentang->gambar2) }}" alt="Gambar 2" style="width: 48%; height: 250px; object-fit: cover; border-radius: 8px;">
                                @else
                                    <img src="https://images.unsplash.com/photo-1556909114-f6e7ad7d3136?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Chef Cooking" style="width: 48%; height: 250px; object-fit: cover; border-radius: 8px;">
                                @endif
                            </div>
                        </div>
                    </section>

                    <!-- VISI Section -->
                    <section style="padding: 60px 20px; background: #e9ecef; position: relative;">
                        <button class="btn btn-sm btn-primary" style="position: absolute; top: 20px; right: 20px; z-index: 10;" onclick="editSection('visi')">Edit</button>
                        <div style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; gap: 40px;">
                            <div style="flex: 1; display: flex; gap: 15px;">
                                @if($tentang->visi_gambar1)
                                    <img src="{{ asset('storage/' . $tentang->visi_gambar1) }}" alt="Visi Image 1" style="width: 48%; height: 250px; object-fit: cover; border-radius: 8px;">
                                @else
                                    <img src="https://images.unsplash.com/photo-1571997478779-2adcbbe9ab2f?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Breakfast" style="width: 48%; height: 250px; object-fit: cover; border-radius: 8px;">
                                @endif
                                @if($tentang->visi_gambar2)
                                    <img src="{{ asset('storage/' . $tentang->visi_gambar2) }}" alt="Visi Image 2" style="width: 48%; height: 250px; object-fit: cover; border-radius: 8px;">
                                @else
                                    <img src="https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?ixlib=rb-4.0.3&auto=format&fit=crop&w=400&q=80" alt="Ramen Bowl" style="width: 48%; height: 250px; object-fit: cover; border-radius: 8px;">
                                @endif
                            </div>
                            <div style="flex: 1;">
                                <h2 style="font-size: 2.5rem; font-weight: bold; color: #333; margin-bottom: 20px;">VISI</h2>
                                <div style="font-size: 1rem; line-height: 1.6; color: #666;">
                                    {!! nl2br(e($tentang->visi)) !!}
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- MISI Section -->
                    <section style="padding: 60px 20px; background: white; position: relative;">
                        <button class="btn btn-sm btn-primary" style="position: absolute; top: 20px; right: 20px; z-index: 10;" onclick="editSection('misi')">Edit</button>
                        <div style="max-width: 1200px; margin: 0 auto; display: flex; align-items: center; gap: 40px;">
                            <div style="flex: 1;">
                                <h2 style="font-size: 2.5rem; font-weight: bold; color: #333; margin-bottom: 20px;">MISI</h2>
                                <div style="font-size: 1rem; line-height: 1.6; color: #666;">
                                    {!! nl2br(e($tentang->misi)) !!}
                                </div>
                            </div>
                            <div style="flex: 1;">
                                @if($tentang->misi_gambar)
                                    <img src="{{ asset('storage/' . $tentang->misi_gambar) }}" alt="Misi Image" style="width: 100%; height: 300px; object-fit: cover; border-radius: 8px;">
                                @else
                                    <img src="https://images.unsplash.com/photo-1504674900247-0877df9cc836?ixlib=rb-4.0.3&auto=format&fit=crop&w=800&q=80" alt="Fresh Ingredients" style="width: 100%; height: 300px; object-fit: cover; border-radius: 8px;">
                                @endif
                            </div>
                        </div>
                    </section>
                @else
                    <div style="padding: 100px 20px; text-align: center; color: #666;">
                        <h3>Belum ada data halaman tentang</h3>
                        <p>Data akan dibuat otomatis saat halaman tentang diakses pertama kali.</p>
                        <a href="/tentang" target="_blank" class="btn btn-primary">Buat Data Default</a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Modal Edit TASTY FOOD -->
<div class="modal fade" id="editTastyFoodModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Section TASTY FOOD</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.tentang.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Judul</label>
                        <input type="text" name="judul" class="form-control" value="{{ $tentang->judul ?? '' }}" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Konten</label>
                        <textarea name="konten" class="form-control" rows="4" required>{{ $tentang->konten ?? '' }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Gambar 1 (Opsional)</label>
                        <input type="file" name="gambar" class="form-control" accept="image/*">
                        @if($tentang && $tentang->gambar)
                            <small class="text-muted">Gambar saat ini: {{ $tentang->gambar }}</small>
                        @endif
                    </div>
                    
                    <div class="form-group">
                        <label>Gambar 2 (Opsional)</label>
                        <input type="file" name="gambar2" class="form-control" accept="image/*">
                        <small class="text-muted">Gambar kedua untuk section TASTY FOOD</small>
                    </div>
                    
                    <input type="hidden" name="visi" value="{{ $tentang->visi ?? '' }}">
                    <input type="hidden" name="misi" value="{{ $tentang->misi ?? '' }}">
                    <input type="hidden" name="status" value="1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editSection(section) {
    if(section === 'tasty_food') {
        $('#editTastyFoodModal').modal('show');
    } else if(section === 'visi') {
        $('#editVisiModal').modal('show');
    } else if(section === 'misi') {
        $('#editMisiModal').modal('show');
    }
}
</script>

<!-- Modal Edit VISI -->
<div class="modal fade" id="editVisiModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Section VISI</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.tentang.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Konten Visi</label>
                        <textarea name="visi" class="form-control" rows="4" required>{{ $tentang->visi ?? '' }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Gambar Visi 1 (Opsional)</label>
                        <input type="file" name="visi_gambar1" class="form-control" accept="image/*">
                        @if($tentang && $tentang->visi_gambar1)
                            <small class="text-muted">Gambar saat ini: {{ $tentang->visi_gambar1 }}</small>
                        @endif
                    </div>
                    
                    <div class="form-group">
                        <label>Gambar Visi 2 (Opsional)</label>
                        <input type="file" name="visi_gambar2" class="form-control" accept="image/*">
                        @if($tentang && $tentang->visi_gambar2)
                            <small class="text-muted">Gambar saat ini: {{ $tentang->visi_gambar2 }}</small>
                        @endif
                    </div>
                    
                    <input type="hidden" name="judul" value="{{ $tentang->judul ?? '' }}">
                    <input type="hidden" name="konten" value="{{ $tentang->konten ?? '' }}">
                    <input type="hidden" name="misi" value="{{ $tentang->misi ?? '' }}">
                    <input type="hidden" name="status" value="1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit MISI -->
<div class="modal fade" id="editMisiModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Section MISI</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form action="{{ route('admin.tentang.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label>Konten Misi</label>
                        <textarea name="misi" class="form-control" rows="4" required>{{ $tentang->misi ?? '' }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Gambar Misi (Opsional)</label>
                        <input type="file" name="misi_gambar" class="form-control" accept="image/*">
                        @if($tentang && $tentang->misi_gambar)
                            <small class="text-muted">Gambar saat ini: {{ $tentang->misi_gambar }}</small>
                        @endif
                    </div>
                    
                    <input type="hidden" name="judul" value="{{ $tentang->judul ?? '' }}">
                    <input type="hidden" name="konten" value="{{ $tentang->konten ?? '' }}">
                    <input type="hidden" name="visi" value="{{ $tentang->visi ?? '' }}">
                    <input type="hidden" name="status" value="1">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection