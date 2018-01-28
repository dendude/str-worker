<?php
/**
 * Created by PhpStorm.
 * User: dendude
 * Date: 28/01/2018
 * Time: 02:26
 */

namespace App\Classes;

use PhpAmqpLib\Channel\AMQPChannel;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class StrListener
 *
 * запускается как микросервис, ожидающий работу
 *
 * @package App\Classes
 */
class StrListener
{
    protected function __construct() {}
    
    public static function run() {
        
        $connection = RabbitMQ::getInstance();
        
        $channel = $connection->channel();
        $channel->queue_declare(StrRequest::QUEUE_NAME, false, false, false, false);
    
        self::listenSigOuts($channel, $connection);
        
        while (true) {
            
            try {
    
                $callback = function(AMQPMessage $req) {
                    
                    $obj = new StrProcess();
                    $result = $obj->process($req->body);
        
                    $msg = new AMQPMessage($result, [
                        'correlation_id' => $req->get('correlation_id')
                    ]);
    
                    /** @var $channel AMQPChannel */
                    
                    $channel = $req->delivery_info['channel'];
                    $channel->basic_publish($msg, '', $req->get('reply_to'));
    
                    $channel = $req->delivery_info['channel'];
                    $channel->basic_ack($req->delivery_info['delivery_tag']);
                };
    
                $channel->basic_qos(null, 1, null);
                $channel->basic_consume(StrRequest::QUEUE_NAME, '', false, false, false, false, $callback);
                
                while (count($channel->callbacks)) {
                    $channel->wait();
                }
                
                unset($callback);
                
            } catch (\Exception $e) {
                
                error_log($e->getCode() . '::' . $e->getMessage());
                echo $e->getMessage() . PHP_EOL;
            }
        }
    }
    
    /**
     * выходим в случае системных вызовов
     *
     * @param AMQPChannel $channel
     * @param AMQPStreamConnection $connection
     */
    protected static function listenSigOuts(AMQPChannel $channel, AMQPStreamConnection $connection) {
        
        foreach ([SIGKILL, SIGTERM] AS $sigNo) {
            
            pcntl_signal($sigNo, function() use ($channel, $connection) {
                $channel->close();
                $connection->close();
            });
        }
    }
}
