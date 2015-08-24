<?php

include 'message.php';
include 'queue.php';
include 'worker.php';

$pid = pcntl_fork();

if ($pid == -1) {
  die('could not fork' . PHP_EOL);
}
else {
  if ($pid) {
    exit(0);
  }
  else{
    posix_setsid();

    $worker = new Worker;
  }
}
