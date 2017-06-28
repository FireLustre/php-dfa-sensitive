<?php

/**
 * 敏感词类库.
 * User: wanghui
 * Date: 17/3/9
 * Time: 上午9:11
 */
require_once 'LHashMap.php';

class LSensitiveWordFilter
{

    private static $_instance;

    public static $badWordList = array();

    //获取单例
    public static function init()
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 将敏感词加入到HashMap中
     * @param $sensitiveWord
     * @return obj
     **/
    public function setHashMap($sensitiveWord)
    {
        $wordMap = new LHashMap();
        foreach ($sensitiveWord as $word) {
            $nowMap = $wordMap;
            $wordLength = mb_strlen($word, 'utf-8');
            for ($i = 0; $i < $wordLength; $i++) {
                $keyChar = mb_substr($word, $i, 1, 'utf-8');
                //获取
                $tempMap = $nowMap->get($keyChar);
                if ($tempMap) {
                    $nowMap = $tempMap;
                } else {
                    // 设置标志位
                    $newMap = new LHashMap();
                    $newMap->put('isEnd', '0');
                    // 添加到集合
                    $nowMap->put($keyChar, $newMap);
                    $nowMap = $newMap;
                }
                // 最后一个
                if ($i == $wordLength - 1) {
                    $nowMap->put("isEnd", "1");
                }
            }
        }
        return $wordMap;
    }

    /**
     * 获取文字中的敏感词
     * @param $content
     * @return obj
     **/
    public function getSensitiveWord($wordMap, $content, $matchType = 1)
    {
        $contentLength = mb_strlen($content, 'utf-8');
        $badWordList = array();
        for ($len = 0; $len < $contentLength; $len++) {
            $matchFlag = 0;
            $flag = false;
            $tempMap = $wordMap;
            for ($i = $len; $i < $contentLength; $i++) {
                $keyChar = mb_substr($content, $i, 1, 'utf-8');
                //获取指定key
                $nowMap = $tempMap->get($keyChar);
                //存在，则判断是否为最后一个
                if ($nowMap != null) {
                    $tempMap = $nowMap;
                    //找到相应key，偏移量+1
                    $matchFlag++;
                    //如果为最后一个匹配规则,结束循环，返回匹配标识数
                    if ($nowMap->get("isEnd") == '1') {
                        $flag = true;
                        if ($matchType == 1) {
                            //最小规则，直接返回
                            break;
                        } else {
                            //最大规则还需继续查找
                            continue;
                        }
                    } else {
                        continue;
                    }
                } else {
                    //不存在，直接返回
                    break;
                }
            }
            if (!$flag) {
                $matchFlag = 0;
            }

            //未找到相应key
            if ($matchFlag > 0) {
                $badWordList[] = mb_substr($content, $len, $matchFlag, 'utf-8');
                //需匹配内容标志位往后移
                $len = $len + $matchFlag - 1;
            } else {
                continue;
            }
        }
        return $badWordList;
    }


    //替换敏感字字符
    public function replaceSensitiveWord($wordMap, $content, $replaceChar, $sTag = '', $eTag = '', $matchType = 1)
    {
        if (empty(self::$badWordList)) {
            $badWordList = $this->getSensitiveWord($wordMap, $content, $matchType);
        } else {
            $badWordList = self::$badWordList;
        }

        if (!empty($badWordList)) {
            foreach ($badWordList as $badWord) {
                if ($sTag || $eTag) {
                    $replaceChar = $sTag . $badWord . $eTag;
                }
                $content = str_replace($badWord, $replaceChar, $content);
            }
        }
        return $content;
    }

}