<?php
/**
 * Created by PhpStorm.
 * User: dendude
 * Date: 28/01/2018
 * Time: 02:26
 */

namespace App\Classes;
use PhpAmqpLib\Message\AMQPMessage;

/**
 * Class StrProcess
 *
 * Есть 6 методов обработки текста:
 * Очистить от тегов
 * Удалить пробелы
 * Заменить все пробелы на переносы строк
 * Экранировать спец-символы
 * Удалить символы [.,/!@#$%&*()]
 * Преобразовать в целое число (найти в тексте)
 *
 * @see https://gitlab.com/turbo-public/backend-task-patterns/wikis/Home
 *
 * @package App\Classes
 */
class StrProcess
{
    /**
     * @param $msg - json-object
     *
     * @return array
     * @throws \Exception
     * @example
     * {
     *  'job': {
     *      'text': 'Привет, мне на <a href="test@test.ru">test@test.ru</a> пришло приглашение встретиться, попить кофе с <strong>10%</strong> содержанием молока за <i>$5</i>, пойдем вместе!'
     *      'methods': [
     *          'stripTags', 'removeSpaces', 'replaceSpacesToEol', 'htmlspecialchars', 'removeSymbols', 'toNumber'
     *      ]
     *  }
     * }
     */
    public function process($msg) {
    
        $data = json_decode($msg, true);
        
        if (isset($data['job']['text'], $data['job']['methods']) && is_array($data['job']['methods'])) {
            
            $result = $msg['job']['text'];
            
            foreach ($data['job']['methods'] AS $method) {
                
                if (!is_string($method)) throw new \Exception('Wrong format of method: ' . print_r($method, 1));
                if ($method === __FUNCTION__) throw new \Exception('Try to call recursive method: ' . $method);
                if (!method_exists($this, $method)) throw new \Exception('Method is not realized: ' . $method);
                
                $result = call_user_func_array([$this, $method], [$result]);
            }
            
            return ['text' => $result];
        }
    
        throw new \Exception('Wrong structure of job');
    }
    
    /**
     * Очистить от тегов
     *
     * @param $str
     * @return string
     */
    public function stripTags($str) {
        return strip_tags($str);
    }
    
    /**
     * Удалить пробелы
     *
     * @param $str
     * @return string
     */
    public function removeSpaces($str) {
        return str_replace(' ', '', $str);
    }
    
    /**
     * Заменить все пробелы на переносы строк
     *
     * @param $str
     * @return string
     */
    public function replaceSpacesToEol($str) {
        return str_replace(' ', PHP_EOL, $str);
    }
    
    /**
     * Экранировать спец-символы
     *
     * @param $str
     * @return string
     */
    public function htmlSpecialChars($str) {
        return htmlspecialchars($str);
    }
    
    /**
     * Удалить символы [.,/!@#$%&*()]
     *
     * @param $str
     * @return string
     */
    public function removeSymbols($str) {
        // faster than preg_replace
        return str_replace(['[','.',',','/','!','@','#','$','%','&','*','(',')',']'], '', $str);
    }
    
    /**
     * Преобразовать в целое число (найти в тексте)
     *
     * @param $str
     * @return string
     */
    public function toNumber($str) {
        return preg_replace('/[^\d]/um', '', $str);
    }
}