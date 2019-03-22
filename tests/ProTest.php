<?php
/**
 * Created by PhpStorm.
 * User: zed
 * Date: 17-11-13
 * Time: 上午10:00
 */

use DfaFilter\SensitiveHelper;
use PHPUnit\Framework\TestCase;

class BaseTest extends TestCase
{
    protected $wordPoolPath;

    public function setUp()
    {
        parent::setUp();

        // 铭感词文件路径
        $this->wordPoolPath = 'tests/data/words.txt';
    }

    public function testGetBadWord()
    {
        $sTime = microtime(true);
        $content = '这是一段测试语句，请忽略赌球网, 第二个敏感词是三级片';

        // 过滤,其中【赌球网】在词库中
        $filterContent = SensitiveHelper::init()
            ->setTreeByFile($this->wordPoolPath)
            ->getBadWord($content);

        // 返回规定数量的敏感词,其中【赌球网,三级片】在词库中
        $badWords = SensitiveHelper::init()
            ->setTreeByFile($this->wordPoolPath)
            ->getBadWord($content, 1, 2);

        $eTime = microtime(true);

        echo ($eTime - $sTime) * 1000 . 'ms' . PHP_EOL;

        $this->assertEquals('赌球网', $filterContent[0]);
        $this->assertEquals('三级片', $badWords[1]);
    }

    public function testFilterWord()
    {
        $content = '这是一段测试语句，请忽略赌球网';

        // 过滤,其中【赌球网】在词库中
        $filterContent = SensitiveHelper::init()
            ->setTreeByFile($this->wordPoolPath)
            ->replace($content,'*');

        $this->assertEquals('这是一段测试语句，请忽略*', $filterContent);


        // 过滤,其中【赌球网】在词库中
        $filterContent = SensitiveHelper::init()
            ->setTreeByFile($this->wordPoolPath)
            ->replace($content,'*', true);

        $this->assertEquals('这是一段测试语句，请忽略***', $filterContent);
    }

    public function testMarkWord()
    {
        $content = '这是一段测试语句，请忽略赌球网';

        // 过滤,其中【赌球网】在词库中
        $markedContent = SensitiveHelper::init()
            ->setTreeByFile($this->wordPoolPath)
            ->mark($content,'<mark>', '</mark>');

        $this->assertEquals('这是一段测试语句，请忽略<mark>赌球网</mark>', $markedContent);
    }
}
