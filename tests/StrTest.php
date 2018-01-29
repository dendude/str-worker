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
 * Есть 6 методов обработки текста:
 * Очистить от тегов
 * Удалить пробелы
 * Заменить все пробелы на переносы строк
 * Экранировать спец-символы
 * Удалить символы [.,/!@#$%&*()]
 * Преобразовать в целое число (найти в тексте)
 *
 * Class StrTest
 * @package App\Tests
 */
class StrTest extends TestCase
{
    const TEST_STR = 'Привет, мне на <a href="test@test.ru">test@test.ru</a> пришло приглашение встретиться, попить кофе с <strong>10%</strong> содержанием молока за <i>$5</i>, пойдем вместе!';
    
    protected function cmdRun(string $text, array $methods) {
        
        $array = [
            'job' => [
                'text' => $text,
                'methods' => $methods,
            ]
        ];
        
        $result = exec('curl -d="' . addslashes(json_encode($array)) . '" localhost:8000');        
        $data = json_decode($content, true);
        
        return ($data['text'] ?? null);
    }
    
    public function testRange1() {
        $result = $this->cmdRun(self::TEST_STR, ['removeSymbols', 'removeSpaces']);
        $this->assertEquals($result, 'Приветмнена<ahref="testtestru">testtestru<a>пришлоприглашениевстретитьсяпопитькофес<strong>10<strong>содержаниеммолоказа<i>5<i>пойдемвместе');
    }
    
    public function testRange2() {
        $result = $this->cmdRun(self::TEST_STR, ['toNumber']);
        $this->assertEquals($result, '105');
    }
    
    public function testRange3() {
        $eol = PHP_EOL;
        $result = $this->cmdRun(self::TEST_STR, ['replaceSpacesToEol']);
        $this->assertEquals($result, "Привет,{$eol}мне{$eol}на{$eol}<a href=\"test@test.ru\">test@test.ru</a>{$eol}пришло{$eol}приглашение{$eol}встретиться,{$eol}попить{$eol}кофе{$eol}с{$eol}<strong>10%</strong>{$eol}содержанием{$eol}молока{$eol}за{$eol}<i>$5</i>,{$eol}пойдем{$eol}вместе!");
    }
}
