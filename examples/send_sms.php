<?php
use lnst\Sender;

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

try {
    $sender = new Sender(USER, PASS);
    $sender->setLogger('sender.log');
    
    $id = rand();
    $dst = RECIPIENT;
    $text = "This SMS was sent for testing purposes";
    $options = array();
    
    $queued = $sender->sms($id, $dst, $text, $options);
    
    if ($sender->errno) {
        echo "Error: ". $sender->errno . ":" . $sender->error . PHP_EOL;
    }

    if ($queued) {
        $status = $sender->getStatusSMS($id);
        echo "ID: ". $id . PHP_EOL;
        echo "Status: ". $status;
        echo " => ". $sender->getStatusCode($status);
        echo " = ". $sender->getStatusDescription($status) . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error: ". $e->getMessage();
}
