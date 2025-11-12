<?php

use App\Http\Controllers\DiscussController;
use Illuminate\Support\Facades\Route;
  
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\User\ProfileController;
  

// cache clear
Route::get('/clear', function() {
    Auth::logout();
    session()->flush();
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
 });
//  cache clear
  
  
Auth::routes();

// Route::fallback(function () {
//     return redirect('/');
// });
  
Route::get('/', [FrontendController::class, 'index'])->name('homepage');
Route::get('/home', [HomeController::class, 'index'])->name('home');
Route::get('about-us', [FrontendController::class, 'about'])->name('about');
Route::get('/blog/{slug}', [FrontendController::class, 'showBlogDetails'])->name('blog.details');

Route::get('/download-report', [ReportController::class, 'downloadPDF'])->name('singleReport');
Route::get('/download-overall-report', [ReportController::class, 'allmemberReport'])->name('allmemberReport');
Route::get('/deposits/export', [ReportController::class, 'exportMonthlyDepositReport'])->name('deposits.export');


Route::group(['prefix' =>'user/', 'middleware' => ['auth', 'is_user']], function(){
  
    Route::get('/dashboard', [HomeController::class, 'userHome'])->name('user.dashboard');
    Route::get('/profile', [ProfileController::class, 'profile'])->name('user.profile');
    Route::post('/profile', [ProfileController::class, 'profileUpdate'])->name('user.profileUpdate');

    
    Route::get('/installment', [ProfileController::class, 'addMoney'])->name('user.installment');
    Route::post('/installment', [ProfileController::class, 'addMoneyStore'])->name('user.installmentStore');
    Route::post('/installment-update', [ProfileController::class, 'installmentUpdate'])->name('user.installmentUpdate');
    Route::get('/installment/{id}', [ProfileController::class, 'tranDelete'])->name('user.installmentDelete');


    Route::get('/all-discussion', [DiscussController::class, 'getAllDiscussion'])->name('user.allDiscussion');
    Route::get('/get-my-discussion', [DiscussController::class, 'getMyDiscussion'])->name('user.mydiscussion');
    Route::post('/get-my-discussion', [DiscussController::class, 'discussionStore'])->name('user.discussionStore');
    Route::get('/get-my-discussion/{id}', [DiscussController::class, 'discussionDelete'])->name('user.discussionDelete');
    Route::get('/get-my-discussion-edit/{id}', [DiscussController::class, 'discussionEdit'])->name('user.discussionEdit');
    Route::post('/update-my-discussion', [DiscussController::class, 'discussionUpdate'])->name('user.discussionUpdate');



});
  

Route::group(['prefix' =>'manager/', 'middleware' => ['auth', 'is_manager']], function(){
  
    Route::get('/dashboard', [HomeController::class, 'managerHome'])->name('manager.dashboard');
});
 