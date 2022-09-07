<?php

use App\Http\Controllers\Authenticate;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AjaxController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// authentication routes
Route::match(['GET', 'POST'], '/', [Authenticate::class, 'login'])->name('auth.login');
Route::match(['GET', 'POST'], '/register', [Authenticate::class, 'register'])->name('auth.register');
Route::match(['GET'], '/email/verification/{data}', [Authenticate::class, 'email_verification'])->name('auth.email_verification');
Route::match(['POST'], '/email/resend-otp', [Authenticate::class, 'email_resend_otp'])->name('auth.email_resend_otp');
Route::match(['POST'], '/registration', [Authenticate::class, 'verifyAndRegister'])->name('auth.verify_register');
Route::match(['GET', 'POST'], '/forget-password', [Authenticate::class, 'forget_password'])->name('auth.fogetPass');
// end Route



Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    Route::match(['GET'], '/logout', [Authenticate::class, 'logout'])->name('auth.logout');
    Route::get('/dashboard', function () {
        $data = [];
        $data['heading'] = 'Dashboard';
        $data['breadcrumb'] = ['Dashboard'];
        return view('admin/dashboard', compact('data'));
    })->name('dashboard')->middleware('role:all,normal_user');

    // user route
    Route::match(['GET'], '/users', [UserController::class, 'list'])->name('users.list')->middleware('role:admin');
    Route::match(['GET'], '/users/manage-role/{id}', [UserController::class, 'manage_role'])->name('users.manageRole')->middleware('role:admin');
    Route::match(['GET'], '/users/set-role/{id}/{role_id}', [UserController::class, 'set_role'])->name('users.setRole')->middleware('role:admin');
    Route::match(['GET', 'POST'], '/users/edit/{id}', [UserController::class, 'edit'])->name('users.edit')->middleware('role:admin');
    Route::match(['GET'], '/users/delete/{id}', [UserController::class, 'delete'])->name('users.delete')->middleware('role:admin');
    Route::match(['GET'], '/users/change-status/{id}/{status}', [UserController::class, 'change_status'])->name('users.changeStatus')->middleware('role:admin');
    // end user route

    Route::match(['GET'], '/profile', [UserController::class, 'profile'])->name('profile')->middleware('role:all,normal_user');
    Route::match(['GET', 'POST'], '/profile/edit', [UserController::class, 'profile_edit'])->name('profile.edit')->middleware('role:all,normal_user');
	
	Route::prefix('pos')->name('pos.')->group(function () {
		Route::match(['GET'], '/pos_type', [PurchaseOrderController::class, 'pos_type'])->name('pos_type');
		Route::match(['GET'], '/pos_payment_method', [PurchaseOrderController::class, 'pos_payment_method'])->name('pos_payment_method');
		
		
		Route::match(['GET'], '/create_order', [PurchaseOrderController::class, 'pos_create'])->name('pos_create');
        /*Route::match(['GET', 'POST'], '/list', [CustomerController::class, 'list'])->name('list');
        Route::match(['GET', 'POST'], '/edit/{id}', [CustomerController::class, 'edit'])->name('edit');
        Route::match(['GET', 'POST'], '/delete/{id}', [CustomerController::class, 'delete'])->name('delete');*/
	});
	
	Route::prefix('customer')->name('customer.')->group(function () {
		Route::match(['GET', 'POST'], '/add', [CustomerController::class, 'add'])->name('add');
        Route::match(['GET', 'POST'], '/list', [CustomerController::class, 'list'])->name('list');
        Route::match(['GET', 'POST'], '/edit/{id}', [CustomerController::class, 'edit'])->name('edit');
        Route::match(['GET', 'POST'], '/delete/{id}', [CustomerController::class, 'delete'])->name('delete');
	});
	
	Route::prefix('supplier')->name('supplier.')->group(function () {
		Route::match(['GET', 'POST'], '/add', [SupplierController::class, 'add'])->name('add');
        Route::match(['GET', 'POST'], '/list', [SupplierController::class, 'list'])->name('list');
        Route::match(['GET', 'POST'], '/edit/{id}', [SupplierController::class, 'edit'])->name('edit');
        Route::match(['GET', 'POST'], '/delete/{id}', [SupplierController::class, 'delete'])->name('delete');
	});
	
	Route::prefix('product')->name('product.')->group(function () {
		Route::match(['GET', 'POST'], '/add', [ProductController::class, 'add'])->name('add');
		Route::match(['GET', 'POST'], '/product_upload', [ProductController::class, 'product_upload'])->name('product_upload');
        Route::match(['GET', 'POST'], '/list', [ProductController::class, 'list'])->name('list');
        Route::match(['GET', 'POST'], '/edit/{id}', [ProductController::class, 'edit'])->name('edit');
        Route::match(['GET', 'POST'], '/delete/{id}', [ProductController::class, 'delete'])->name('delete');
	});
	
	Route::prefix('report')->name('report.')->group(function () {
		Route::match(['GET'], '/sales', [ReportController::class, 'sales'])->name('sales');
		Route::prefix('invoice')->name('invoice.')->group(function () {
			Route::match(['GET'], '/report', [ReportController::class, 'invoice_report'])->name('invoice_report');
		});
		
		
		
		
		Route::match(['GET'], '/purchase', [ReportController::class, 'purchase'])->name('purchase');
		Route::match(['GET'], '/inventory', [ReportController::class, 'inventory'])->name('inventory');
		Route::match(['GET'], '/reminders', [ReportController::class, 'reminders'])->name('reminders');
		
        //Route::match(['GET', 'POST'], '/list', [ProductController::class, 'list'])->name('list');
        //Route::match(['GET', 'POST'], '/edit/{id}', [ProductController::class, 'edit'])->name('edit');
        //Route::match(['GET', 'POST'], '/delete/{id}', [ProductController::class, 'delete'])->name('delete');
	});
	
	Route::prefix('purchase')->name('purchase.')->group(function () {
		Route::match(['GET', 'POST'], '/invoice_upload', [PurchaseOrderController::class, 'invoice_upload'])->name('invoice_upload');
		
		Route::match(['GET', 'POST'], '/inward_stock', [PurchaseOrderController::class, 'create_order'])->name('inward_stock');
        Route::match(['GET', 'POST'], '/material_inward', [PurchaseOrderController::class, 'material_inward'])->name('material_inward');
		Route::match(['GET', 'POST'], '/supplier_bill', [PurchaseOrderController::class, 'supplier_bill'])->name('supplier_bill');
        Route::match(['GET', 'POST'], '/debitnote', [PurchaseOrderController::class, 'debitnote'])->name('debitnote');
	});
	
	
	
	
	
});


// Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/ajaxpost', [App\Http\Controllers\AjaxController::class, 'ajaxpost']);
Route::post('/ajaxpost', [App\Http\Controllers\AjaxController::class, 'ajaxpost']);