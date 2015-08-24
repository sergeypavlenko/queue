<?php

class Worker {
  /**
   * Store the semaphore queue handler.
   * @var resource
   */
  private $queue = NULL;
  /**
   * Store an instance of the read Message
   * @var Message
   */
  private $message = NULL;

  private $max = 5;
  private $childs = [];

  /**
   * Constructor: Setup our enviroment, load the queue and then
   * process the message.
   */
  public function __construct() {
    # Get the queue
    $this->queue = Queue::getQueue();
    # Now process
    $this->process();
  }
  private function process() {
    $messageType = NULL;
    $messageMaxSize = 1024;

    while (TRUE) {
      if (count($this->childs) < $this->max) {
        echo count($this->childs) . " ";
        if(msg_receive($this->queue, QUEUE_TYPE_START, $messageType, $messageMaxSize, $this->message)) {
          $pid = pcntl_fork();

          if ($pid == -1) {
            die('could not fork' . PHP_EOL);
          }
          else {
            if ($pid) {
              $this->childs[$pid] = TRUE;
              $messageType = NULL;
              $this->message = NULL;
            }
            else {
              sleep(3);
              $this->complete($messageType, $this->message);
              exit();
            }
          }

          foreach ($this->childs as $pid => $value) {
            if (pcntl_waitpid($pid, $status, WNOHANG)) {
              if (pcntl_wifexited($status)) {
                unset($this->childs[$pid]);
              }
            }
          }
        }
      }

      sleep(1);
    }
  }
  /**
   * complete: Handle the message we read from the queue
   *
   * @param $messageType int - The type we actually got, not what we desired
   * @param $message Message - The actual object
   */
  private function complete($messageType, Message $message) {
    # Generic method
    echo $message->getKey() . "\n";
  }
}
