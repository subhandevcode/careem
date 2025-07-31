<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\dataController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\UserSaveController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\SubscriptionController;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
       if (Auth::check()) {
           // Check user type to redirect to the appropriate dashboard
           if (Auth::user()->user_type == "1") { // Admin User
               return view('admin.dashboard');
           } elseif (Auth::user()->user_type == "0") { // Regular User
               return view('dashboard');
           }    
       }
       return redirect()->route('login');
    })->name('dashboard');
});

Route::middleware('auth')->group(function () {
    Route::get('/userprofile', [dataController::class, 'edit'])->name('userprofile.edit');
    Route::post('/userprofile/update', [dataController::class, 'update'])->name('userprofile.update');
});


Route::middleware(['auth'])->group(function () {
    Route::get('/user-save', [UserSaveController::class, 'edit'])->name('user.save.edit');
    Route::post('/user-save', [UserSaveController::class, 'update'])->name('user.save.update');
});

Route::get('/api/nearby-users', [UserProfileController::class, 'getNearbyUsers']);

// Define a route to call the chatWithUser method
Route::get('/chat/{userId}', [ChatController::class, 'chatWithUser'])->name('chat.withUser');

//Subsciption 
// Route::get('/subscribe', [SubscriptionController::class, 'showSubscriptionPage'])->name('subscribe.show');
// Route::post('/subscribe', [SubscriptionController::class, 'subscribe'])->name('subscribe.store');

Route::get('/subscribe', [SubscriptionController::class, 'showSubscriptionPage'])->name('subscribe.show');
Route::post('/subscribe', [SubscriptionController::class, 'createSubscription'])->name('subscribe.store');
// Add the user profile show route
Route::get('/userprofile/show', [UserProfileController::class, 'show'])->name('userprofile.show');


Route::get('/payment/{user_id}', [PaymentController::class, 'showPaymentPage'])->name('payment.show');
Route::post('/payment', [PaymentController::class, 'processPayment'])->name('payment.process');

Route::get('/payment/success', function () {
    return view('payment.payment-success');  // Payment success page inside the 'payment' folder
})->name('payment.success');

Route::get('/payment/failed', function () {
    return view('payment.payment-failed');  // Payment failed page inside the 'payment' folder
})->name('payment.failed');