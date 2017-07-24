<?php
/**
 * 测试案例.
 * User: wanghui
 * Date: 17/6/28
 * Time: 下午5:54
 */

$sTime = microtime(true);

require './vendor/autoload.php';

use DfaFilter;
// 获取铭感词库
$wordPool = file_get_contents('keyWord.txt');
$wordData = explode(',', $wordPool);

$sensitiveWordHelper = DfaFilter\SensitiveHelper::init();

// 构建敏感词hashMap
$sensitiveWordMap = $sensitiveWordHelper->setHashMap($wordData);

// 过滤
$content = '这是一段测试语句，请忽略赌球网';
$filterContent = $sensitiveWordHelper->replaceSensitiveWord($sensitiveWordMap, $content, '***');

$eTime = microtime(true);
//结果
echo '<hr/>';
echo '<hr/>';
echo '<hr/>';
echo '检测后结果：' . $filterContent . '<br/>';
echo '运行时间：' . ($eTime - $sTime) * 1000 . '(ms)';
echo '<hr/>';
echo '<hr/>';
echo '<hr/>';

