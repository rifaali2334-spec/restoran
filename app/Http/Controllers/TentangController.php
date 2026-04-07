<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tentang;

class TentangController extends Controller
{
    public function index()
    {
        $tentang = Tentang::where('status', true)->first();
        
        // Jika belum ada data, buat data default
        if (!$tentang) {
            $tentang = Tentang::create([
                'judul' => 'TASTY FOOD',
                'konten' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ornare, augue eu rutrum commodo, dui diam convallis arcu, eget consectetur ex sem eget lacus. Nullam vitae dignissim neque, vel luctus ex. Fusce sit amet viverra ante.',
                'visi' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce scelerisque magna aliquet cursus tempus. Duis viverra metus et turpis elementum elementum. Aliquam rutrum placerat tellus et suscipit.',
                'misi' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Fusce scelerisque magna aliquet cursus tempus. Duis viverra metus et turpis elementum elementum. Aliquam rutrum placerat tellus et suscipit.',
                'status' => true
            ]);
        }
        
        return view('tentang', compact('tentang'));
    }
}