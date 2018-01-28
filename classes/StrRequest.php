<?php
/**
 * Created by PhpStorm.
 * User: dendude
 * Date: 28/01/2018
 * Time: 16:31
 */

namespace App\Classes;

use PhpAmqpLib\Message\AMQPMessage;

class StrRequest
{
    const QUEUE_NAME = 'strProcess';
    
    private $connection;
    private $channel;
    private $callback_queue;
    private $response;
    private $corr_id;
    
    public function __construct() {
        
        $this->connection = RabbitMQ::getInstance();
        $this->channel = $this->connection->channel();
        
        $this->callback_queue = array_shift($this->channel->queue_declare('', false, false, true, false));
        $this->channel->basic_consume($this->callback_queue, '', false, false, false, false, [$this, 'onResponse']);
    }
    
    public function onResponse(AMQPMessage $rep) {
        
        if ($rep->get('correlation_id') == $this->corr_id) {
            $this->response = $rep->body;
        }
    }
    
    public function getResponse($msg) {
        
        $this->response = null;
        $this->corr_id = uniqid('corr_');
        
        $msg = new AMQPMessage($msg, [
            'correlation_id' => $this->corr_id,
            'reply_to'       => $this->callback_queue,
        ]);
        
        $this->channel->basic_publish($msg, '', self::QUEUE_NAME);
        
        while (is_null($this->response)) {
            $this->channel->wait();
        }
        
        $this->channel->close();
        $this->connection->close();
        
        return (string)$this->response;
    }
}