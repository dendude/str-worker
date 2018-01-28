<?php
/**
 * Created by PhpStorm.
 * User: dendude
 * Date: 27/01/2018
 * Time: 23:22
 */

namespace App\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Базовый класс для тестов
 *
 * {
 *  'job': {
 *      'text': 'Привет, мне на <a href="test@test.ru">test@test.ru</a> пришло приглашение встретиться, попить кофе с <strong>10%</strong> содержанием молока за <i>$5</i>, пойдем вместе!'
 *      'methods': [
 *          'stripTags', 'removeSpaces', 'replaceSpacesToEol', 'htmlspecialchars', 'removeSymbols', 'toNumber'
 *      ]
 *  }
 * }
 *
 * Class StrTest
 * @package App\Tests
 */
class StrTest extends TestCase
{
    protected function cmdRun(string $text, array $methods) {
        
        $array = [
            'job' => [
                'text' => $text,
                'methods' => $methods,
            ]
        ];
        
        ob_start();
        exec('curl -d="' . addslashes(json_encode($array)) . '" localhost:8000');
        $content = ob_get_contents();
        ob_end_clean();
        
        $data = json_decode($content, true);
        
        return ($data['text'] ?? '');
    }
    
    public function testRemoveSpaces() {
        $result = $this->cmdRun('some text', ['removeSpaces']);
        $this->assertEquals($result, 'sometext');
    }
    
    public function testToNumber() {
        $result = $this->cmdRun('some 1 text 2', ['toNumber']);
        $this->assertEquals($result, '12');
    }
}