<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AddController;
use App\Http\Controllers\UsersController;
use App\Http\Controllers\PartnersController;
use App\Http\Controllers\WritersController;
use App\Http\Controllers\BooksController;
use App\Http\Controllers\AudiobooksController;
use App\Http\Controllers\PartnerBooksController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\EventsController;
use App\Http\Controllers\QuestionsController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\ReportsController;



Route::prefix('admin')->group(function () {
    Route::get('/', fn () => redirect()->route('admin.auth'));
    // Public auth
    Route::get('/auth', [App\Http\Controllers\AdminAuthController::class, 'showAuthPage'])->name('admin.auth');
    Route::post('/login', [AdminAuthController::class, 'login'])->name('admin.login');
    Route::post('/register', [AdminAuthController::class, 'register'])->name('admin.register');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Protected
    Route::middleware('admin.auth')->group(function () {
        // Dashboard
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::post('/partner/approve', [AdminDashboardController::class, 'approvePartner'])->name('admin.partner.approve');
        Route::post('/partner/cancel',  [AdminDashboardController::class, 'cancelPartner'])->name('admin.partner.cancel');

        // Add page + ajax
        Route::get('/add', [AddController::class, 'index'])->name('admin.add');
        Route::post('/add/book', [AddController::class, 'storeBook'])->name('admin.add.book');
        Route::post('/add/audiobook', [AddController::class, 'storeAudiobook'])->name('admin.add.audiobook');
        Route::post('/add/subscription', [AddController::class, 'storeSubscription'])->name('admin.add.subscription');
        Route::post('/add/event', [AddController::class, 'storeEvent'])->name('admin.add.event');
        Route::post('/add/item', [AddController::class, 'addItem'])->name('admin.add.item');

        // Users
        Route::get('/users', [UsersController::class, 'index'])->name('admin.users');
        Route::post('/users/delete', [UsersController::class, 'destroy'])->name('admin.users.delete');

        // Partners
        Route::get('/partners', [PartnersController::class, 'index'])->name('admin.partners');
        Route::post('/partners/approve', [PartnersController::class, 'approve'])->name('admin.partners.approve');
        Route::post('/partners/delete',  [PartnersController::class, 'destroy'])->name('admin.partners.delete');

        // Writers  <<< THE 3 ROUTES YOU NEED >>>
        Route::get('/writers',         [WritersController::class, 'index'])->name('admin.writers');
        Route::post('/writers/update', [WritersController::class, 'update'])->name('admin.writers.update');
        Route::post('/writers/delete', [WritersController::class, 'destroy'])->name('admin.writers.delete');

        // Books
        Route::get('/books',              [\App\Http\Controllers\BooksController::class, 'index'])->name('admin.books');
        Route::get('/books/edit/{id}',    [\App\Http\Controllers\BooksController::class, 'edit'])->name('admin.books.edit');
        Route::post('/books/update',      [\App\Http\Controllers\BooksController::class, 'update'])->name('admin.books.update'); // <-- add this
        Route::post('/books/delete',      [\App\Http\Controllers\BooksController::class, 'destroy'])->name('admin.books.delete');

        // Audiobooks
        Route::get('/audiobooks',           [\App\Http\Controllers\AudiobooksController::class, 'index'])->name('admin.audiobooks');
        Route::post('/audiobooks/update',   [\App\Http\Controllers\AudiobooksController::class, 'update'])->name('admin.audiobooks.update');
        Route::post('/audiobooks/delete',   [\App\Http\Controllers\AudiobooksController::class, 'destroy'])->name('admin.audiobooks.delete');

        // Partner Books
        Route::get('/partnerbooks', [\App\Http\Controllers\PartnerBooksController::class, 'index'])->name('admin.partnerbooks');
        Route::post('/partnerbooks/approve', [\App\Http\Controllers\PartnerBooksController::class, 'approve'])->name('admin.partnerbooks.approve');
        Route::post('/partnerbooks/delete', [\App\Http\Controllers\PartnerBooksController::class, 'destroy'])->name('admin.partnerbooks.delete');
        Route::post('/partnerbooks/return-date', [\App\Http\Controllers\PartnerBooksController::class, 'setReturnDate'])->name('admin.partnerbooks.returnDate');

        // Orders
        Route::get('/orders', [\App\Http\Controllers\OrdersController::class, 'index'])->name('admin.orders');
        Route::post('/orders/status', [\App\Http\Controllers\OrdersController::class, 'updateStatus'])->name('admin.orders.updateStatus');
        Route::post('/orders/delete', [\App\Http\Controllers\OrdersController::class, 'destroy'])->name('admin.orders.delete');
        Route::get('/orders/edit/{id}', [\App\Http\Controllers\OrdersController::class, 'edit'])->name('admin.orders.edit');
        Route::post('/orders/edit/{id}', [\App\Http\Controllers\OrdersController::class, 'update'])->name('admin.orders.update');
        Route::get('/orders/{order}/items', [\App\Http\Controllers\OrdersController::class, 'items'])->name('admin.orders.items');
        
        // Subscription
        Route::get('/subscription', [SubscriptionController::class, 'index'])->name('admin.subscription');
        Route::post('/subscription/delete', [SubscriptionController::class, 'destroy'])->name('admin.subscription.delete');
        Route::get('/subscription/edit/{id}', [SubscriptionController::class, 'edit'])->name('admin.subscription.edit');
        Route::put('/subscription/update/{id}', [SubscriptionController::class, 'update'])->name('admin.subscription.update');
// Events  <<< THE 4 ROUTES YOU NEED >>>
        Route::get('/events', [EventsController::class, 'index'])->name('admin.events');
        Route::get('/events/edit/{id}', [EventsController::class, 'edit'])->name('admin.events.edit');
        Route::put('/events/edit/{id}', [EventsController::class, 'update'])->name('admin.events.update');
        Route::delete('/events/{id}', [EventsController::class, 'destroy'])->name('admin.events.destroy');


        



        // Others
        Route::get('/question',                                   [\App\Http\Controllers\QuestionsController::class, 'index'])->name('admin.question');
        Route::post('/question/answer',                           [\App\Http\Controllers\QuestionsController::class, 'storeAnswer'])->name('admin.question.answer.store');
        Route::put('/question/answer/{answer_id}',                [\App\Http\Controllers\QuestionsController::class, 'updateAnswer'])->name('admin.question.answer.update');
        Route::delete('/question/answer/{answer_id}',             [\App\Http\Controllers\QuestionsController::class, 'destroyAnswer'])->name('admin.question.answer.destroy');
       // Community  <<< THE 2 ROUTES YOU NEED >>>
        Route::get('/admin/community', [CommunityController::class, 'index'])->name('admin.community');
        Route::delete('/admin/community', [CommunityController::class, 'destroy'])->name('admin.community.destroy');
        Route::get('/reports',   [ReportsController::class, 'index'])->name('admin.reports');
    });
});
