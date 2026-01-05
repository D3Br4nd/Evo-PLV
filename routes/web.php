<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminProfileController;
use App\Http\Controllers\AdminMemberInvitationController;
use App\Http\Controllers\MemberInvitationAcceptController;
use App\Http\Controllers\MemberCardController;
use App\Http\Controllers\MemberOnboardingController;
use App\Http\Controllers\MemberPasswordController;
use App\Http\Controllers\MemberPushSubscriptionController;
use App\Http\Controllers\PublicContentPageController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        $isAdmin = in_array($user->role, ['super_admin', 'direzione', 'segreteria'], true);

        if ($isAdmin) {
            return redirect('/admin/dashboard');
        }

        return redirect($user->must_set_password ? '/me/onboarding' : '/me/card');
    }

    return Inertia::render('Welcome');
})->name('home');

Route::middleware('auth')->get('/me/card', [MemberCardController::class, 'show'])->name('member.card');
Route::middleware('auth')->get('/me/onboarding', [MemberOnboardingController::class, 'show'])->name('member.onboarding');
Route::middleware('auth')->patch('/me/password', [MemberPasswordController::class, 'update'])->name('member.password.update');
Route::middleware('auth')->post('/me/push-subscriptions', [MemberPushSubscriptionController::class, 'store'])->name('member.push-subscriptions.store');
Route::middleware('auth')->delete('/me/push-subscriptions', [MemberPushSubscriptionController::class, 'destroy'])->name('member.push-subscriptions.destroy');

// One-time invitation link (public)
Route::get('/invite/{token}', [MemberInvitationAcceptController::class, 'show'])->name('invite.accept');
Route::post('/invite/{token}', [MemberInvitationAcceptController::class, 'store'])->name('invite.store');

// Public content pages (published)
Route::get('/p/{slug}', [PublicContentPageController::class, 'show'])->name('public.page');

// Public legal pages
Route::get('/privacy-policy', function () {
    return Inertia::render('Public/PrivacyPolicy');
})->name('privacy.policy');

Route::get('/cookie-policy', function () {
    return Inertia::render('Public/CookiePolicy');
})->name('cookie.policy');

// Authentication routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Admin routes (protected)
Route::middleware(['auth', 'role:super_admin,direzione,segreteria'])->prefix('admin')->group(function () {
    Route::get('/dashboard', AdminDashboardController::class)->name('admin.dashboard');

    Route::get('/profile', [AdminProfileController::class, 'edit'])->name('admin.profile.edit');
    Route::patch('/profile', [AdminProfileController::class, 'update'])->name('admin.profile.update');
    Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('admin.profile.password');

    Route::resource('members', \App\Http\Controllers\AdminMemberController::class);
    Route::post('members/{member}/invite', [AdminMemberInvitationController::class, 'store'])
        ->name('members.invite.store');
    Route::patch('members/{member}/role', \App\Http\Controllers\AdminMemberRoleController::class.'@update')
        ->name('members.role.update')
        ->middleware('role:super_admin');
    Route::resource('events', \App\Http\Controllers\AdminEventController::class);
    Route::get('events/{event}/checkins', [\App\Http\Controllers\AdminEventCheckinController::class, 'index'])
        ->name('events.checkins.index');
    Route::post('events/{event}/checkins', [\App\Http\Controllers\AdminEventCheckinController::class, 'store'])
        ->name('events.checkins.store');
    Route::get('events/{event}/checkins/export', [\App\Http\Controllers\AdminEventCheckinController::class, 'exportCsv'])
        ->name('events.checkins.export');
    Route::resource('projects', \App\Http\Controllers\AdminProjectController::class);
    Route::resource('content-pages', \App\Http\Controllers\AdminContentPageController::class)
        ->only(['index', 'store', 'update', 'destroy']);
});
