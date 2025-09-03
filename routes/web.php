<?php
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\MemberItemController;
use App\Http\Controllers\DashboardMenuController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\OrderController as AdminOrderController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\TableQrController;
use App\Http\Controllers\MenuMemberController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

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


Route::get('/dashboard', function () {
    return view('dashboard');
});
    
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate'])->name('authenticate');

    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'createAccount'])->name('create.account');
});

Route::prefix('/admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::prefix('/dashboard')->group(function () {
        Route::get('/', [DashboardController::class, 'index'])
        ->name('admin.dashboard');

        Route::get('/chart-data', [DashboardController::class, 'chart'])
        ->name('admin.dashboard.chart');

        Route::get('/daily-stats', [DashboardController::class, 'dailyStats'])
        ->name('admin.dashboard.daily-stats');
    });

    Route::prefix('/category')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('admin.category.index');
        Route::post('/store', [CategoryController::class, 'store'])->name('admin.category.store');
        Route::get('/{id}/edit', [CategoryController::class, 'edit'])->name('admin.category.edit');
        Route::put('/{id}/update', [CategoryController::class, 'update'])->name('admin.category.update');
        Route::post('/{id}/delete', [CategoryController::class, 'destroy'])->name('admin.category.destroy');
    });

    Route::prefix('/menu')->group(function () {
        Route::get('/', [MenuController::class, 'index'])->name('admin.menu.index');
        Route::post('/store', [MenuController::class, 'store'])->name('admin.menu.store');
        Route::get('/{id}/edit', [MenuController::class, 'edit'])->name('admin.menu.edit');
        Route::put('/{id}/update', [MenuController::class, 'update'])->name('admin.menu.update');
        Route::post('/{id}/delete', [MenuController::class, 'destroy'])->name('admin.menu.destroy');
    });

    Route::prefix('/menu-member')->group(function () {
        Route::get('/', [MemberItemController::class, 'index'])->name('admin.menu-member.index');
        Route::post('/store', [MemberItemController::class, 'store'])->name('admin.menu-member.store');
        Route::get('/{id}/edit', [MemberItemController::class, 'edit'])->name('admin.menu-member.edit');
        Route::put('/{id}/update', [MemberItemController::class, 'update'])->name('admin.menu-member.update');
        Route::post('/{id}/delete', [MemberItemController::class, 'destroy'])->name('admin.menu-member.destroy');
    });

    Route::prefix('tables')->group(function () {
        Route::get('/', [TableController::class, 'index'])->name('admin.tables.index');
        Route::post('/', [TableController::class, 'store'])->name('admin.tables.store');
        Route::delete('/{table}', [TableController::class, 'destroy'])->name('admin.tables.destroy');
        Route::post('/{table}/status', [TableController::class, 'updateStatus'])->name('admin.tables.updateStatus');

        Route::get('/qr/print', [TableController::class, 'printAll'])->name('admin.qrs.printAll');

        Route::get('/{table}/qrs', [TableQrController::class, 'index'])->name('admin.tables.qrs.index');
        Route::post('/{table}/qrs', [TableQrController::class, 'store'])->name('admin.tables.qrs.store');
        Route::delete('/{table}/qrs/{qr}', [TableQrController::class, 'destroy'])->name('admin.tables.qrs.destroy');
        Route::get('/{table}/qr/print', [TableQrController::class, 'printTable'])->name('admin.tables.qrs.print');
    });

    Route::prefix('/user')->group(function () {
        Route::get('/', [UserController::class, 'index'])->name('admin.user');
        Route::post('/create', [UserController::class, 'store'])->name('admin.user.store');
        Route::get('/{id}/edit', [UserController::class, 'edit'])->name('admin.user.edit');
        Route::post('/{id}/update', [UserController::class, 'update'])->name('admin.user.update');
        Route::post('/{id}/delete', [UserController::class, 'destroy'])->name('admin.user.destroy');
    });

    Route::prefix('orders')->group(function () {
        Route::get('/', [AdminOrderController::class, 'index'])->name('admin.orders.index');
        Route::get('/get-data', [AdminOrderController::class, 'getOrders'])->name('admin.orders.getData');
        Route::post('/{order}/status', [AdminOrderController::class, 'updateStatus'])->name('admin.orders.updateStatus');
    });
});


Route::get('/', [DashboardMenuController::class, 'index'])->name('user.menu');

Route::prefix('/keranjang')->group(function () {
    Route::get('/', [CartController::class, 'index']);
    Route::post('/tambah', [CartController::class, 'addToCart']);
    Route::post('/update', [CartController::class, 'updateCart']);
    Route::post('/hapus', [CartController::class, 'removeCart']);
    Route::get('/total-items', [CartController::class, 'getTotalItems']);
    Route::get('/item', [CartController::class, 'getCartItem']);
    Route::post('/update-direct', [CartController::class, 'updateDirectly']);
    Route::post('/check-kode', [CartController::class, 'checkKode']);
    Route::post('/reset-kode', [CartController::class, 'resetKode']);
    Route::post('/checkout', [OrderController::class, 'store'])->name('user.order.store');
});

Route::get('/order', [OrderController::class, 'index'])->name('user.order.index');
Route::get('/order/{uuid}', [OrderController::class, 'show'])->name('user.order.show');
Route::post('/order/{uuid}', [OrderController::class, 'endOfOrder'])->name('user.order.endOfOrder');

Route::prefix('/api')->group(function () {
    Route::get('/get-menu', [DashboardMenuController::class, 'getMenus']);
});

Route::middleware(['auth','role:user'])->group(function () {
    Route::prefix('member')->group(function () {
        Route::get('/', [MenuMemberController::class, 'index'])->name('user.member.index');
        Route::get('/api/get-member-menu', [MenuMemberController::class, 'getMemberMenu']);
        Route::post('/redeem', [MenuMemberController::class, 'redeem']);
    });
});

Route::get('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'role:user'])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('user.profile');

    Route::get('/verify', [AuthController::class, 'verification'])->name('verify');
    Route::post('/verify', [AuthController::class, 'verifyOtp'])->name('verify.otp');
    Route::post('/resend-verification', [AuthController::class, 'sendVerification'])->name('resend.verification');
});
