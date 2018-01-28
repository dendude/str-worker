<?php
/**
 * Created by PhpStorm.
 * User: dendude
 * Date: 28/01/2018
 * Time: 02:26
 */

namespace App\Classes;

/**
 * Class StrListener
 *
 * запускается как микросервис
 *
 * @package App\Classes
 */
class StrListener
{
    protected function __construct() {}
    
    public static function run() {
        
        $self = new self();
        
        $connection = RabbitMQ::getInstance();
        $channel = $connection->channel();
        $channel->queue_declare(StrProcess::QUEUE_NAME, false, true, false, false);
        
        while (true) {
            
            try {
                
                $channel->basic_consume(
                    StrProcess::QUEUE_NAME,         // очередь
                    '',                             // тег получателя
                    false,                          // не локальный - TRUE: сервер не будет отправлять сообщения соединениям, которые сам опубликовал
                    false,                          // отправлять соответствующее подтверждение обработчику, как только задача будет выполнена
                    false,                          // эксклюзивная - к очереди можно получить доступ только в рамках текущего соединения
                    false,                          // не ждать - TRUE: сервер не будет отвечать методу. Клиент не должен ждать ответа
                    [$self, 'process']              // функция обратного вызова - метод, который будет принимать сообщение
                );
                
                while (count($channel->callbacks)) {
                    $channel->wait();
                }
                
            } catch (\Exception $e) {
                
                error_log($e->getCode() . '::' . $e->getMessage());
                echo $e->getMessage() . PHP_EOL;
            }
        }
        
        $channel->close();
        $connection->close();
    }
}