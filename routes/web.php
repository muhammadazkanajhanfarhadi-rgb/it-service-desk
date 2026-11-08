<?php

use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\KnowledgeArticleController as AdminKnowledgeArticleController;
use App\Http\Controllers\Admin\ServiceController as AdminServiceController;
use App\Http\Controllers\Admin\SlaPolicyController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\KnowledgeBaseController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ServiceCatalogController;
use App\Http\Controllers\ServiceRequestController;
use App\Http\Controllers\SlaMonitoringController;
use App\Http\Controllers\TicketController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes — IT Service Desk
|--------------------------------------------------------------------------
*/

// Redirect root to login or dashboard
Route::get('/', function () {
    return auth()->check()
        ? redirect()->route('dashboard')
        : redirect()->route('login');
});

// Dashboard (role-based)
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ── Tickets ──────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Employee: my tickets
    Route::get('/my-tickets', [TicketController::class, 'myTickets'])
         ->name('tickets.my');

    // All tickets (admin/manager/technician)
    Route::get('/tickets', [TicketController::class, 'index'])
         ->name('tickets.index');

    // Create
    Route::get('/tickets/create', [TicketController::class, 'create'])
         ->name('tickets.create');
    Route::post('/tickets', [TicketController::class, 'store'])
         ->name('tickets.store');

    // Show
    Route::get('/tickets/{ticket}', [TicketController::class, 'show'])
         ->name('tickets.show');

    // Actions
    Route::patch('/tickets/{ticket}/status', [TicketController::class, 'updateStatus'])
         ->name('tickets.status');
    Route::patch('/tickets/{ticket}/assign', [TicketController::class, 'assign'])
         ->name('tickets.assign');
    Route::post('/tickets/{ticket}/comments', [TicketController::class, 'addComment'])
         ->name('tickets.comments.store');
});

// ── Service Catalog ───────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    // Assets (all authenticated users can view; admin can CRUD)
    Route::get('/assets',                      [AssetController::class, 'index'])           ->name('assets.index');
    Route::get('/assets/create',               [AssetController::class, 'create'])         ->name('assets.create');
    Route::post('/assets',                     [AssetController::class, 'store'])          ->name('assets.store');
    Route::get('/assets/{asset}',              [AssetController::class, 'show'])           ->name('assets.show');
    Route::get('/assets/{asset}/edit',         [AssetController::class, 'edit'])            ->name('assets.edit');
    Route::patch('/assets/{asset}',            [AssetController::class, 'update'])         ->name('assets.update');
    Route::delete('/assets/{asset}',           [AssetController::class, 'destroy'])        ->name('assets.destroy');
    Route::patch('/assets/{asset}/assign',     [AssetController::class, 'assign'])         ->name('assets.assign');
    Route::patch('/assets/{asset}/return',     [AssetController::class, 'returnAsset'])     ->name('assets.return');
    Route::post('/assets/{asset}/maintenance', [AssetController::class, 'storeMaintenance'])->name('assets.maintenance');

    // Knowledge Base (all authenticated users can read published)
    Route::get('/knowledge-base',                       [KnowledgeBaseController::class, 'index'])  ->name('knowledge-base.index');
    Route::get('/knowledge-base/{article}',              [KnowledgeBaseController::class, 'show'])   ->name('knowledge-base.show');
    Route::post('/knowledge-base/{article}/feedback',    [KnowledgeBaseController::class, 'feedback'])->name('knowledge-base.feedback');

    Route::get('/service-catalog',                [ServiceCatalogController::class, 'index'])  ->name('service-catalog.index');
    Route::get('/service-catalog/{service}',       [ServiceCatalogController::class, 'show'])  ->name('service-catalog.show');

    // Service Requests (Employee)
    Route::get('/my-requests',                      [ServiceRequestController::class, 'myRequests'])    ->name('service-requests.my');
    Route::get('/service-catalog/{service}/request',[ServiceRequestController::class, 'create'])       ->name('service-requests.create');
    Route::post('/service-catalog/{service}/request',[ServiceRequestController::class, 'store'])       ->name('service-requests.store');
    Route::patch('/service-requests/{serviceRequest}/cancel', [ServiceRequestController::class, 'cancel'])->name('service-requests.cancel');

    // Approval (Manager/Approver)
    Route::get('/approvals',                        [ServiceRequestController::class, 'pendingApprovals'])->name('service-requests.approvals');
    Route::patch('/service-requests/{serviceRequest}/approve', [ServiceRequestController::class, 'approve'])->name('service-requests.approve');
    Route::patch('/service-requests/{serviceRequest}/reject',  [ServiceRequestController::class, 'reject']) ->name('service-requests.reject');
    Route::patch('/service-requests/{serviceRequest}/status',  [ServiceRequestController::class, 'updateStatus'])->name('service-requests.status');
});

// ── SLA Monitoring ──────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/sla-monitoring', [SlaMonitoringController::class, 'index'])
         ->name('sla.monitoring');
});

// ── Reports (Manager & Admin only) ───────────────────────────────────────
Route::middleware(['auth'])->group(function () {
    Route::get('/reports', [ReportController::class, 'index'])
         ->middleware('can:manage')
         ->name('reports.index');
});

// ── Profile ───────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Notifications ─────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {
    Route::get('/notifications',           [NotificationController::class, 'index'])      ->name('notifications.index');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])    ->name('notifications.read-all');
    Route::get('/notifications/count',     [NotificationController::class, 'unreadCount'])->name('notifications.count');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'read'])       ->name('notifications.read');
});

require __DIR__.'/auth.php';

// ── Admin Panel (Admin only) ─────────────────────────────────────────────
Route::middleware(['auth', 'can:admin'])->prefix('admin')->name('admin.')->group(function () {
    // Users
    Route::get('users',                        [AdminUserController::class, 'index'])         ->name('users.index');
    Route::get('users/create',                  [AdminUserController::class, 'create'])       ->name('users.create');
    Route::post('users',                        [AdminUserController::class, 'store'])        ->name('users.store');
    Route::get('users/{user}/edit',             [AdminUserController::class, 'edit'])         ->name('users.edit');
    Route::patch('users/{user}',               [AdminUserController::class, 'update'])       ->name('users.update');
    Route::patch('users/{user}/toggle-active', [AdminUserController::class, 'toggleActive']) ->name('users.toggle-active');
    Route::patch('users/{user}/reset-password',[AdminUserController::class, 'resetPassword'])->name('users.reset-password');

    // Categories
    Route::get('categories',           [CategoryController::class, 'index'])  ->name('categories.index');
    Route::post('categories',          [CategoryController::class, 'store'])  ->name('categories.store');
    Route::patch('categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

    // SLA Policies
    Route::get('sla-policies',                 [SlaPolicyController::class, 'index'])  ->name('sla-policies.index');
    Route::patch('sla-policies/{slaPolicy}',   [SlaPolicyController::class, 'update']) ->name('sla-policies.update');

    // Services (Admin CRUD)
    Route::get('services',             [AdminServiceController::class, 'index'])  ->name('services.index');
    Route::post('services',            [AdminServiceController::class, 'store'])  ->name('services.store');
    Route::patch('services/{service}', [AdminServiceController::class, 'update']) ->name('services.update');
    Route::delete('services/{service}',[AdminServiceController::class, 'destroy'])->name('services.destroy');

    // Audit Log
    Route::get('audit-log', [AuditLogController::class, 'index'])->name('audit-log.index');
});

// ── Knowledge Articles Admin (Admin + Manager can manage) ─────────────────
Route::middleware(['auth', 'can:manage'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('knowledge-articles',                 [AdminKnowledgeArticleController::class, 'index'])  ->name('knowledge-articles.index');
    Route::get('knowledge-articles/create',          [AdminKnowledgeArticleController::class, 'create']) ->name('knowledge-articles.create');
    Route::post('knowledge-articles',                 [AdminKnowledgeArticleController::class, 'store'])  ->name('knowledge-articles.store');
    Route::get('knowledge-articles/{article}/edit',   [AdminKnowledgeArticleController::class, 'edit'])   ->name('knowledge-articles.edit');
    Route::patch('knowledge-articles/{article}',      [AdminKnowledgeArticleController::class, 'update']) ->name('knowledge-articles.update');
    Route::delete('knowledge-articles/{article}',    [AdminKnowledgeArticleController::class, 'destroy'])->name('knowledge-articles.destroy');
});
