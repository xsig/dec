<?php
require_once  __DIR__ . '/vendor/autoload.php';
use Dec\lib\DecApi as DecApi;

// Requests from the same server don't have a HTTP_ORIGIN header
if (!array_key_exists('HTTP_ORIGIN', $_SERVER)) {
    $_SERVER['HTTP_ORIGIN'] = $_SERVER['SERVER_NAME'];
}

try {
     $API = new DecApi($_REQUEST['request'], $_SERVER['HTTP_ORIGIN']);
     echo $API->processAPI();
} catch (Exception $e) {
    echo json_encode(Array('error' => $e->getMessage()));
}
