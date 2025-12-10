<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ClientsController;
use App\Http\Controllers\Admin\DealsController;
use App\Http\Controllers\Admin\LeadsController;
use App\Http\Controllers\Admin\TasksController;
use App\Http\Controllers\Admin\NotesController;
use App\Http\Controllers\Admin\UsersController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\DashboardController;




Route::get('/', function () {
    return view('welcome');
});
Route::get('admin/login', [AdminAuthController::class, 'showLoginForm'])->name('admin.login');
Route::post('admin/login', [AdminAuthController::class, 'login'])->name('admin.login.submit');
Route::get('locale/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'ar'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('locale.switch');


Route::get('admin/register', [AdminAuthController::class, 'showRegisterForm'])->name('admin.register');
Route::post('admin/register', [AdminAuthController::class, 'register'])->name('admin.register.submit');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__ . '/auth.php';

Route::prefix('admin')
    ->name('admin.')
    ->middleware(['admin_or_user']) // remove this if you have no admin authentication
    ->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Admin CRUD
    
        Route::get('logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

        //   Route::resource('admin', AdminController::class);
        Route::get('/admins/data', [AdminController::class, 'data'])->name('admin.data');
        Route::get('admin', [AdminController::class, 'index'])->name('admin.index');
        Route::get('admin/create', [AdminController::class, 'create'])->name('admin.create');
        Route::post('admin', [AdminController::class, 'store'])->name('admin.store');
        Route::get('admin/{admin}/edit', [AdminController::class, 'edit'])->name('admin.edit');
        Route::put('admin/{admin}', [AdminController::class, 'update'])->name('admin.update');
        Route::delete('admin/{admin}', [AdminController::class, 'destroy'])->name('admin.destroy');

        // CLIENT CRUD
        Route::get('/clients/data', [ClientsController::class, 'data'])->name('clients.data');

        Route::get('/clients/uploadForm', [ClientsController::class, 'uploadForm'])->name('clients.uploadForm');
        Route::post('/clients/uploadForm', [ClientsController::class, 'upload'])->name('clients.upload');


        Route::get('clients', [ClientsController::class, 'index'])->name('clients.index');
        Route::get('clients/create', [ClientsController::class, 'create'])->name('clients.create');
        Route::post('clients', [ClientsController::class, 'store'])->name('clients.store');
        Route::get('clients/{client}/edit', [ClientsController::class, 'edit'])->name('clients.edit');
        Route::put('clients/{client}', [ClientsController::class, 'update'])->name('clients.update');
        Route::delete('clients/{client}', [ClientsController::class, 'destroy'])->name('clients.destroy');

        // DEALS CRUD
        Route::get('deals', [DealsController::class, 'index'])->name('deals.index');
        Route::get('deals/data', [DealsController::class, 'data'])->name('deals.data');
        Route::get('deals/create', [DealsController::class, 'create'])->name('deals.create');
        Route::post('deals', [DealsController::class, 'store'])->name('deals.store');
        Route::get('deals/{id}/edit', [DealsController::class, 'edit'])->name('deals.edit');
        Route::put('deals/{id}', [DealsController::class, 'update'])->name('deals.update');
        Route::delete('deals/{id}', [DealsController::class, 'destroy'])->name('deals.destroy');

        //LEAD CRUD
        Route::get('leads', [LeadsController::class, 'index'])->name('leads.index');
        Route::get('leads/data', [LeadsController::class, 'data'])->name('leads.data');
        Route::get('leads/create', [LeadsController::class, 'create'])->name('leads.create');
        Route::post('leads', [LeadsController::class, 'store'])->name('leads.store');
        Route::get('leads/{id}/edit', [LeadsController::class, 'edit'])->name('leads.edit');
        Route::put('leads/{id}', [LeadsController::class, 'update'])->name('leads.update');
        Route::delete('leads/{id}', [LeadsController::class, 'destroy'])->name('leads.destroy');
        //NOTES CRUD
        Route::get('notes', [NotesController::class, 'index'])->name('notes.index');
        Route::get('notes/data', [NotesController::class, 'data'])->name('notes.data');
        Route::get('notes/create', [NotesController::class, 'create'])->name('notes.create');
        Route::post('notes', [NotesController::class, 'store'])->name('notes.store');
        Route::get('notes/{id}/edit', [NotesController::class, 'edit'])->name('notes.edit');
        Route::put('notes/{id}', [NotesController::class, 'update'])->name('notes.update');
        Route::delete('notes/{id}', [NotesController::class, 'destroy'])->name('notes.destroy');

        // TASKS CRUD
        Route::get('tasks', [TasksController::class, 'index'])->name('tasks.index');
        Route::get('tasks/data', [TasksController::class, 'data'])->name('tasks.data');
        Route::get('tasks/create', [TasksController::class, 'create'])->name('tasks.create');
        Route::post('tasks', [TasksController::class, 'store'])->name('tasks.store');
        Route::get('tasks/{id}/edit', [TasksController::class, 'edit'])->name('tasks.edit');
        Route::put('tasks/{id}', [TasksController::class, 'update'])->name('tasks.update');
        Route::delete('tasks/{id}', [TasksController::class, 'destroy'])->name('destroy');

        // USER CRUD
        Route::get('users', [UsersController::class, 'index'])->name('users.index');
        Route::get('users/data', [UsersController::class, 'data'])->name('users.data');
        Route::get('users/create', [UsersController::class, 'create'])->name('users.create');
        Route::post('users', [UsersController::class, 'store'])->name('users.store');
        Route::get('users/{id}/edit', [UsersController::class, 'edit'])->name('users.edit');
        Route::put('users/{id}', [UsersController::class, 'update'])->name('users.update');
        Route::delete('users/{id}', [UsersController::class, 'destroy'])->name('users.destroy');


    });
