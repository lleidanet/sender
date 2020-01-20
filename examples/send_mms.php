<?php
use lnst\Sender;

require_once dirname(__DIR__) . '/vendor/autoload.php';
require_once __DIR__ . '/config.php';

try {
    $sender = new Sender(USER, PASS);
    $sender->setLogger('sender.log');
    
    $id = rand();
    $dst = RECIPIENT;
    $text = 'MMS sent at '. date('YmdHi', time());
    $subject = 'Subject MMS';
    $options = array();
    
    $file = 'image.png';
    $attachment = array(
        'mime' =>  mime_content_type($file),
        'content' => $sender->getFileContentBase64($file)
    );
    
    $queued = $sender->mms($id, $dst, $text, $subject, $attachment, $options);
    
    if ($sender->errno) {
        echo "Error: ". $sender->errno . ":" . $sender->error . PHP_EOL;
    }
    
    if ($queued) {
        $status = $sender->getStatusMMS($id);
        echo "ID: ". $id . PHP_EOL;
        echo "Status: ". $status;
        echo " => ". $sender->getStatusCode($status);
        echo " = ". $sender->getStatusDescription($status) . PHP_EOL;
    }
} catch (Exception $e) {
    echo "Error: ". $e->getMessage();
}
