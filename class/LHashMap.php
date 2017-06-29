<?php

/**
 * php构建哈希表类.
 * User: wanghui
 * Date: 17/3/9
 * Time: 上午9:10
 **/
class LHashMap
{

    public $H_table;

    /*
     * HashMap构造函数
     */
    public function __construct()
    {
        $this->H_table = array();
    }

    /**
     * 向HashMap中添加一个键值对
     * @param $key 插入的键
     * @param $value 插入的值
     **/
    public function put($key, $value)
    {
        if (!array_key_exists($key, $this->H_table)) {
            $this->H_table[$key] = $value;
            return null;
        } else {
            $tempValue = $this->H_table[$key];
            $this->H_table[$key] = $value;
            return $tempValue;
        }
    }

    /**
     * 根据key获取对应的value
     * @param $key
     **/
    public function get($key)
    {
        if (array_key_exists($key, $this->H_table))
            return $this->H_table[$key];
        else
            return null;
    }

    /**
     * 删除指定key的键值对
     * @param $key 要移除键值对的key
     **/
    public function remove($key)
    {
        $temp_table = array();
        if (array_key_exists($key, $this->H_table)) {
            $tempValue = $this->H_table[$key];
            while ($curValue = current($this->H_table)) {
                if (!(key($this->H_table) == $key))
                    $temp_table[key($this->H_table)] = $curValue;

                next($this->H_table);
            }
            $this->H_table = null;
            $this->H_table = $temp_table;
            return $tempValue;
        } else
            return null;
    }

    /**
     * 获取HashMap的所有键值
     * @return 返回HashMap中key的集合,以数组形式返回
     **/
    public function keys()
    {
        return array_keys($this->H_table);
    }

    /**
     * 获取HashMap的所有value值
     */
    public function values()
    {
        return array_values($this->H_table);
    }

    /**
     * 将一个HashMap的值全部put到当前HashMap中
     * @param $map
     **/
    public function putAll($map)
    {
        if (!$map->isEmpty() && $map->size() > 0) {
            $keys = $map->keys();
            foreach ($keys as $key) {
                $this->put($key, $map->get($key));
            }
        }
    }

    /**
     * 移除HashMap中所有元素
     **/
    public function removeAll()
    {
        $this->H_table = null;
        $this->H_table = array();
    }

    /**
     * HashMap中是否包含指定的值
     * @param $value
     **/
    public function containsValue($value)
    {
        while ($curValue = current($this->H_table)) {
            if ($curValue == $value) {
                return true;
            }
            next($this->H_table);
        }
        return false;
    }

    /**
     * HashMap中是否包含指定的键key
     * @param $key
     **/
    public function containsKey($key)
    {
        if (array_key_exists($key, $this->H_table)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 获取HashMap中元素个数
     **/
    public function size()
    {
        return count($this->H_table);
    }

    /**
     * 判断HashMap是否为空
     **/
    public function isEmpty()
    {
        return (count($this->H_table) == 0);
    }


}