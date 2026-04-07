<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Models\Content;
use App\Models\Setting;
use App\Models\News;
use App\Models\Gallery;
use App\Models\ContactMessage;
use App\Models\Tentang;
use App\Models\FooterSetting;

class AdminController extends Controller
{
    public function login()
    {
        return view('admin.login');
    }
    
    public function loginPost(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        
        // Debug: cek semua admin
        $allAdmins = Admin::all();
        \Log::info('All Admins:', $allAdmins->toArray());
        
        $admin = Admin::where('email', $request->email)
                     ->where('is_active', true)
                     ->first();
        
        \Log::info('Login attempt:', [
            'email' => $request->email,
            'password' => $request->password,
            'admin_found' => $admin ? 'yes' : 'no'
        ]);
        
        // Debug: cek apakah admin ditemukan
        if (!$admin) {
            return redirect()->route('admin.login')->with('error', 'Email tidak ditemukan atau akun tidak aktif');
        }
        
        \Log::info('Admin data:', $admin->toArray());
        
        // Debug: cek password
        $passwordCheck = Hash::check($request->password, $admin->password);
        \Log::info('Password check:', ['result' => $passwordCheck]);
        
        if (!$passwordCheck) {
            return redirect()->route('admin.login')->with('error', 'Password salah');
        }
        
        session(['admin_id' => $admin->id, 'admin_name' => $admin->name]);
        return redirect()->route('admin.index');
    }
    
    public function hashPassword()
    {
        $admin = Admin::find(2); // ID admin yang mau di-hash
        $admin->password = 'admin123'; // Password asli
        $admin->save(); // Akan di-hash otomatis oleh model
        
        return 'Password berhasil di-hash!';
    }
    
    public function logout()
    {
        session()->forget(['admin_id', 'admin_name']);
        return redirect()->route('admin.login');
    }
    
    public function index()
    {
        try {
            $users = User::all();
        } catch (\Exception $e) {
            $users = collect();
        }
        
        try {
            $contents = Content::all();
        } catch (\Exception $e) {
            $contents = collect();
        }
        
        try {
            $settings = Setting::all();
        } catch (\Exception $e) {
            $settings = collect();
        }
        
        try {
            $news = News::all();
        } catch (\Exception $e) {
            $news = collect();
        }
        
        try {
            $galleries = Gallery::all();
        } catch (\Exception $e) {
            $galleries = collect();
        }
        
        try {
            $contacts = ContactMessage::all();
        } catch (\Exception $e) {
            $contacts = collect();
        }
        
        return view('admin.index', compact('users', 'contents', 'settings', 'news', 'galleries', 'contacts'));
    }

    public function users()
    {
        $users = User::all();
        return view('admin.users', compact('users'));
    }

    public function home()
    {
        return view('admin.home');
    }

    public function contents()
    {
        $contents = Content::all();
        
        // Ambil data hero untuk ditampilkan
        $heroTitle = Content::getValue('hero_title', 'title', 'HEALTHY');
        $heroSubtitle = Content::getValue('hero_subtitle', 'title', 'TASTY FOOD');
        $heroDescription = Content::getValue('hero_description', 'content', 'Healthy Tasty Food merupakan salah satu restoran yang menyajikan makanan vegan');
        $heroImage = Content::getValue('hero_image', 'image');
        
        // Ambil data about untuk ditampilkan
        $aboutTitle = Content::getValue('about_home_title', 'title', 'TENTANG KAMI');
        $aboutDescription = Content::getValue('about_home_description', 'content', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit.');
        
        // Ambil data cards untuk ditampilkan
        $cards = Content::where('key', 'like', 'card_%')->where('is_active', true)->get();
        
        // Ambil data news title
        $newsTitle = Content::getValue('news_title', 'title', 'BERITA KAMI');
        
        // Ambil berita featured dan berita lainnya
        $featuredNews = \App\Models\News::where('is_featured', true)->where('is_published', true)->first();
        $otherNews = \App\Models\News::where('is_featured', false)->where('is_published', true)->limit(4)->get();
        $allNews = \App\Models\News::where('is_published', true)->get();
        
        // Ambil data gallery untuk ditampilkan
        $galleries = Gallery::all();
        
        // Ambil gallery yang published untuk carousel
        $publishedGalleries = Gallery::where('is_published', true)->get();
        
        // Ambil gallery yang dipublish ke index
        $galleryPositions = [];
        for ($i = 1; $i <= 6; $i++) {
            $position = Content::where('key', 'gallery_position_' . $i)->first();
            if ($position) {
                $gallery = Gallery::find($position->content);
                if ($gallery) {
                    $galleryPositions[$i] = $gallery;
                }
            }
        }
        
        return view('admin.contents', compact('contents', 'heroTitle', 'heroSubtitle', 'heroDescription', 'heroImage', 'aboutTitle', 'aboutDescription', 'cards', 'newsTitle', 'featuredNews', 'otherNews', 'allNews', 'galleries', 'publishedGalleries', 'galleryPositions'));
    }

    public function settings()
    {
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
        
        return view('admin.settings', compact('footer'));
    }
    
    public function updateFooter(Request $request)
    {
        $request->validate([
            'company_name' => 'required|string|max:255',
            'company_description' => 'required|string',
            'contact_email' => 'required|email',
            'contact_phone' => 'required|string',
            'contact_address' => 'required|string',
            'social_facebook' => 'nullable|string',
            'social_twitter' => 'nullable|string',
            'social_instagram' => 'nullable|string',
        ]);
        
        $footer = FooterSetting::first();
        
        if ($footer) {
            $footer->update($request->all());
        } else {
            FooterSetting::create($request->all());
        }
        
        return redirect()->route('admin.settings')->with('success', 'Footer settings updated successfully!');
    }

    public function news()
    {
        $news = News::all();
        return view('admin.news', compact('news'));
    }

    public function galleries()
    {
        $galleries = Gallery::all();
        return view('admin.galleries', compact('galleries'));
    }

    public function contacts()
    {
        $contacts = ContactMessage::orderBy('created_at', 'desc')->get();
        return view('admin.contacts', compact('contacts'));
    }

    public function showContact($id)
    {
        $contact = ContactMessage::findOrFail($id);
        $contact->update(['is_read' => true]);
        return response()->json($contact);
    }

    public function updateContact(Request $request, $id)
    {
        $contact = ContactMessage::findOrFail($id);
        
        $contact->update([
            'is_read' => $request->is_read,
            'replied_at' => $request->reply ? now() : null
        ]);
        
        return response()->json(['success' => true]);
    }

    public function deleteContact($id)
    {
        ContactMessage::findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function storeContent(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'is_active' => 'boolean'
        ]);

        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('contents', 'public');
        }

        Content::create([
            'key' => $request->key,
            'title' => $request->title,
            'content' => $request->content,
            'image' => $imagePath,
            'is_active' => $request->has('is_active')
        ]);

        return redirect()->route('admin.contents')->with('success', 'Content created successfully');
    }

    public function updateContent(Request $request, $id)
    {
        \Log::info('Update Content Called', ['id' => $id, 'data' => $request->all()]);
        
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            ]);

            $content = Content::findOrFail($id);
            \Log::info('Content Found', ['content' => $content->toArray()]);
            
            $updateData = [
                'title' => $request->title,
                'content' => $request->content,
                'is_active' => $request->has('is_active') ? 1 : 0
            ];
            
            if ($request->hasFile('image')) {
                $updateData['image'] = $request->file('image')->store('contents', 'public');
                \Log::info('Image uploaded', ['path' => $updateData['image']]);
            }

            \Log::info('Update Data', ['data' => $updateData]);
            $result = $content->update($updateData);
            \Log::info('Update Result', ['result' => $result]);

            return redirect()->route('admin.contents')->with('success', 'Content updated successfully');
        } catch (\Exception $e) {
            \Log::error('Update Content Error', ['error' => $e->getMessage()]);
            return redirect()->route('admin.contents')->with('error', 'Error updating content: ' . $e->getMessage());
        }
    }

    public function deleteContent($id)
    {
        Content::findOrFail($id)->delete();
        return redirect()->route('admin.contents')->with('success', 'Content deleted successfully');
    }

    public function showContent($id)
    {
        $content = Content::findOrFail($id);
        return response()->json($content);
    }

    public function updateHeroImage(Request $request)
    {
        try {
            // Update hero title
            if ($request->title) {
                Content::updateOrCreate(
                    ['key' => 'hero_title'],
                    ['title' => $request->title, 'is_active' => true]
                );
            }

            // Update hero subtitle
            if ($request->subtitle) {
                Content::updateOrCreate(
                    ['key' => 'hero_subtitle'],
                    ['title' => $request->subtitle, 'is_active' => true]
                );
            }

            // Update hero description
            if ($request->description) {
                Content::updateOrCreate(
                    ['key' => 'hero_description'],
                    ['content' => $request->description, 'is_active' => true]
                );
            }

            // Update hero image
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('contents', 'public');
                Content::updateOrCreate(
                    ['key' => 'hero_image'],
                    ['image' => $imagePath, 'is_active' => true]
                );
            }

            return redirect()->route('admin.contents')->with('success', 'Hero berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()->route('admin.contents')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function updateAbout(Request $request)
    {
        try {
            // Update about title
            if ($request->aboutTitle) {
                Content::updateOrCreate(
                    ['key' => 'about_home_title'],
                    ['title' => $request->aboutTitle, 'is_active' => true]
                );
            }

            // Update about description
            if ($request->aboutDescription) {
                Content::updateOrCreate(
                    ['key' => 'about_home_description'],
                    ['content' => $request->aboutDescription, 'is_active' => true]
                );
            }

            return redirect()->route('admin.contents')->with('success', 'Tentang Kami berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()->route('admin.contents')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function addAbout(Request $request)
    {
        try {
            $request->validate([
                'aboutTitle' => 'required|string|max:255',
                'aboutDescription' => 'required|string'
            ]);

            // Generate unique key for new about item
            $timestamp = time();
            $titleKey = 'about_item_title_' . $timestamp;
            $descKey = 'about_item_desc_' . $timestamp;

            // Create new about title
            Content::create([
                'key' => $titleKey,
                'title' => $request->aboutTitle,
                'is_active' => true
            ]);

            // Create new about description
            Content::create([
                'key' => $descKey,
                'content' => $request->aboutDescription,
                'is_active' => true
            ]);

            return redirect()->route('admin.contents')->with('success', 'Item Tentang Kami berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->route('admin.contents')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deleteAbout(Request $request)
    {
        try {
            // Delete main about items
            Content::where('key', 'about_home_title')->delete();
            Content::where('key', 'about_home_description')->delete();

            return redirect()->route('admin.contents')->with('success', 'Item Tentang Kami berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('admin.contents')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function addCard(Request $request)
    {
        try {
            $request->validate([
                'cardTitle' => 'required|string|max:255',
                'cardDescription' => 'required|string',
                'cardImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $imagePath = null;
            if ($request->hasFile('cardImage')) {
                $imagePath = $request->file('cardImage')->store('cards', 'public');
            }

            $timestamp = time();
            Content::create([
                'key' => 'card_' . $timestamp,
                'title' => $request->cardTitle,
                'content' => $request->cardDescription,
                'image' => $imagePath,
                'is_active' => true
            ]);

            return redirect()->route('admin.contents')->with('success', 'Card berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->route('admin.contents')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function updateCard(Request $request, $id)
    {
        try {
            $request->validate([
                'cardTitle' => 'required|string|max:255',
                'cardDescription' => 'required|string',
                'cardImage' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
            ]);

            $card = Content::findOrFail($id);
            
            $updateData = [
                'title' => $request->cardTitle,
                'content' => $request->cardDescription
            ];

            if ($request->hasFile('cardImage')) {
                $updateData['image'] = $request->file('cardImage')->store('cards', 'public');
            }

            $card->update($updateData);

            return redirect()->route('admin.contents')->with('success', 'Card berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()->route('admin.contents')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function deleteCard(Request $request, $id)
    {
        try {
            Content::findOrFail($id)->delete();
            return redirect()->route('admin.contents')->with('success', 'Card berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('admin.contents')->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function updateNews(Request $request)
    {
        try {
            if ($request->newsTitle) {
                Content::updateOrCreate(
                    ['key' => 'news_title'],
                    ['title' => $request->newsTitle, 'is_active' => true]
                );
            }

            return redirect()->route('admin.contents')->with('success', 'Berita berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()->route('admin.contents')->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function setFeaturedNews(Request $request)
    {
        try {
            // Reset semua berita jadi tidak featured
            \App\Models\News::where('is_featured', true)->update(['is_featured' => false]);
            
            // Set berita yang dipilih jadi featured
            \App\Models\News::findOrFail($request->news_id)->update(['is_featured' => true]);
            
            return redirect()->route('admin.contents')->with('success', 'Berita featured berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()->route('admin.contents')->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function storeNews(Request $request)
    {
        $news = new \App\Models\News();
        $news->title = $request->title;
        $news->slug = \Str::slug($request->title);
        $news->content = $request->content;
        $news->excerpt = $request->excerpt;
        $news->is_published = $request->has('is_published') ? 1 : 0;
        $news->is_featured = $request->has('is_featured') ? 1 : 0;
        $news->published_at = $request->has('is_published') ? now() : null;
        
        if ($request->hasFile('image')) {
            $news->image = $request->file('image')->store('news', 'public');
        }
        
        $news->save();
        
        // Auto-sync foto ke public
        $this->syncFotoToPublic();
        
        return redirect()->route('admin.news')->with('success', 'Berita berhasil ditambahkan!');
    }
    
    public function editNews($id)
    {
        try {
            $news = \App\Models\News::findOrFail($id);
            return response()->json($news);
        } catch (\Exception $e) {
            return response()->json(['error' => 'News not found'], 404);
        }
    }
    
    public function updateNewsData(Request $request, $id)
    {
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'excerpt' => 'nullable|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240'
            ]);

            $news = \App\Models\News::findOrFail($id);
            
            $news->title = $request->title;
            $news->slug = \Str::slug($request->title);
            $news->content = $request->content;
            $news->excerpt = $request->excerpt;
            $news->is_published = $request->has('is_published') ? 1 : 0;
            $news->is_featured = $request->has('is_featured') ? 1 : 0;
            $news->published_at = $request->has('is_published') ? now() : null;
            
            if ($request->hasFile('image')) {
                $news->image = $request->file('image')->store('news', 'public');
            }
            
            $news->save();
            
            // Auto-sync foto ke public
            $this->syncFotoToPublic();
            
            return redirect()->route('admin.news')->with('success', 'Berita berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()->route('admin.news')->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function publishToIndex(Request $request)
    {
        try {
            $newsId = $request->news_id;
            $position = $request->position; // Sekarang hanya satu posisi
            
            if (!$position) {
                return redirect()->route('admin.contents')->with('error', 'Pilih posisi terlebih dahulu!');
            }
            
            // Hapus berita lain dari posisi ini
            Content::where('key', 'news_position_' . $position)->delete();
            
            // Hapus berita ini dari posisi lain
            Content::where('content', $newsId)->where('key', 'like', 'news_position_%')->delete();
            
            // Simpan berita ke posisi yang dipilih
            Content::create([
                'key' => 'news_position_' . $position,
                'title' => 'News Position ' . $position,
                'content' => $newsId,
                'is_active' => true
            ]);
            
            $message = 'Berita berhasil dipublis ke posisi: Berita ' . $position;
            return redirect()->route('admin.contents')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.contents')->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function toggleFeaturedNews(Request $request)
    {
        // Reset semua berita jadi tidak featured
        \App\Models\News::where('is_featured', true)->update(['is_featured' => false]);
        
        // Set berita yang dipilih jadi featured jika checked
        if ($request->is_featured == '1') {
            \App\Models\News::findOrFail($request->news_id)->update(['is_featured' => true]);
        }
        
        return redirect()->route('admin.news')->with('success', 'Status featured berhasil diupdate!');
    }
    
    public function deleteNews($id)
    {
        try {
            \App\Models\News::findOrFail($id)->delete();
            return redirect()->route('admin.news')->with('success', 'Berita berhasil dihapus!');
        } catch (\Exception $e) {
            return redirect()->route('admin.news')->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function addGallery(Request $request)
    {
        try {
            $request->validate([
                'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:10240',
            ]);

            $imagePath = $request->file('image')->store('gallery', 'public');
            
            Gallery::create([
                'title' => 'Gallery ' . time(),
                'image' => $imagePath,
            ]);
            
            // Auto-sync foto ke public (karena tidak ada symbolic link)
            $this->syncFotoToPublic();

            return redirect()->back()->with('success', 'Gallery berhasil ditambahkan!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function updateGallery(Request $request, $id)
    {
        try {
            $request->validate([
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            ]);

            $gallery = Gallery::findOrFail($id);
            
            $updateData = [];

            if ($request->hasFile('image')) {
                $updateData['image'] = $request->file('image')->store('gallery', 'public');
            }

            $gallery->update($updateData);
            
            // Auto-sync foto ke public (karena tidak ada symbolic link)
            $this->syncFotoToPublic();

            return redirect()->back()->with('success', 'Gallery berhasil diupdate!');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function toggleCarousel($id)
    {
        try {
            $gallery = Gallery::findOrFail($id);
            
            // Jika gallery belum published, cek apakah sudah ada 5 yang published
            if (!$gallery->is_published) {
                $publishedCount = Gallery::where('is_published', true)->count();
                if ($publishedCount >= 5) {
                    return redirect()->back()->with('error', 'Maksimal 5 foto untuk carousel!');
                }
            }
            
            // Toggle status published
            $gallery->update(['is_published' => !$gallery->is_published]);
            
            $message = $gallery->is_published ? 'Gallery berhasil dipublish ke carousel!' : 'Gallery berhasil dihapus dari carousel!';
            return redirect()->back()->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function publishGallery($id)
    {
        $publishedCount = Gallery::where('is_published', true)->count();
        if ($publishedCount >= 5) {
            return redirect()->back()->with('error', 'Maksimal 5 foto untuk carousel!');
        }
        
        Gallery::findOrFail($id)->update(['is_published' => true]);
        return redirect()->back()->with('success', 'Gallery berhasil dipublish!');
    }
    
    public function publishGalleryToIndex(Request $request)
    {
        try {
            $galleryId = $request->gallery_id;
            $position = $request->position;
            
            if (!$position) {
                return redirect()->route('admin.contents')->with('error', 'Pilih posisi terlebih dahulu!');
            }
            
            // Hapus gallery lain dari posisi ini
            Content::where('key', 'gallery_position_' . $position)->delete();
            
            // Hapus gallery ini dari posisi lain
            Content::where('content', $galleryId)->where('key', 'like', 'gallery_position_%')->delete();
            
            // Simpan gallery ke posisi yang dipilih
            Content::create([
                'key' => 'gallery_position_' . $position,
                'title' => 'Gallery Position ' . $position,
                'content' => $galleryId,
                'is_active' => true
            ]);
            
            $message = 'Gallery berhasil dipublis ke posisi: Gallery ' . $position;
            return redirect()->route('admin.contents')->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->route('admin.contents')->with('error', 'Error: ' . $e->getMessage());
        }
    }

    public function updateContentData(Request $request)
    {
        try {
            $type = $request->type;
            
            if ($type === 'hero') {
                // Update hero content
                if ($request->hero_title) {
                    Content::updateOrCreate(
                        ['key' => 'hero_title'],
                        ['title' => $request->hero_title, 'is_active' => true]
                    );
                }
                
                if ($request->hero_subtitle) {
                    Content::updateOrCreate(
                        ['key' => 'hero_subtitle'],
                        ['title' => $request->hero_subtitle, 'is_active' => true]
                    );
                }
                
                if ($request->hero_description) {
                    Content::updateOrCreate(
                        ['key' => 'hero_description'],
                        ['content' => $request->hero_description, 'is_active' => true]
                    );
                }
                
                if ($request->hasFile('hero_image')) {
                    $imagePath = $request->file('hero_image')->store('contents', 'public');
                    Content::updateOrCreate(
                        ['key' => 'hero_image'],
                        ['image' => $imagePath, 'is_active' => true]
                    );
                }
                
                return redirect()->route('admin.contents')->with('success', 'Hero content updated successfully!');
            }
            
            if ($type === 'about') {
                if ($request->about_title) {
                    Content::updateOrCreate(
                        ['key' => 'about_home_title'],
                        ['title' => $request->about_title, 'is_active' => true]
                    );
                }
                
                if ($request->about_description) {
                    Content::updateOrCreate(
                        ['key' => 'about_home_description'],
                        ['content' => $request->about_description, 'is_active' => true]
                    );
                }
                
                return redirect()->route('admin.contents')->with('success', 'About section updated successfully!');
            }
            
        } catch (\Exception $e) {
            return redirect()->route('admin.contents')->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function storeCard(Request $request)
    {
        \Log::info('Store Card Request', ['data' => $request->all()]);
        
        try {
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240'
            ]);

            $imagePath = null;
            if ($request->hasFile('image')) {
                $imagePath = $request->file('image')->store('contents', 'public');
            }

            $timestamp = time();
            Content::create([
                'key' => 'card_' . $timestamp,
                'title' => $request->title,
                'content' => $request->content,
                'image' => $imagePath,
                'is_active' => true
            ]);

            return redirect()->route('admin.contents')->with('success', 'Card added successfully!');
        } catch (\Exception $e) {
            \Log::error('Store Card Error', ['error' => $e->getMessage()]);
            return redirect()->route('admin.contents')->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function getCard($id)
    {
        \Log::info('Get Card Request', ['id' => $id]);
        
        try {
            $card = Content::findOrFail($id);
            \Log::info('Card Found', ['card' => $card->toArray()]);
            return response()->json($card);
        } catch (\Exception $e) {
            \Log::error('Get Card Error', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Card not found'], 404);
        }
    }
    
    public function updateCardData(Request $request, $id)
    {
        \Log::info('Card Update Request', ['id' => $id, 'data' => $request->all()]);
        
        try {
            $card = Content::findOrFail($id);
            \Log::info('Updating card from URL ID', ['id' => $id]);
            
            $request->validate([
                'title' => 'required|string|max:255',
                'content' => 'required|string',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240'
            ]);
            
            $updateData = [
                'title' => $request->title,
                'content' => $request->content
            ];

            if ($request->hasFile('image')) {
                $updateData['image'] = $request->file('image')->store('contents', 'public');
                \Log::info('Image uploaded', ['path' => $updateData['image']]);
            }

            \Log::info('Update Data', ['data' => $updateData]);
            $result = $card->update($updateData);
            \Log::info('Update Result', ['result' => $result]);

            return redirect()->route('admin.contents')->with('success', 'Card updated successfully!');
        } catch (\Exception $e) {
            \Log::error('Card Update Error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('admin.contents')->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function deleteCardData($id)
    {
        try {
            Content::findOrFail($id)->delete();
            return redirect()->route('admin.contents')->with('success', 'Card deleted successfully!');
        } catch (\Exception $e) {
            return redirect()->route('admin.contents')->with('error', 'Error: ' . $e->getMessage());
        }
    }
    
    public function tentang()
    {
        $tentang = Tentang::where('status', true)->first();
        return view('admin.tentang', compact('tentang'));
    }
    
    public function updateTentang(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'visi' => 'required|string',
            'misi' => 'required|string',
            'gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'gambar2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'visi_gambar1' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'visi_gambar2' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'misi_gambar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240'
        ]);
        
        $tentang = Tentang::where('status', true)->first();
        
        if (!$tentang) {
            $tentang = new Tentang();
        }
        
        $tentang->judul = $request->judul;
        $tentang->konten = $request->konten;
        $tentang->visi = $request->visi;
        $tentang->misi = $request->misi;
        $tentang->status = $request->has('status');
        
        if ($request->hasFile('gambar')) {
            $tentang->gambar = $request->file('gambar')->store('tentang', 'public');
        }
        
        if ($request->hasFile('gambar2')) {
            $tentang->gambar2 = $request->file('gambar2')->store('tentang', 'public');
        }
        
        if ($request->hasFile('visi_gambar1')) {
            $tentang->visi_gambar1 = $request->file('visi_gambar1')->store('tentang', 'public');
        }
        
        if ($request->hasFile('visi_gambar2')) {
            $tentang->visi_gambar2 = $request->file('visi_gambar2')->store('tentang', 'public');
        }
        
        if ($request->hasFile('misi_gambar')) {
            $tentang->misi_gambar = $request->file('misi_gambar')->store('tentang', 'public');
        }
        
        $tentang->save();
        
        // Auto-sync foto ke public
        $this->syncFotoToPublic();
        
        return redirect()->route('admin.tentang')->with('success', 'Halaman Tentang berhasil diupdate!');
    }
    
    // Helper function untuk sync foto ke public
    private function syncFotoToPublic()
    {
        try {
            $source = storage_path('app/public');
            $destination = public_path('storage');
            
            if (!is_dir($destination)) {
                mkdir($destination, 0755, true);
            }
            
            $folders = ['gallery', 'news', 'contents', 'tentang', 'cards'];
            
            foreach ($folders as $folder) {
                $src_folder = $source . '/' . $folder;
                $dest_folder = $destination . '/' . $folder;
                
                if (is_dir($src_folder)) {
                    if (!is_dir($dest_folder)) {
                        mkdir($dest_folder, 0755, true);
                    }
                    
                    foreach (glob($src_folder . '/*') as $file) {
                        if (is_file($file)) {
                            copy($file, $dest_folder . '/' . basename($file));
                        }
                    }
                }
            }
            
            \Log::info('Sync foto berhasil!');
        } catch (\Exception $e) {
            \Log::error('Sync foto error: ' . $e->getMessage());
        }
    }
}