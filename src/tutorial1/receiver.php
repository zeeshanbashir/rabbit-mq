<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

//make connection
try {
    $connection = new AMQPStreamConnection('localhost', '5672', 'guest', 'guest');
}catch (\Exception $exception){
    die('Error : '.$exception->getMessage());
}

//create channel
$channel = $connection->channel();

//create queue from channel
$channel->queue_declare('first');

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
};

//consume message from defined queue and print message through callback
$channel->basic_consume('first', '', false, true, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();




