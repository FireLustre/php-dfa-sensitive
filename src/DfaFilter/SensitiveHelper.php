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
    /**
     * 待检测语句长度
     *
     * @var int
     */
    protected $contentLength = 0;

    /**
     * 敏感词单例
     *
     * @var object|null
     */
    private static $_instance = null;

    /**
     * 铭感词库树
     *
     * @var HashMap|null
     */
    protected $wordTree = null;

    /**
     * 存放待检测语句铭感词
     *
     * @var array|null
     */
    protected static $badWordList = null;

    /**
     * 获取单例
     *
     * @return self
     */
    public static function init()
    {
        if (! self::$_instance instanceof self) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 构建铭感词树【文件模式】
     *
     * @param string $sensitiveWord
     * @return $this
     */
    public function setTreeByFile($filepath = '')
    {
        if (! file_exists($filepath)) {
            throw new \Exception('词库文件不存在');
        }

        // 词库树初始化
        $this->wordTree = new HashMap();

        foreach ($this->yieldToReadFile($filepath) as $word) {
            $this->buildWordToTree(trim($word));
        }

        return $this;
    }


    /**
     * 构建铭感词树【数组模式】
     *
     * @param string $sensitiveWord
     * @return $this
     */
    public function setTree($sensitiveWords = null)
    {
        if (empty($sensitiveWords)) {
            throw new \Exception('词库不能为空');
        }

        $this->wordTree = new HashMap();

        foreach ($sensitiveWords as $word) {
            $this->buildWordToTree($word);
        }
        return $this;
    }

    /**
     * 检测文字中的敏感词
     *
     * @param string   $content    待检测内容
     * @param int      $matchType  匹配类型 [默认为最小匹配规则]
     * @param int      $wordNum    需要获取的敏感词数量 [默认获取全部]
     * @return array
     */
    public function getBadWord($content, $matchType = 1, $wordNum = 0)
    {
        $this->contentLength = mb_strlen($content, 'utf-8');
        $badWordList = array();
        for ($length = 0; $length < $this->contentLength; $length++) {
            $matchFlag = 0;
            $flag = false;
            $tempMap = $this->wordTree;
            for ($i = $length; $i < $this->contentLength; $i++) {
                $keyChar = mb_substr($content, $i, 1, 'utf-8');

                // 获取指定节点树
                $nowMap = $tempMap->get($keyChar);

                // 不存在节点树，直接返回
                if (empty($nowMap)) {
                    break;
                }

                // 存在，则判断是否为最后一个
                $tempMap = $nowMap;

                // 找到相应key，偏移量+1
                $matchFlag++;

                // 如果为最后一个匹配规则,结束循环，返回匹配标识数
                if (false === $nowMap->get('ending')) {
                    continue;
                }

                $flag = true;

                // 最小规则，直接退出
                if (1 === $matchType)  {
                    break;
                }
            }

            if (! $flag) {
                $matchFlag = 0;
            }

            // 找到相应key
            if ($matchFlag <= 0) {
                continue;
            }

            $badWordList[] = mb_substr($content, $length, $matchFlag, 'utf-8');

            // 有返回数量限制
            if ($wordNum > 0 && count($badWordList) == $wordNum) {
                return $badWordList;
            }

            // 需匹配内容标志位往后移
            $length = $length + $matchFlag - 1;
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
    public function replace($content, $replaceChar = '', $sTag = '', $eTag = '', $matchType = 1)
    {
        if (empty($content)) {
            throw new \Exception('请填写检测的内容');
        }

        if (empty(self::$badWordList)) {
            $badWordList = $this->getBadWord($content, $matchType);
        } else {
            $badWordList = self::$badWordList;
        }

        // 未检测到敏感词，直接返回
        if (empty($badWordList)) {
            return $content;
        }

        foreach ($badWordList as $badWord) {
            if ($sTag || $eTag) {
                $replaceChar = $sTag . $badWord . $eTag;
            }
            $content = str_replace($badWord, $replaceChar, $content);
        }
        return $content;
    }

    // 被检测内容是否合法
    public function islegal($content)
    {
        $this->contentLength = mb_strlen($content, 'utf-8');

        for ($length = 0; $length < $this->contentLength; $length++) {
            $matchFlag = 0;

            $tempMap = $this->wordTree;
            for ($i = $length; $i < $this->contentLength; $i++) {
                $keyChar = mb_substr($content, $i, 1, 'utf-8');

                // 获取指定节点树
                $nowMap = $tempMap->get($keyChar);

                // 不存在节点树，直接返回
                if (empty($nowMap)) {
                    break;
                }

                // 找到相应key，偏移量+1
                $tempMap = $nowMap;
                $matchFlag++;

                // 如果为最后一个匹配规则,结束循环，返回匹配标识数
                if (false === $nowMap->get('ending')) {
                    continue;
                }

                return true;
            }

            // 找到相应key
            if ($matchFlag <= 0) {
                continue;
            }

            // 需匹配内容标志位往后移
            $length = $length + $matchFlag - 1;
        }
        return false;
    }

    protected function yieldToReadFile($filepath)
    {
        $fp = fopen($filepath, 'r');
        while (! feof($fp)) {
            yield fgets($fp);
        }
        fclose($fp);
    }

    // 将单个敏感词构建成树结构
    protected function buildWordToTree($word = '')
    {
        if ('' === $word) {
            return;
        }
        $tree = $this->wordTree;

        $wordLength = mb_strlen($word, 'utf-8');
        for ($i = 0; $i < $wordLength; $i++) {
            $keyChar = mb_substr($word, $i, 1, 'utf-8');

            // 获取子节点树结构
            $tempTree = $tree->get($keyChar);

            if ($tempTree) {
                $tree = $tempTree;
            } else {
                // 设置标志位
                $newTree = new HashMap();
                $newTree->put('ending', false);

                // 添加到集合
                $tree->put($keyChar, $newTree);
                $tree = $newTree;
            }

            // 到达最后一个节点
            if ($i == $wordLength - 1) {
                $tree->put('ending', true);
            }
        }

        return;
    }
}
