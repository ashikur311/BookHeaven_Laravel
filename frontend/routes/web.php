<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| AUTHENTICATION
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\VerifyOtpController;
use App\Http\Controllers\Auth\ResetPasswordController;

// 🔹 Login & Register
Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'showLoginRegister'])->name('login');
Route::get('/register', [AuthController::class, 'showLoginRegister'])->name('register');

Route::get('/verify-2fa', [AuthController::class, 'show2FA'])->name('verify.2fa');
Route::post('/verify-2fa', [AuthController::class, 'verify2FA'])->name('verify.2fa.post');

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// 🔹 Forgot Password & OTP
Route::get('/forgot-password', [ForgotPasswordController::class, 'showForgotForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetOtp'])->name('password.email');

Route::get('/verify-otp', [VerifyOtpController::class, 'show'])->name('verify.otp.show');
Route::post('/verify-otp', [VerifyOtpController::class, 'verify'])->name('verify.otp.post');


Route::get('/reset-password', [ResetPasswordController::class, 'showResetForm'])->name('password.reset.form');
Route::post('/reset-password', [ResetPasswordController::class, 'resetPassword'])->name('password.reset');


/*
|--------------------------------------------------------------------------
| HOME + SEARCH
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\HomeController;
use App\Http\Controllers\SearchSuggestionController;
use App\Http\Controllers\SearchController;

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search_suggestions', [SearchSuggestionController::class, 'index'])->name('search.suggestions');
Route::get('/search', [SearchController::class, 'index'])->name('search');

/*
|--------------------------------------------------------------------------
| AUTHENTICATED USER FEATURES
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\MusicPlayerController;
use App\Http\Controllers\UserSubscriptionController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\WishlistController;
use App\Http\Controllers\SubscriptionPlanController;
use App\Http\Controllers\SubscriptionController; // ✅ Added
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\PaymentController;

use App\Http\Controllers\BookSubscriptionController;
use App\Http\Controllers\PartnerController;

use App\Http\Controllers\EventController;

// ✅ Everything inside this group requires login
Route::middleware(['auth'])->group(function () {

    // 🔹 Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/settings', [ProfileController::class, 'settings'])->name('profile.settings');
    Route::get('/profile/subscriptions', [UserSubscriptionController::class, 'index'])->name('user.subscription');
    Route::get('/profile/orders', [OrderController::class, 'index'])->name('orders.index');

    Route::view('/orders', 'profile.orders')->name('orders');   // ✅ <── Fixes your current error
    Route::get('/orders/details/{id}', [OrderController::class, 'details'])->name('orders.details');
    
    // 🔹 Subscription Pages
    Route::get('/subscription', [SubscriptionController::class, 'index'])->name('subscription'); // ✅ Added route for old subscription_plan.php
    Route::get('/subscriptions/plans', [SubscriptionPlanController::class, 'index'])->name('subscriptions.plan');
    
    // 🔹 Add book Subscription 
    
    Route::get('/audio_book_add_to_subscription', [SubscriptionController::class, 'audioAddToSubscription'])
        ->name('audio.add.to.subscription');
    Route::post('/audio_book_add_to_subscription/add', [SubscriptionController::class, 'storeAudioToSubscription'])
        ->name('audio.add.to.subscription.store');


Route::get('/book_add_to_subscription', [BookSubscriptionController::class, 'show'])->middleware('auth');
Route::post('/book_add_to_subscription', [BookSubscriptionController::class, 'addBook'])->middleware('auth');  

    // 🔹 Wishlist
    Route::get('/wishlist', [WishlistController::class, 'index'])->name('wishlist');
    Route::post('/wishlist/remove', [WishlistController::class, 'remove'])->name('wishlist.remove');
    Route::post('/wishlist/add-to-cart', [WishlistController::class, 'addToCart'])->name('wishlist.addToCart');
    Route::post('/wishlist/check-cart', [WishlistController::class, 'checkCart'])->name('wishlist.checkCart');

    
    Route::post('/book/add-to-wishlist', [BookController::class, 'addToWishlist'])->name('book.addToWishlist');
    

// 🔹 Cart
Route::get('/cart', [CartController::class, 'index'])->name('cart'); // 👈 alias used in header.blade.php

Route::post('/cart/action', [CartController::class, 'handleAction'])->name('cart.action');
Route::post('/cart/add', [CartController::class, 'add'])->name('cart.add');





    // 🔹 Settings
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings');
    Route::post('/settings/update-profile', [SettingsController::class, 'updateProfile'])->name('settings.updateProfile');
    Route::post('/settings/change-password', [SettingsController::class, 'changePassword'])->name('settings.changePassword');
    Route::post('/settings/update-billing', [SettingsController::class, 'updateBilling'])->name('settings.updateBilling');
    Route::post('/settings/add-payment', [SettingsController::class, 'addPayment'])->name('settings.addPayment');
    Route::post('/settings/update-verification', [SettingsController::class, 'updateVerification'])->name('settings.updateVerification');

    // 🔹 Partner
    Route::get('/partner', [PartnerController::class, 'index'])->name('partner.dashboard');
    Route::post('/partner/become', [PartnerController::class, 'becomePartner'])->name('partner.become');
    Route::post('/partner/apply-return', [PartnerController::class, 'applyReturn'])->name('partner.applyReturn');
    Route::post('/partner/delete-return', [PartnerController::class, 'deleteReturn'])->name('partner.deleteReturn');

       Route::get('/partner/add-book', [PartnerController::class, 'addBook'])->name('partner.addBook');
    Route::post('/partner/add-book', [PartnerController::class, 'storeBook'])->name('partner.storeBook');


    // 🔹 Payments
// 🔹 Payment Routes
    Route::get('/payment/bkash', [PaymentController::class, 'bkash'])->name('payment.bkash');
    Route::post('/payment/bkash/pay', [PaymentController::class, 'bkashPay'])->name('payment.bkash.pay');

// 🔹 Card Payment Routes
Route::get('/payment/card', [PaymentController::class, 'cardPayment'])->name('payment.card');
Route::post('/payment/card', [PaymentController::class, 'processCardPayment'])->name('payment.card.pay');
    

    // OTP (AJAX)
    Route::post('/payment/send-otp', [PaymentController::class, 'sendOtp'])->name('payment.send.otp');


    // 🔹 Music Player
    Route::get('/music-player', [MusicPlayerController::class, 'index'])->name('music.player');
    Route::get('/audiobooks/user', [MusicPlayerController::class, 'getUserAudiobooks'])->name('audiobooks.user');

// events:

    Route::get('/events', [EventController::class, 'index'])->name('events.index');
    Route::post('/events/join', [EventController::class, 'join'])->name('events.join');
    Route::post('/events/leave', [EventController::class, 'leave'])->name('events.leave');
    Route::post('/events/ticket', [EventController::class, 'ticket'])->name('events.ticket');



});


// ✅ Move this OUTSIDE (public, AJAX-safe)
Route::post('/payment/send-otp', [PaymentController::class, 'sendOtp'])->name('payment.send.otp');

/*
|--------------------------------------------------------------------------
| COMMUNITY
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\MessageController;

Route::middleware(['auth'])->group(function () {
    Route::get('/community', [CommunityController::class, 'index'])->name('community.dashboard');
    Route::post('/community/create', [CommunityController::class, 'create'])->name('community.create');
    Route::post('/community/join', [CommunityController::class, 'join'])->name('community.join');
    Route::post('/community/update', [CommunityController::class, 'update'])->name('community.update');
    
    Route::get('/community/{id}', [CommunityController::class, 'show'])->name('community.show');
    Route::post('/community/{id}/post', [CommunityController::class, 'createPost'])->name('community.post');
    Route::post('/community/{id}/like', [CommunityController::class, 'toggleLike'])->name('community.like');
    Route::post('/community/{id}/comment', [CommunityController::class, 'addComment'])->name('community.comment');
    Route::delete('/community/{id}/post/{post_id}', [CommunityController::class, 'deletePost'])->name('community.deletePost');
    Route::patch('/community/{id}/post/{post_id}', [CommunityController::class, 'updatePost'])->name('community.updatePost');

    Route::get('/community/{id}/members', [CommunityController::class, 'members'])->name('community.members');

    // Messages
    Route::get('/community/{id}/messages', [MessageController::class, 'index'])->name('community.messages');
    Route::post('/community/{id}/messages/send', [MessageController::class, 'send'])->name('community.messages.send');
    Route::get('/community/{id}/messages/poll', [MessageController::class, 'poll'])->name('community.messages.poll');
});


/*
|--------------------------------------------------------------------------
| STATIC PAGES
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\PageController;

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/faq', [PageController::class, 'faq'])->name('faq');
Route::get('/shipping', [PageController::class, 'shipping'])->name('shipping');
Route::get('/privacy', [PageController::class, 'privacy'])->name('privacy');


use App\Http\Controllers\GenreController;

Route::get('/genre_books/{id?}', [GenreController::class, 'show'])->name('genre_books.show');
Route::post('/genre_books/add-to-cart', [GenreController::class, 'addToCart'])->name('genre_books.addToCart');


use App\Http\Controllers\BookController;

Route::get('/book_details/{id}', [BookController::class, 'bookDetails'])->name('book.details');

Route::post('/book/add-to-cart', [BookController::class, 'addToCart'])->name('book.addToCart');
Route::post('/book/add-to-wishlist', [BookController::class, 'addToWishlist'])->name('book.addToWishlist');
Route::post('/book/submit-review', [BookController::class, 'submitReview'])->name('book.submitReview');
Route::post('/book/submit-question', [BookController::class, 'submitQuestion'])->name('book.submitQuestion');


use App\Http\Controllers\WriterController;

Route::get('/writer_books/{writer_id?}', [WriterController::class, 'index'])
     ->name('writer.books');
Route::post('/writer_books/add-to-cart', [WriterController::class, 'addToCart'])
     ->name('writer.addToCart');
