<?php

include 'message.php';
include 'queue.php';

Queue::getQueue();

$key = rand(1, 1000000);

$data = array(
  'time' => time(),
  'key' => $key,
  'request' => 'start'
);
# Add the message into the queue
Queue::addMessage($key, $data);