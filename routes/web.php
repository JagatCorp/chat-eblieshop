<?php
use App\Http\Controllers\MessagesController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
 */

Route::redirect('/', '/chatify');
// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::post('/member', [MessagesController::class, 'addMemberToGroup'])->name('member');
Route::post('/delete-member', [MessagesController::class, 'deleteUserFromChannel'])->name('delete-member');
 Route::post('/update-owner', [MessagesController::class, 'updateOwner'])->name('update.owner');


Route::get('storage-link', function () {
    Artisan::call('storage:link');
});


require __DIR__ . '/auth.php';
