<?php

//Product kind
define('LC_PRODUCT_SINGLE', 0);
define('LC_PRODUCT_BUILD', 1);
define('LC_PRODUCT_GROUP', 2);
//Product property
define('LC_PROPERTY_PHYSICAL', 'physical');
define('LC_PROPERTY_DOWNLOAD', 'download');
// list ID admin guard
define('LC_GUARD_ADMIN', ['1']); // admin
// list ID language guard
define('LC_GUARD_LANGUAGE', ['1', '2']); // vi, en
// list ID currency guard
define('LC_GUARD_CURRENCY', ['1', '2']); // vndong , usd
// list ID ROLES guard
define('LC_GUARD_ROLES', ['1', '2']); // admin, only view

/**
 * Admin define
 */
define('LC_ADMIN_MIDDLEWARE', ['web', 'admin', 'auth:sanctum']);
define('LC_FRONT_MIDDLEWARE', ['web', 'front']);
define('LC_API_MIDDLEWARE', ['api']);
define('LC_CONNECTION', 'mysql');
define('LC_CONNECTION_LOG', 'mysql');
//Prefix url admin
define('LC_ADMIN_PREFIX', config('const.LC_ADMIN_PREFIX'));
define('LC_ADMIN_AUTH', config('const.LC_ADMIN_AUTH'));
//Prefix database
define('LC_DB_PREFIX', config('const.LC_DB_PREFIX'));
// Root ID store
define('LC_ID_ROOT', 1);
