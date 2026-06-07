<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\ReservationController;
use App\Http\Controllers\Api\CalendarSourceController;
use App\Http\Controllers\Api\ExpenseController;
use App\Http\Controllers\Api\HousekeepingController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\MaintenanceTicketController;
use App\Http\Controllers\Api\InvoiceController;

Route::post('/login', [AuthController::class, 'login']);

/*
|--------------------------------------------------------------------------
| Property Routes
|--------------------------------------------------------------------------
*/

Route::get('/properties', [PropertyController::class, 'index']);
Route::post('/properties', [PropertyController::class, 'store']);
Route::put('/properties/{id}', [PropertyController::class, 'update']);
Route::delete('/properties/{id}', [PropertyController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| Event Routes
|--------------------------------------------------------------------------
*/

Route::get('/events', [EventController::class, 'index']);
Route::post('/events', [EventController::class, 'store']);
Route::put('/events/{id}', [EventController::class, 'update']);
Route::delete('/events/{id}', [EventController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| Reservation Routes
|--------------------------------------------------------------------------
*/

Route::get('/reservations', [ReservationController::class, 'index']);
Route::post('/reservations', [ReservationController::class, 'store']);
Route::put('/reservations/{id}', [ReservationController::class, 'update']);
Route::delete('/reservations/{id}', [ReservationController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| Calendar Source Routes
|--------------------------------------------------------------------------
*/

Route::get('/calendar-sources', [CalendarSourceController::class, 'index']);
Route::post('/calendar-sources', [CalendarSourceController::class, 'store']);
Route::put('/calendar-sources/{id}', [CalendarSourceController::class, 'update']);
Route::delete('/calendar-sources/{id}', [CalendarSourceController::class, 'destroy']);
Route::post('/calendar-sources/{id}/sync', [CalendarSourceController::class, 'syncNow']);

/*
|--------------------------------------------------------------------------
| Expense Routes
|--------------------------------------------------------------------------
*/

Route::get('/expenses', [ExpenseController::class, 'index']);
Route::post('/expenses', [ExpenseController::class, 'store']);
Route::put('/expenses/{id}', [ExpenseController::class, 'update']);
Route::delete('/expenses/{id}', [ExpenseController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| Housekeeping Routes
|--------------------------------------------------------------------------
*/

Route::get('/housekeeping', [HousekeepingController::class, 'index']);
Route::post('/housekeeping', [HousekeepingController::class, 'store']);
Route::put('/housekeeping/{id}', [HousekeepingController::class, 'update']);
Route::delete('/housekeeping/{id}', [HousekeepingController::class, 'destroy']);

/*
|--------------------------------------------------------------------------
| Report Routes
|--------------------------------------------------------------------------
*/

Route::get('/reports/monthly-pdf', [ReportController::class, 'monthlyPdf']);
/*
|--------------------------------------------------------------------------
| Maintenance Ticket Routes
|--------------------------------------------------------------------------
*/

Route::get('/maintenance-tickets', [MaintenanceTicketController::class, 'index']);
Route::post('/maintenance-tickets', [MaintenanceTicketController::class, 'store']);
Route::put('/maintenance-tickets/{id}', [MaintenanceTicketController::class, 'update']);
Route::delete('/maintenance-tickets/{id}', [MaintenanceTicketController::class, 'destroy']);

Route::get('/invoices/reservations/{reservationId}/download', [InvoiceController::class, 'download']);