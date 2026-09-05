<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\BusinessSettingsController;
use App\Http\Controllers\IndustryController;
use App\Http\Controllers\TechnologyController;
use App\Http\Controllers\ProjectTypesController;
use App\Http\Controllers\EnquirySourcesController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DataController;
use App\Http\Controllers\EnquiryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\EnquiryFollowupController;
use App\Http\Controllers\EnquiryScopeOfWorkController;

Route::group(['middleware' => ['guest']], function () {
    Route::get('login', [AuthController::class, 'showLoginForm'])->name('admin.login');
    Route::post('login', [AuthController::class, 'login']);
});

// Route::get('/', [AuthController::class, 'showLoginForm'])->name('home');
Route::get('logout', [AuthController::class, 'logout'])->name('admin.logout');

Route::group(['middleware' => ['auth']], function () {
    Route::get('/dailymail', [AdminController::class, 'dailyFollowupMail'])->name('dailymail');

    Route::get('/', [AdminController::class, 'admin_dashboard'])->name('admin.dashboard');
    Route::get('/cache-cache', [AdminController::class, 'clearCache'])->name('cache.clear');

    Route::resource('roles', RoleController::class);
    Route::get('/roles/edit/{id}', [RoleController::class, 'edit'])->name('roles.edit');
    Route::get('/roles/destroy/{id}', [RoleController::class, 'destroy'])->name('roles.destroy');

    Route::resource('staffs', StaffController::class);
    Route::get('/staffs/destroy/{id}', [StaffController::class, 'destroy'])->name('staffs.destroy');
    Route::post('/staff/status', [StaffController::class, 'updateStatus'])->name('staff.status');
    Route::post('/staff/mail-status', [StaffController::class, 'updateFollowupMailStatus'])->name('staff.mail-status');
    Route::post('/staff/bypass-hierarchy', [StaffController::class, 'updateBypassHierarchy'])->name('staff.bypass-hierarchy');

    // Manage Imdustries
    Route::get('/industries', [IndustryController::class, 'index'])->name('industries.index');
    Route::post('/industries/store', [IndustryController::class, 'store'])->name('industries.store');
    Route::post('/industries/update/{id}', [IndustryController::class, 'update'])->name('industries.update');
    Route::post('/industries/status', [IndustryController::class, 'updateStatus'])->name('industries.status');
    Route::get('/industries/delete/{id}', [IndustryController::class, 'destroy'])->name('industries.destroy');


    Route::post('/business-settings/update', [BusinessSettingsController::class, 'update'])->name('business_settings.update');

    // Manage Technologies
    Route::get('/technologies', [TechnologyController::class, 'index'])->name('technologies.index');
    Route::post('/technologies/store', [TechnologyController::class, 'store'])->name('technologies.store');
    Route::post('/technologies/update/{id}', [TechnologyController::class, 'update'])->name('technologies.update');
    Route::post('/technologies/status', [TechnologyController::class, 'updateStatus'])->name('technologies.status');
    Route::get('/technologies/delete/{id}', [TechnologyController::class, 'destroy'])->name('technologies.destroy');

    // Manage Project Categories
    Route::get('/project-categories', [ProjectTypesController::class, 'index'])->name('project_category.index');
    Route::post('/project_category/store', [ProjectTypesController::class, 'store'])->name('project_category.store');
    Route::post('/project_category/update/{id}', [ProjectTypesController::class, 'update'])->name('project_category.update');
    Route::post('/project_category/status', [ProjectTypesController::class, 'updateStatus'])->name('project_category.status');
    Route::get('/project_category/delete/{id}', [ProjectTypesController::class, 'destroy'])->name('project_category.destroy');

    // Manage Enquiry Sources
    Route::get('/enquiry-sources', [EnquirySourcesController::class, 'index'])->name('enquiry_sources.index');
    Route::post('/enquiry-sources/store', [EnquirySourcesController::class, 'store'])->name('enquiry_sources.store');
    Route::post('/enquiry-sources/update/{id}', [EnquirySourcesController::class, 'update'])->name('enquiry_sources.update');
    Route::post('/enquiry-sources/status', [EnquirySourcesController::class, 'updateStatus'])->name('enquiry_sources.status');
    Route::get('/enquiry-sources/delete/{id}', [EnquirySourcesController::class, 'destroy'])->name('enquiry_sources.destroy');

    Route::resource('data', DataController::class);
    Route::post('/data/change-status', [DataController::class, 'changeStatus'])->name('data.changeStatus');
    Route::get('/data/{id}', [DataController::class, 'show'])->name('data.show');
    Route::get('/data-details/{id}/{status}', [DataController::class, 'getStatusData']);
    Route::get('/data-import', [DataController::class, 'importData'])->name('data-import.index');
    Route::post('/data/import', [DataController::class, 'import'])->name('data.import');
    Route::get('/data/{id}/timeline', [DataController::class, 'timeline'])->name('data.timeline');


    Route::resource('customers', CustomerController::class);
    Route::post('/customer/status', [CustomerController::class, 'updateStatus'])->name('customer.status');
    Route::get('/customers/{id}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customer/export', [CustomerController::class, 'export'])->name('customers.export');

    // Manage Enquiries
    Route::resource('enquiries', EnquiryController::class);
    Route::post('/enquiries/change-status', [EnquiryController::class, 'changeStatus'])->name('enquiries.changeStatus');
    Route::get('/enquiries/{id}/proposal-items/{status}', [EnquiryController::class, 'getProposalItems']);

    Route::get('/enquiry-scopes', [EnquiryScopeOfWorkController::class, 'index'])->name('enquiry-scopes.index');
    Route::get('/enquiry-scopes/{id}', [EnquiryScopeOfWorkController::class, 'show'])->name('enquiry-scopes.show');
    Route::put('/enquiry-scopes/{id}', [EnquiryScopeOfWorkController::class, 'update'])->name('enquiry-scopes.update');
    Route::post('/enquiry-scopes/{scope}/comments', [EnquiryScopeOfWorkController::class, 'storeComment'])->name('enquiry-scopes.comments.store');



    Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
    Route::get('/project/create', [ProjectController::class, 'create'])->name('projects.create');
    Route::post('/project/store', [ProjectController::class, 'store'])->name('projects.store');
    Route::get('/project/{id}', [ProjectController::class, 'show'])->name('projects.show');
    Route::get('/project/{id}/edit', [ProjectController::class, 'edit'])->name('projects.edit');
    Route::post('project/{id}/update', [ProjectController::class, 'update'])->name('projects.update');
    

    Route::get('/get-enquiries/{customerId}', [ProjectController::class, 'getEnquiries'])->name('get.enquiries');

    // Manage Followups
    Route::get('/followups', [EnquiryFollowupController::class, 'index'])->name('followups.index'); // list
    Route::get('/followups/create/{enquiry_id?}', [EnquiryFollowupController::class, 'create'])->name('followups.create');
    Route::post('/followups/store', [EnquiryFollowupController::class, 'store'])->name('followups.store');
    Route::get('/followups/{id}/edit', [EnquiryFollowupController::class, 'edit'])->name('followups.edit');
    Route::put('/followups/{id}', [EnquiryFollowupController::class, 'update'])->name('followups.update');
    Route::post('/followups/{id}/update-status', [EnquiryFollowupController::class, 'updateStatus']);


    Route::get('/followups/calendar', [EnquiryFollowupController::class, 'calendar'])->name('followups.calendar');
    Route::get('/followups/calendar/events', [EnquiryFollowupController::class, 'calendarEvents'])->name('followups.calendar.events');

});

