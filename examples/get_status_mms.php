<?php

use lnst\Sender;

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

//Requiring user to provide the MMS ID
if (php_sapi_name() == "cli") {
    if ($_SERVER['argc'] >= 2) {
        $id = $_SERVER['argv'][1];
    } else {
        die("Get status SMS: missing param. Run \"php ". $_SERVER['argv'][0] . " <id>\"");
    }
} else {
    $id = isset($_GET['id']) && !empty($_GET['id']) ? $_GET['id'] : false;
}

try {
    $sender = new Sender(USER, PASS);
    $sender->setLogger('sender.log');
    $status = $sender->getStatusMMS($id);

    echo "Status: ". $status;
    echo " => ". $sender->getStatusCode($status);
    echo " = ". $sender->getStatusDescription($status) . PHP_EOL;
} catch (Exception $e) {
    echo "Error: ". $e->getMessage();
}
