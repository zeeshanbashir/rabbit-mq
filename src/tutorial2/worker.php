<?php
require_once __DIR__ . '/../../vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPStreamConnection;

//make connection
try {
    $connection = new AMQPStreamConnection('localhost', '5672', 'guest', 'guest');
} catch (\Exception $exception) {
    die('Error : ' . $exception->getMessage());
}

//create channel
$channel = $connection->channel();

//create queue from channel
// durable is set to true which means your message will not be discarded even consumer is dead.
$channel->queue_declare('task_queue', false, true, false, false);

echo " [*] Waiting for messages. To exit press CTRL+C\n";

$callback = function ($msg) {
    echo ' [x] Received ', $msg->body, "\n";
    sleep(substr_count($msg->body, '.'));
    echo " [x] Done\n";
    //consumer acknowledge
    $msg->delivery_info['channel']->basic_ack($msg->delivery_info['delivery_tag']);
};

//not to give more then one message to a worker
$channel->basic_qos(null, 1, null);
//consume message from defined queue and print message through callback
$channel->basic_consume('task_queue', '', false, false, false, false, $callback);

while ($channel->is_consuming()) {
    $channel->wait();
}

$channel->close();
$connection->close();




