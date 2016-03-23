<?php
/**
 * Apps dashboard page
 * https://go.tradegecko.com/oauth/applications
 *
 * Docs
 * http://developer.tradegecko.com/
 **/

if (in_array($_SERVER['HTTP_HOST'], array('localhost'))) {
    define('REDIRECT_URI', 'http://localhost/example/tradegecko/');
} else {
    define('REDIRECT_URI', 'https://www.example.com/tradegecko/');
}

define('TG_API_URL', 'https://api.tradegecko.com/');
define('TG_CLIENT_ID', 'CLIENT_ID');
define('TG_SECRET', 'SECRET');
define('TG_PRIVILIGED_CODE', 'API_TOKEN');

// For GLS
$GLOBALS['sender'] = array(
    'name' => 'NAME',
    'street' => 'STREET',
    'city' => 'CITY',
    'zip' => 'ZIP',
    'country' => 'SK',
    'phone' => 'PHONE',
    'ref_number' => 'GLS_REF_NUMBER',
);
