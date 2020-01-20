<?php
use lnst\Sender;

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

try {
    $sender = new Sender(USER, PASS);
    $sender->setLogger('sender.log');
    
    $id = rand();
    $dst = RECIPIENT;
    $timestamp = time();
    $schedule = $timestamp + 60*5; //send it within 5 mins
    $text = 'Scheduled SMS at '. date('YmdHi', $timestamp).' to be sent at '. date('YmdHi', $schedule);
    $options = array('schedule' => date('YmdHi', $schedule));
    
    $queued = $sender->scheduledSMS($id, $dst, $text, $options);
    
    if ($sender->errno) {
        echo "Error: ". $sender->errno . ":" . $sender->error . PHP_EOL;
    }

    if ($queued) {
        $status = $sender->getStatusScheduled($id);
        echo "ID: ". $id . PHP_EOL;
        echo "Status: ". $status;
        echo " => ". $sender->getStatusCode($status);
        echo " = ". $sender->getStatusDescription($status) . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error: ". $e->getMessage();
}
