<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'redirect_halaman_utama';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;

// Auth Routes (letakkan auth/login SEBELUM auth/(:any) — jika tidak, auth/login tertangkap ke Login::login() yang tidak ada → 404)
$route['auth'] = 'auth/login';
$route['auth/login'] = 'auth/login';
$route['auth/(:any)'] = 'auth/login/$1';

// API Routes
$route['api/reservation'] = 'api/reservation';
$route['api/reservation/(:any)'] = 'api/reservation/$1';

// Attendance API Routes
$route['api/attendance/settings'] = 'api/attendance/settings';
$route['api/attendance/check_radius'] = 'api/attendance/check_radius';
$route['api/attendance/save'] = 'api/attendance/save';
$route['api/attendance/checkout'] = 'api/attendance/checkout';
$route['api/attendance/history'] = 'api/attendance/history';
$route['api/attendance/schedule_today'] = 'api/attendance/schedule_today';
$route['api/attendance/sync_alpha'] = 'api/attendance/sync_alpha';
$route['api/attendance/(:any)'] = 'api/attendance/$1';

// Reservation Acceptance API Routes
$route['user/gro/reservation_acceptanced/get_details/(:num)'] = 'user/gro/reservation_acceptanced/get_details/$1';

// KPI Routes
$route['user/big_data/kpi_assessment'] = 'user/big_data/kpi_assessment';
$route['user/big_data/kpi_save'] = 'user/big_data/kpi_save';
$route['user/big_data/kpi_delete/(:num)'] = 'user/big_data/kpi_delete/$1';

// Attendance Routes
$route['user/big_data/attendance_settings'] = 'user/big_data/attendance_settings';
$route['user/big_data/attendance_archive'] = 'user/big_data/attendance_archive';
$route['user/big_data/attendance_sync_alpha'] = 'user/big_data/attendance_sync_alpha';
$route['user/big_data/attendance_delete/(:num)'] = 'user/big_data/attendance_delete/$1';
$route['user/big_data/employee_save'] = 'user/big_data/employee_save';
$route['user/big_data/employee_delete/(:num)'] = 'user/big_data/employee_delete/$1';
$route['user/big_data/data_reset'] = 'user/big_data/data_reset';
$route['user/big_data/event_delete/(:any)'] = 'user/big_data/event_delete/$1';

// Class Management Routes
$route['class_management'] = 'class_management';
$route['class_management/(:any)'] = 'class_management/$1';

// Class Schedule API Routes
$route['api/class_schedule'] = 'api/class_schedule';
$route['api/class_schedule/(:any)'] = 'api/class_schedule/$1';

// GSC Package API Routes (scan paket + package manage)
$route['api/gsc_package'] = 'api/gsc_package';
$route['api/gsc_package/(:any)'] = 'api/gsc_package/$1';

// Event API Routes
$route['api/event'] = 'api/event';
$route['api/event/(:any)'] = 'api/event/$1';
$route['api/notification'] = 'api/notification';
$route['api/notification/(:any)'] = 'api/notification/$1';
