<?php
/**
 * 测试案例.
 * User: wanghui
 * Date: 17/6/28
 * Time: 下午5:54
 */

$wordPool = file_get_contents('keyWord.txt');

//构建敏感词hashMap
$sensitiveWordMap = LSensitiveWordFilter::init()->setHashMap($wordData);