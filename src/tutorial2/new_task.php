<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

//make connection
try {
    $connection = new AMQPStreamConnection('localhost', '5672', 'guest', 'guest');
} catch (\Exception $exception) {
    echo "Unable to connect with rabbit-mq. Error " . $exception->getMessage();
    exit;
}

//create channel from connection
$channel = $connection->channel();

//create queue from channel
$channel->queue_declare('task_queue', false, true,false,false);

$data = implode(' ', array_slice($argv, 1));
if (empty($data)) {
    $data = 'hello world';
}

//prepare your message
$message = new AMQPMessage($data, ['delivery_mode' => AMQPMessage::DELIVERY_MODE_NON_PERSISTENT]);
//publish your message on defined queue
$channel->basic_publish($message, '', 'task_queue');

echo " [x] Sent '.$data.'\n";

$channel->close();
$connection->close();


