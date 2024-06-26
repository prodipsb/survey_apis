<?php

use App\Http\Controllers\AccessController;
use App\Http\Controllers\ArchivedSurveyController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BINInformationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DevController;
use App\Http\Controllers\DeviceController;
use App\Http\Controllers\DeviceServiceController;
use App\Http\Controllers\DeviceServiceIssueController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PushNotificationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('guest')->prefix('v1/')->group(function () {

    // Route::post('refresh-token', 'AuthController@refreshToken')->name('refreshToken');
    Route::post('auth-login', [AuthController::class, 'authlogin'])->name('auth.login');
});


Route::middleware('auth:api')->prefix('v1/')->group(function () {


    //========= Roles Routes ==========

    Route::post('role-create', [AccessController::class, 'roleCreate']);
    Route::get('roles', [AccessController::class, 'getRoles']);
    Route::post('role-delete', [AccessController::class, 'deleteRole']);
    Route::post('set-role', [AccessController::class, 'setRole']);
    Route::get('role/users', [AccessController::class, 'showUsersByRole']);
    Route::get('users/exclude-role', [AccessController::class, 'getExcludeRoleUsers']);
    Route::post('user/role-remove', [AccessController::class, 'removeRoleFromUser']);
    Route::get('roles/upper-roles', [AccessController::class, 'getUpperRoles']);
    Route::get('role-devices', [AccessController::class, 'getRoleDevices']);


    //========= Permissions Routes ==========

    Route::post('permission-create', [AccessController::class, 'permissionCreate']);
    Route::get('permissions', [AccessController::class, 'allPermission']);
    Route::post('permission-delete', [AccessController::class, 'deletePermission']);
    Route::post('set-permission', [AccessController::class, 'setPermission']);
    Route::get('user-permissions', [AccessController::class, 'userInformationWithPermission']);
    Route::post('remove-permission', [AccessController::class, 'removePermissionFromUser']);
    Route::post('role/remove-permissions', [AccessController::class, 'removePermissionFromRole']);
    Route::get('permission-by-role', [AccessController::class, 'roleViaPermission']);


    //========= User Routes ==========

    Route::post('user-create', [UserController::class, 'createUser']);
    Route::get('users', [UserController::class, 'index']);
    Route::get('all-users', [UserController::class, 'getUsers']);
    Route::get('user/{id}', [UserController::class, 'getUser']);
    Route::post('user-delete', [UserController::class, 'deleteUser']);
    Route::get('profile', [UserController::class, 'userProfile'])->name('user.profile');
    Route::post('password-update', [UserController::class, 'passwordUpdate'])->name('password.update');
    Route::post('avatar-update', [UserController::class, 'userAvatarUpdate'])->name('user.avatar');
    Route::post('user-status', [UserController::class, 'updateUserStatus'])->name('user.status');
    Route::get('get-supervise-users', [UserController::class, 'getSuperviseUsers'])->name('supervise.users');
    Route::get('get-supervise-users/list', [UserController::class, 'getSuperviseUsersList'])->name('supervise.users.list');


    Route::post('user-import', [UserController::class, 'importUser']);

    // $router->get('/demo-file-export', 'ReportController@demoFileExport');



    //========= Survey Routes ==========

    Route::post('survey-submission', [SurveyController::class, 'surveySubmission'])->name('survey.submission');
    Route::get('survey/{id}', [SurveyController::class, 'getSurvey']);
    Route::get('surveys', [SurveyController::class, 'index']);
    Route::post('survey-update', [SurveyController::class, 'surveyUpdate'])->name('survey.update');
    Route::post('survey-delete', [SurveyController::class, 'surveyDelete'])->name('survey.delete');
    Route::get('check-bin-existence', [SurveyController::class, 'checkBinNumberExistance'])->name('bin.existence');


    //========= Archived Survey Routes ==========
    Route::get('archived-surveys', [ArchivedSurveyController::class, 'archivedSurveys'])->name('surveys.archived');



    //========= Attandance Routes ==========
    Route::get('attendances', [AttendanceController::class, 'index'])->name('attendances');
    Route::post('attendance', [AttendanceController::class, 'storeAttendance'])->name('store.attendance');
    Route::get('user-current-attendance', [AttendanceController::class, 'getUserCurrentAttendance'])->name('user.currect.attendance');


    //========= Device Service Issue Routes ==========
    Route::resource('device-service-issue', DeviceServiceIssueController::class);
    Route::post('device-service-issue/{id}/update', [DeviceServiceIssueController::class, 'update'])->name('device.service.issue.update');
    Route::post('device-service-issue/{id}/delete', [DeviceServiceIssueController::class, 'destroy'])->name('device.service.issue.destroy');

    //========= Device Service Routes ==========
    Route::resource('device-services', DeviceServiceController::class);
    Route::post('device-services/{id}/update', [DeviceServiceController::class, 'update'])->name('device.service.update');
    Route::post('device-services/{id}/delete', [DeviceServiceController::class, 'destroy'])->name('device.service.destroy');

    Route::get('device-services/ao/devices', [DeviceServiceController::class, 'getUserDeviceServices'])->name('device.service.ao.devices');
    Route::get('device-services/ao/ready-devices', [DeviceServiceController::class, 'readyDevicesForAO'])->name('device.service.ao.ready.devices');
    Route::post('device-services/ao/device-delivered', [DeviceServiceController::class, 'deliveredDevice'])->name('delivered.device');

   
   


    // ========= Archive Device Information ===========
    Route::get('import-device/information', [BINInformationController::class, 'importExcelForDeviceInformation'])->name('import.device.information');
    Route::get('bin-number/details', [BINInformationController::class, 'getBinNumberDetails'])->name('bin.number.details');




    //========= Setting Routes ==========

    Route::post('general-settings', [SettingController::class, 'generalSettingStore'])->name('setting.store');
    Route::get('general-setting', [SettingController::class, 'index'])->name('settings');
    Route::get('logo', [SettingController::class, 'getLogo'])->name('web.logo');

    //========= Dashboard Routes ==========

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');


    //========= Report Routes ==========

    Route::get('master-report', [ReportController::class, 'masterReport'])->name('report.master');
    Route::get('performance-report', [ReportController::class, 'performaceReport'])->name('report.performace');
    Route::get('attendance-report', [ReportController::class, 'attendanceReport'])->name('report.attendance');



    //========= Notification Routes ==========

    Route::get('notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::get('notifications-unread', [NotificationController::class, 'unreadNotifications'])->name('notifications.read');
    Route::get('notification/{id}', [NotificationController::class, 'show'])->name('notification.show');
    Route::post('notification-read', [NotificationController::class, 'read'])->name('notification.read');
    Route::post('notifications-as-read', [NotificationController::class, 'allNotificationRead'])->name('all.notification.read');
    Route::post('notification-delete', [NotificationController::class, 'destroy'])->name('notification.destroy');
    Route::post('notifications-delete', [NotificationController::class, 'allDestroy'])->name('notification.all.destroy');

    // Route::post('push-notification/send', [NotificationController::class, 'pushNotificationSend'])->name('push.notification.send');
    Route::post('store/device-tokens', [DeviceController::class, 'storeDeviceToken'])->name('store.device.token');
    Route::get('device-tokens', [DeviceController::class, 'getDeviceTokens'])->name('device.tokens');
    Route::post('delete/device-token', [DeviceController::class, 'deleteDeviceToken'])->name('delete.device.token');


    //========= Push Notification Routes ==========
    Route::get('push-notifications', [PushNotificationController::class, 'index'])->name('push.notifications');
    Route::get('unread-push-notifications', [PushNotificationController::class, 'getUnReadPushNotifications'])->name('unread.push.notifications');
    Route::post('push-notification-create', [PushNotificationController::class, 'pushNotificationSend'])->name('push.notification.send');
    Route::post('push-notification-delete', [PushNotificationController::class, 'destroy'])->name('push.notification.destroy');




    Route::post('general-setting-update', [AccessController::class, 'update'])->name('setting.update');


    Route::post('logout', [AuthController::class, 'logout'])->name('user.logout');


    //========= Dev Routes ==========
    Route::get('update-devices', [DevController::class, 'index'])->name('devices.update');
    Route::get('check-bin', [DevController::class, 'checkBinNumber'])->name('check.bin');
});


//========= Dev Routes ==========
Route::middleware('guest')->prefix('v1/')->group(function () {
    Route::get('test-excel', [DevController::class, 'excelUpload'])->name('excel.upload');
    Route::get('geo-location', [LocationController::class, 'getLocationName'])->name('geo.location');

    Route::get('update-archive-survey', [DevController::class, 'updateArchiveSurvey'])->name('update.archive');
});
