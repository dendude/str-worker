<?php
/**
 * Created by PhpStorm.
 * User: dendude
 * Date: 28/01/2018
 * Time: 02:29
 */

namespace App\Classes;

use PhpAmqpLib\Connection\AMQPStreamConnection;

/**
 * pattern - singleton
 *
 * Class RabbitMQ
 * @package App\Classes
 */
class RabbitMQ
{
    const TYPE_DEFAULT = 'default';
    const TYPE_OTHER = 'other';
    
    /**
     * pattern - multiton
     *
     * @var AMQPStreamConnection[]
     */
    protected static $connections = [];
    
    protected function construct($type){}
    
    public function __destruct() {
        self::closeConnection();
    }
    
    /**
     * singleton / multiton
     *
     * @param string $type
     * @return AMQPStreamConnection
     */
    public static function getInstance($type = self::TYPE_DEFAULT) {
        
        if (!isset(self::$connections[$type])) {
            
            $config = self::_getConfig()[$type];
            self::$connections[$type] = new AMQPStreamConnection(
                $config['host'],
                $config['port'],
                $config['user'],
                $config['pass']
            );
        }
    
        return self::$connections[$type];
    }
    
    public static function closeConnection($type = null) {
    
        foreach (self::$connections AS $k => $conn) {
            
            // selection close connection
            if (!is_null($type) && $type !== $k) continue;
            
            $conn->close();
            $conn = null;
        }
    }
    
    protected static function _getConfig() {
        return require(__DIR__ . '/../config/rabbitMQ.php');
    }
}