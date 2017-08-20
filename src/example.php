<?php
/**
 * 测试案例.
 * User: wanghui
 * Date: 17/6/28
 * Time: 下午5:54
 */

require '../vendor/autoload.php';

use DfaFilter\SensitiveHelper;

$sTime = microtime(true);
// 获取铭感词库
$wordPool = file_get_contents('keyWord.txt');
$wordData = explode(',', $wordPool);

$content = '这是一段测试语句，请忽略赌球网';

// 过滤,其中【赌球网】在词库中

$filterContent = SensitiveHelper::init()
    ->setTree($wordData)
    ->getBadWord($content);

$eTime = microtime(true);
var_dump($filterContent);die;
//结果
echo '检测后结果：' . $filterContent . '<br/>';
echo '运行时间：' . ($eTime - $sTime) * 1000 . '(ms)';

