<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Content;
use App\Models\FooterSetting;
use App\Models\News;
use App\Models\Gallery;

class HomeController extends Controller
{
    public function index()
    {
        // Ambil konten dari tabel contents
        $heroTitle = Content::getValue('hero_title', 'title', 'HEALTHY');
        $heroSubtitle = Content::getValue('hero_subtitle', 'title', 'TASTY FOOD');
        $heroDescription = Content::getValue('hero_description', 'content', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ornare, augue eu rutrum commodo, dui diam convallis arcu, eget consectetur ex sem eget lacus.');
        $heroButtonText = Content::getValue('hero_button_text', 'title', 'TENTANG KAMI');
        $heroImage = Content::getValue('hero_image', 'image');
        
        $aboutTitle = Content::getValue('about_home_title', 'title', 'TENTANG KAMI');
        $aboutDescription = Content::getValue('about_home_description', 'content', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ornare, augue eu rutrum commodo, dui diam convallis arcu, eget consectetur ex sem eget lacus.');
        
        // Ambil data cards
        $cards = Content::where('key', 'like', 'card_%')->where('is_active', true)->get();
        
        $newsTitle = Content::getValue('news_home_title', 'title', 'BERITA KAMI');
        $galleryTitle = Content::getValue('gallery_home_title', 'title', 'GALERI KAMI');
        
        // Ambil footer settings
        $footer = FooterSetting::first();
        if (!$footer) {
            $footer = FooterSetting::create([
                'company_name' => 'Tasty Food',
                'company_description' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.',
                'contact_email' => 'tastyfood@gmail.com',
                'contact_phone' => '+62 812 3456 7890',
                'contact_address' => 'Kota Bandung, Jawa Barat',
                'social_facebook' => '#',
                'social_twitter' => '#',
                'social_instagram' => '#',
            ]);
        }
        
        // Ambil berita berdasarkan posisi
        $newsPosition1 = null;
        $newsPosition2 = null;
        $newsPosition3 = null;
        $newsPosition4 = null;
        $newsPosition5 = null;
        
        // Ambil ID berita dari setiap posisi
        $pos1 = Content::where('key', 'news_position_1')->first();
        if ($pos1) {
            $newsPosition1 = News::find($pos1->content);
        }
        
        $pos2 = Content::where('key', 'news_position_2')->first();
        if ($pos2) {
            $newsPosition2 = News::find($pos2->content);
        }
        
        $pos3 = Content::where('key', 'news_position_3')->first();
        if ($pos3) {
            $newsPosition3 = News::find($pos3->content);
        }
        
        $pos4 = Content::where('key', 'news_position_4')->first();
        if ($pos4) {
            $newsPosition4 = News::find($pos4->content);
        }
        
        $pos5 = Content::where('key', 'news_position_5')->first();
        if ($pos5) {
            $newsPosition5 = News::find($pos5->content);
        }
        
        // Ambil galeri berdasarkan posisi (6 posisi)
        $galleryPosition1 = null;
        $galleryPosition2 = null;
        $galleryPosition3 = null;
        $galleryPosition4 = null;
        $galleryPosition5 = null;
        $galleryPosition6 = null;
        
        // Ambil ID gallery dari setiap posisi
        $gPos1 = Content::where('key', 'gallery_position_1')->first();
        if ($gPos1) {
            $galleryPosition1 = Gallery::find($gPos1->content);
        }
        
        $gPos2 = Content::where('key', 'gallery_position_2')->first();
        if ($gPos2) {
            $galleryPosition2 = Gallery::find($gPos2->content);
        }
        
        $gPos3 = Content::where('key', 'gallery_position_3')->first();
        if ($gPos3) {
            $galleryPosition3 = Gallery::find($gPos3->content);
        }
        
        $gPos4 = Content::where('key', 'gallery_position_4')->first();
        if ($gPos4) {
            $galleryPosition4 = Gallery::find($gPos4->content);
        }
        
        $gPos5 = Content::where('key', 'gallery_position_5')->first();
        if ($gPos5) {
            $galleryPosition5 = Gallery::find($gPos5->content);
        }
        
        $gPos6 = Content::where('key', 'gallery_position_6')->first();
        if ($gPos6) {
            $galleryPosition6 = Gallery::find($gPos6->content);
        }
        
        // Buat data default jika belum ada
        $this->createDefaultContent();
        
        return view('index', compact(
            'heroTitle', 'heroSubtitle', 'heroDescription', 'heroButtonText', 'heroImage',
            'aboutTitle', 'aboutDescription', 'cards', 'newsTitle', 'galleryTitle',
            'newsPosition1', 'newsPosition2', 'newsPosition3', 'newsPosition4', 'newsPosition5',
            'galleryPosition1', 'galleryPosition2', 'galleryPosition3', 'galleryPosition4', 'galleryPosition5', 'galleryPosition6',
            'footer'
        ));
    }
    
    private function createDefaultContent()
    {
        // Buat konten default jika belum ada
        $defaultContents = [
            ['key' => 'hero_title', 'title' => 'HEALTHY'],
            ['key' => 'hero_subtitle', 'title' => 'TASTY FOOD'],
            ['key' => 'hero_description', 'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ornare, augue eu rutrum commodo, dui diam convallis arcu, eget consectetur ex sem eget lacus.'],
            ['key' => 'hero_button_text', 'title' => 'TENTANG KAMI'],
            ['key' => 'hero_image', 'title' => 'Hero Image', 'content' => 'Hero section image'],
            ['key' => 'about_home_title', 'title' => 'TENTANG KAMI'],
            ['key' => 'about_home_description', 'content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Phasellus ornare, augue eu rutrum commodo, dui diam convallis arcu, eget consectetur ex sem eget lacus.'],
            ['key' => 'news_home_title', 'title' => 'BERITA KAMI'],
            ['key' => 'gallery_home_title', 'title' => 'GALERI KAMI'],
        ];
        
        foreach ($defaultContents as $content) {
            Content::firstOrCreate(
                ['key' => $content['key']],
                $content
            );
        }
        

    }

    // Admin CRUD Methods (tetap sama seperti sebelumnya)
    public function adminIndex()
    {
        $contents = Content::all();
        return view('admin.home.index', compact('contents'));
    }

    public function create()
    {
        return view('admin.home.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:100|unique:contents',
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->all();
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('contents', 'public');
        }

        Content::create($data);
        
        return redirect()->route('admin.home.index')->with('success', 'Konten berhasil ditambahkan!');
    }

    public function show($id)
    {
        $content = Content::findOrFail($id);
        return view('admin.home.show', compact('content'));
    }

    public function edit($id)
    {
        $content = Content::findOrFail($id);
        return view('admin.home.edit', compact('content'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'key' => 'required|string|max:100|unique:contents,key,' . $id,
            'title' => 'nullable|string|max:255',
            'content' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $content = Content::findOrFail($id);
        $data = $request->all();
        
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('contents', 'public');
        }

        $content->update($data);
        
        return redirect()->route('admin.home.index')->with('success', 'Konten berhasil diupdate!');
    }

    public function destroy($id)
    {
        $content = Content::findOrFail($id);
        $content->delete();
        
        return redirect()->route('admin.home.index')->with('success', 'Konten berhasil dihapus!');
    }
    
    public function berita()
    {
        // Debug: ambil semua berita dulu
        $allNews = News::all();
        
        $featuredNews = News::where('is_featured', true)->first();
        $otherNews = News::where('is_featured', false)->get();
        
        return view('berita', compact('featuredNews', 'otherNews', 'allNews'));
    }
    
    public function getBeritaDetail($slug)
    {
        $news = News::where('slug', $slug)->firstOrFail();
        return response()->json($news);
    }
    
    public function beritaDetail($slug)
    {
        $news = News::where('slug', $slug)->firstOrFail();
        $relatedNews = News::where('id', '!=', $news->id)
                           ->where('is_published', true)
                           ->limit(4)
                           ->get();
        
        return view('berita-detail', compact('news', 'relatedNews'));
    }
    
    public function galeri()
    {
        // Ambil semua gallery untuk halaman galeri
        $allGalleries = Gallery::all();
        
        // Ambil gallery yang published untuk carousel
        $publishedGalleries = Gallery::where('is_published', true)->get();
        
        return view('galeri', compact('allGalleries', 'publishedGalleries'));
    }
}