<?php
/**
 * 测试案例.
 * User: wanghui
 * Date: 17/6/28
 * Time: 下午5:54
 */
header('Content-type: text/html; charset=utf-8');
date_default_timezone_set('PRC');

require_once 'Lib/SensitiveWordFilter.php';

$sTime = microtime(true);

//获取铭感词库
$wordPool = file_get_contents('keyWord.txt');
$wordData = explode(',', $wordPool);
//构建敏感词hashMap
$sensitiveWordMap = LSensitiveWordFilter::init()->setHashMap($wordData);
$content = $_POST['content'];
$filterContent = LSensitiveWordFilter::init()->replaceSensitiveWord($sensitiveWordMap, $content, '***');

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

