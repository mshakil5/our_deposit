<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CompanyDetailsController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\TransactionController;

Route::group(['prefix' =>'admin/', 'middleware' => ['auth', 'is_admin']], function(){
  
    Route::get('/dashboard', [HomeController::class, 'adminHome'])->name('admin.dashboard');

    //Admin crud
    Route::get('/new-admin', [AdminController::class, 'getAdmin'])->name('alladmin');
    Route::post('/new-admin', [AdminController::class, 'adminStore']);
    Route::get('/new-admin/{id}/edit', [AdminController::class, 'adminEdit']);
    Route::post('/new-admin-update', [AdminController::class, 'adminUpdate']);
    Route::get('/new-admin/{id}', [AdminController::class, 'adminDelete']);

    //User crud
    Route::get('/users', [UserController::class, 'index'])->name('allUsers');
    Route::post('/users', [UserController::class, 'store']);
    Route::get('/users/{id}/edit', [UserController::class, 'edit']);
    Route::post('/users-update', [UserController::class, 'update']);
    Route::get('/users/{id}', [UserController::class, 'delete']);
    Route::post('/users/{id}/status', [UserController::class, 'updateStatus'])->name('users.updateStatus');

    // company information
    Route::get('/company-details', [CompanyDetailsController::class, 'index'])->name('admin.companyDetail');
    Route::post('/company-details', [CompanyDetailsController::class, 'update'])->name('admin.companyDetails');

    // Blog Categories Routes
    Route::get('/blog-categories', [BlogCategoryController::class, 'index'])->name('allBlogCategories');
    Route::post('/blog-categories', [BlogCategoryController::class, 'store']);
    Route::get('/blog-categories/{id}/edit', [BlogCategoryController::class, 'edit']);
    Route::post('/blog-categories-update', [BlogCategoryController::class, 'update']);
    Route::get('/blog-categories/{id}', [BlogCategoryController::class, 'delete']);
    Route::post('/blog-categories/{id}/status', [BlogCategoryController::class, 'updateStatus'])->name('blogCategories.updateStatus');

    Route::get('/blogs', [BlogController::class, 'index'])->name('allBlogs');
    Route::post('/blogs', [BlogController::class, 'store']);
    Route::get('/blogs/{id}/edit', [BlogController::class, 'edit']);
    Route::post('/blogs-update', [BlogController::class, 'update']);
    Route::get('/blogs/{id}', [BlogController::class, 'delete']);
    Route::post('/blogs/{id}/status', [BlogController::class, 'updateStatus'])->name('blogs.updateStatus');

    
    Route::get('/pending-transaction', [TransactionController::class, 'pending'])->name('pendingtransaction');
    Route::get('/transaction/{id?}', [TransactionController::class, 'index'])->name('alltransaction');
    Route::get('/transaction/{id}/edit', [TransactionController::class, 'edit']);
    Route::post('/transaction-status', [TransactionController::class, 'updateStatus'])->name('transaction.updateStatus');
    Route::post('/transaction-update', [TransactionController::class, 'update']);

    Route::get('/missing-deposit', [TransactionController::class, 'missingDeposit'])->name('missingDeposit');
        
});
  