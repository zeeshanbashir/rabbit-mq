<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

//make connection
try {
    $connection = new AMQPStreamConnection('localhost', '5672', 'guest', 'guest');
}catch (\Exception $exception ){
    echo "Unable to connect with rabbit-mq. Error ".$exception->getMessage();
    exit;
}

//create channel from connection
$channel = $connection->channel();

//create queue from channel
$channel->queue_declare('first');

//prepare your message
$message = new AMQPMessage('Hello this is my first message through rabbit-mq\n');
//publish your message on defined queue
$channel->basic_publish($message,'','first');

echo " [x] Sent 'Hello World!'\n";

$channel->close();
$connection->close();


