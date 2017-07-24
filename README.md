# php-DFA-filterWord
php实现基于确定有穷自动机算法的铭感词过滤

### 使用流程
> composer安装 

    composer require lustre/php-dfa-sensitive
   
> 实例化敏感词过滤助手

    require './vendor/autoload.php';
    
    use DfaFilter\SensitiveHelper;
    
    $sensitiveWordHelper = SensitiveHelper::init();

> 获取敏感词库

    //获取感词库索引数组
    $wordData = array(
        '察象蚂',
        '拆迁灭',
        '车牌隐',
        '成人电',
        '成人卡通',
        ......
    );
    
> 构建敏感词hashMap

    //构建敏感词hashMap
    $sensitiveWordMap = $sensitiveWordHelper->setHashMap($wordData);
    
> 敏感词过滤
    
    //敏感词替换为***为例
    $filterContent = $sensitiveWordHelper->replaceSensitiveWord($sensitiveWordMap, $content, '***');
    
> 你也可以获取文字中的敏感词

    //获取内容中所有的敏感词
    $sensitiveWordGroup = $sensitiveWordHelper->getSensitiveWord($sensitiveWordMap, $content);
    //仅且获取一个敏感词
    $sensitiveWordGroup = $sensitiveWordHelper->getSensitiveWord($sensitiveWordMap, $content, 1);
    
*如果大家有更好的建议，请大家多多指正，O(∩_∩)O谢谢*
