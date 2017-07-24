<?php

/**
 * 敏感词类库.
 * User: wanghui
 * Date: 17/3/9
 * Time: 上午9:11
 */
namespace DfaFilter;

class SensitiveHelper
{

    private static $_instance;

    protected static $badWordList = array();

    /**
     * 获取单例
     *
     * @return LSensitiveWordFilter
     */
    public static function init()
    {
        if (!self::$_instance instanceof self) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 将敏感词加入到HashMap中
     *
     * @param $sensitiveWord
     * @return HashMap
     */
    public function setHashMap($sensitiveWord)
    {
        $wordMap = new HashMap();
        foreach ($sensitiveWord as $word) {
            $nowMap = $wordMap;
            $wordLength = mb_strlen($word, 'utf-8');
            for ($i = 0; $i < $wordLength; $i++) {
                $keyChar = mb_substr($word, $i, 1, 'utf-8');
                // 获取
                $tempMap = $nowMap->get($keyChar);
                if ($tempMap) {
                    $nowMap = $tempMap;
                } else {
                    // 设置标志位
                    $newMap = new HashMap();
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
     *
     * @param $wordMap
     * @param $content
     * @param int $matchType
     * @return array
     */
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

                // 获取指定key
                $nowMap = $tempMap->get($keyChar);

                // 不存在，直接返回
                if (empty($nowMap)) break;

                // 存在，则判断是否为最后一个
                $tempMap = $nowMap;

                // 找到相应key，偏移量+1
                $matchFlag++;

                // 如果为最后一个匹配规则,结束循环，返回匹配标识数
                if ('1' !== $nowMap->get('isEnd')) continue;
                $flag = true;

                // 最小规则，直接返回
                if ($matchType == 1)
                    break;
                 else // 最大规则还需继续查找
                    continue;
            }
            if (! $flag) $matchFlag = 0;

            // 找到相应key
            if ($matchFlag <= 0) continue;
            $badWordList[] = mb_substr($content, $len, $matchFlag, 'utf-8');

            // 需匹配内容标志位往后移
            $len = $len + $matchFlag - 1;
        }
        return $badWordList;
    }


    /**
     * 替换敏感字字符
     *
     * @param $wordMap
     * @param $content
     * @param $replaceChar
     * @param string $sTag
     * @param string $eTag
     * @param int $matchType
     * @return mixed
     */
    public function replaceSensitiveWord($wordMap, $content, $replaceChar, $sTag = '', $eTag = '', $matchType = 1)
    {
        if (empty(self::$badWordList)) {
            $badWordList = $this->getSensitiveWord($wordMap, $content, $matchType);
        } else {
            $badWordList = self::$badWordList;
        }

        // 未检测到敏感词，直接返回
        if (empty($badWordList))
            return $content;

        foreach ($badWordList as $badWord) {
            if ($sTag || $eTag) {
                $replaceChar = $sTag . $badWord . $eTag;
            }
            $content = str_replace($badWord, $replaceChar, $content);
        }
        return $content;
    }

}