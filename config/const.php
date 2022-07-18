<?php
/**
 * Definevlue
 *
 * @return  [type]  [return description]
 */
return [
	// Admin define
	'LC_DB_PREFIX' => env('DB_PREFIX', ''),
	'LC_ADMIN_PREFIX' => env('ADMIN_PREFIX', 'system'),
	'LC_ADMIN_AUTH' => env('ADMIN_AUTH', 'api'),
	'LC_ADMIN_MIDDLEWARE' => ['web' , 'admin' , 'auth:sanctum'],
	'LC_FRONT_MIDDLEWARE' => ['web' , 'front' , 'auth:sanctum'],
	'LC_API_MIDDLEWARE' => ['api' , 'api.extent'],
	'LC_CONNECTION' => 'mysql',
	'LC_CONNECTION_LOG' => 'mysql',
    'LOG_SLACK_WEBHOOK_URL' => env('LOG_SLACK_WEBHOOK_URL',''),
    'MAIL_HOST' => env('MAIL_HOST',''),
    'MAIL_PORT' => env('MAIL_PORT',''),
    'MAIL_ENCRYPTION' => env('MAIL_ENCRYPTION',''),
    'MAIL_USERNAME' => env('MAIL_USERNAME',''),
    'MAIL_PASSWORD' => env('MAIL_PASSWORD',''),

	//Product kind
	'LC_PRODUCT_SINGLE' => 0,
	'LC_PRODUCT_BUILD' => 1,
	'LC_PRODUCT_GROUP' => 2,
	//Product property
	'LC_PROPERTY_PHYSICAL' => 'physical',
	'LC_PROPERTY_DOWNLOAD' => 'download',
	// list ID admin guard
	'LC_GUARD_ADMIN' => ['1'], // admin
	// list ID language guard
	'LC_GUARD_LANGUAGE' => ['1','2'], // vn=> us
	// list ID currency guard
	'LC_GUARD_CURRENCY' => ['1','2'], // vndong => usd
	// list ID ROLES guard
	'LC_GUARD_ROLES' => ['1','2'], // admin=> only view

	// Root ID store
	'LC_ID_ROOT' => 1

];
