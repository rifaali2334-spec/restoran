<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\TentangController;

Route::get('/', [HomeController::class, 'index']);

Route::get('/admin/login', [AdminController::class, 'login'])->name('admin.login');

Route::post('/admin/login', [AdminController::class, 'loginPost'])->name('admin.login.post');

Route::get('/admin/logout', [AdminController::class, 'logout'])->name('admin.logout');

Route::get('/admin/hash-password', [AdminController::class, 'hashPassword']);

Route::get('/tentang', [TentangController::class, 'index']);

Route::get('/berita', [HomeController::class, 'berita']);

Route::get('/api/berita/{slug}', [HomeController::class, 'getBeritaDetail']);

Route::get('/berita/{slug}', [HomeController::class, 'beritaDetail'])->name('berita.detail');

Route::get('/galeri', [HomeController::class, 'galeri']);

Route::get('/kontak', function () {
    return view('kontak');
});

Route::post('/contact', function () {
    request()->validate([
        'subject' => 'required|max:255',
        'name' => 'required|max:255', 
        'email' => 'required|email|max:255',
        'message' => 'required'
    ]);
    
    \App\Models\ContactMessage::create([
        'subject' => request('subject'),
        'name' => request('name'),
        'email' => request('email'), 
        'message' => request('message')
    ]);
    
    return redirect('/kontak')->with('success', 'Pesan berhasil dikirim!');
});

Route::middleware(['admin.auth'])->group(function () {
    Route::get('/admin', [AdminController::class, 'index'])->name('admin.index');
    Route::get('/admin/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/admin/home', [AdminController::class, 'home'])->name('admin.home');
    Route::get('/admin/contents', [AdminController::class, 'contents'])->name('admin.contents');
    Route::get('/admin/contents-table', function() {
        $contents = \App\Models\Content::all();
        return view('admin.contents', compact('contents'));
    })->name('admin.contents.table');
    Route::get('/admin/contents-debug', function() {
        $contents = \App\Models\Content::all();
        return view('admin.contents-debug', compact('contents'));
    });
    Route::post('/admin/contents', [AdminController::class, 'storeContent'])->name('admin.contents.store');
    Route::get('/admin/contents/{id}/edit', [AdminController::class, 'showContent'])->name('admin.contents.edit');
    Route::post('/admin/contents/{id}/update', [AdminController::class, 'updateContent'])->name('admin.contents.update');
    Route::delete('/admin/contents/{id}', [AdminController::class, 'deleteContent'])->name('admin.contents.delete');
    Route::get('/admin/settings', [AdminController::class, 'settings'])->name('admin.settings');
    Route::put('/admin/footer/update', [AdminController::class, 'updateFooter'])->name('admin.footer.update');
    Route::get('/admin/news', [AdminController::class, 'news'])->name('admin.news');
    Route::get('/admin/galleries', [AdminController::class, 'galleries'])->name('admin.galleries');
    Route::get('/admin/contacts', [AdminController::class, 'contacts'])->name('admin.contacts');
    Route::get('/admin/contacts/{id}', [AdminController::class, 'showContact'])->name('admin.contacts.show');
    Route::post('/admin/contacts/{id}', [AdminController::class, 'updateContact'])->name('admin.contacts.update');
    Route::delete('/admin/contacts/{id}', [AdminController::class, 'deleteContact'])->name('admin.contacts.delete');
    Route::post('/admin/hero/update', [AdminController::class, 'updateHeroImage'])->name('admin.hero.update');
    Route::post('/admin/about/update', [AdminController::class, 'updateAbout'])->name('admin.about.update');
    Route::post('/admin/about/add', [AdminController::class, 'addAbout'])->name('admin.about.add');
    Route::post('/admin/about/delete', [AdminController::class, 'deleteAbout'])->name('admin.about.delete');
    Route::post('/admin/cards/add', [AdminController::class, 'addCard'])->name('admin.cards.add');
    Route::post('/admin/cards/{id}/update', [AdminController::class, 'updateCard'])->name('admin.cards.update');
    Route::post('/admin/cards/{id}/delete', [AdminController::class, 'deleteCard'])->name('admin.cards.delete');
    Route::post('/admin/news/update', [AdminController::class, 'updateNews'])->name('admin.news.update.settings');
    Route::post('/admin/news/store', [AdminController::class, 'storeNews'])->name('admin.news.store');
    Route::get('/admin/news/{id}/edit', [AdminController::class, 'editNews'])->name('admin.news.edit');
    Route::put('/admin/news/{id}/update', [AdminController::class, 'updateNewsData'])->name('admin.news.update');
    Route::post('/admin/news/toggle-featured', [AdminController::class, 'toggleFeaturedNews'])->name('admin.news.toggleFeatured');
    Route::delete('/admin/news/{id}', [AdminController::class, 'deleteNews'])->name('admin.news.delete');
    Route::post('/admin/news/publish-to-index', [AdminController::class, 'publishToIndex'])->name('admin.news.publishToIndex');
    Route::post('/admin/gallery/add', [AdminController::class, 'addGallery'])->name('admin.gallery.add');
    Route::post('/admin/gallery/{id}/update', [AdminController::class, 'updateGallery'])->name('admin.gallery.update');
    Route::post('/admin/gallery/{id}/toggle-carousel', [AdminController::class, 'toggleCarousel'])->name('admin.gallery.toggleCarousel');
    Route::post('/admin/gallery/publish-to-index', [AdminController::class, 'publishGalleryToIndex'])->name('admin.gallery.publishToIndex');
    Route::post('/admin/content/update', [AdminController::class, 'updateContentData'])->name('admin.content.update');
    Route::post('/admin/card/store', [AdminController::class, 'storeCard'])->name('admin.card.store');
    Route::get('/admin/card/{id}', [AdminController::class, 'getCard'])->name('admin.card.get');
    Route::post('/admin/card/{id}/update', [AdminController::class, 'updateCardData'])->name('admin.card.update');
    Route::put('/admin/card/{id}/update', [AdminController::class, 'updateCardData'])->name('admin.card.update.put');    
    Route::delete('/admin/card/{id}', [AdminController::class, 'deleteCardData'])->name('admin.card.delete');
    Route::delete('/admin/gallery/{id}', [AdminController::class, 'deleteGallery'])->name('admin.gallery.delete');
    Route::get('/admin/tentang', [AdminController::class, 'tentang'])->name('admin.tentang');
    Route::post('/admin/tentang/update', [AdminController::class, 'updateTentang'])->name('admin.tentang.update');
});
