<?php

// Import necessary classes for routing
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DownloadController;

use App\Http\Controllers\PageAddons;

// Route to handle password-protected downloads display via AJAX
// This POST route is triggered when a user provides a password to check for access to downloadable resources.
// It sends the request to the 'showDownloads' method in the DownloadController.
Route::post('/check-password-and-get-downloads', [DownloadController::class, 'showDownloads'])->name('checkPasswordAndGetDownloads');

// Route to display the downloads page for admins.
// This GET route returns the 'admin' view when the '/admin/downloads' URL is accessed.
// This route is meant for administrators to view a downloads page (if using a separate view).
Route::get('/admin/downloads', function () {
    return view('admin');
});

// Home route displaying the downloads index page.
// This GET route calls the 'index' method in the DownloadController to load the default view for downloads.
Route::get('/', [DownloadController::class, 'index']);

// Route to handle file download requests.
// This POST route accepts file download requests and triggers the 'download' method in DownloadController.
Route::post('/download', [DownloadController::class, 'download']);

// Route to handle video download requests.
// This POST route specifically handles video downloads by triggering the 'downloadVideo' method in the DownloadController.
Route::post('/downloadVideo', [DownloadController::class, 'downloadVideo']);

// Route to serve a downloadable file via URL parameter.
// This GET route uses a dynamic segment `{file_name}` to retrieve a specific file.
// The 'getFile' method in DownloadController handles the actual file serving logic.
Route::get('/download-file/{file_name}', [DownloadController::class, 'getFile'])->name('download.file');

// Route to display the FAQ page.
// This GET route invokes the 'faq' method from the PageAddons controller to render the FAQ page.
Route::get('/faq', [PageAddons::class, 'faq']);

// Route to display the Terms & Conditions (T&C) page.
// This GET route uses the 'terms' method in the PageAddons controller to show the T&C page.
Route::get('/t&c', [PageAddons::class, 'terms']);

// Route to display the visits page.
// This GET route directly returns the 'visits' view when the '/visits' URL is accessed.
Route::get('/visits', function () {
    return view('visits');
});

// Route to handle password-protected access to visit records.
// This POST route is triggered when a user submits a password to view certain visit records.
// The 'checkPasswordAndGetVisits' method in the DownloadController checks the password and returns the visit data.
Route::post('/check-password-and-get-visits', [DownloadController::class, 'checkPasswordAndGetVisits'])->name('checkPasswordAndGetVisits');
