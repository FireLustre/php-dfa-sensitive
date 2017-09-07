# php-DFA-filterWord
php实现基于确定有穷自动机算法的铭感词过滤

###  安装&使用流程
> composer安装 

    composer require lustre/php-dfa-sensitive
   
> 如果你需要手动引入

    require './vendor/autoload.php';
    
    use DfaFilter\SensitiveHelper;

> 获取敏感词库

    // 获取感词库索引数组
    $wordData = array(
        '察象蚂',
        '拆迁灭',
        '车牌隐',
        '成人电',
        '成人卡通',
        ......
    );
   
> 检测是否含有敏感词

    $islegal = SensitiveHelper::init()->setTree($wordData)->islegal($content);
> 敏感词过滤
    
    // 敏感词替换为***为例
    $filterContent = SensitiveHelper::init()->setTree($wordData)->replace($content, '***');
    
> 获取文字中的敏感词

    // 获取内容中所有的敏感词
    $sensitiveWordGroup = SensitiveHelper::init()->setTree($wordData)->getBadWord($content);
    // 仅且获取一个敏感词
    $sensitiveWordGroup = SensitiveHelper::init()->setTree($wordData)->getBadWord($content, 1);

目前已知使用平台：[广电云](http://www.guangdianyun.tv/) 节目直播页面在线聊天功能支持


*如果大家有更好的建议，请大家多多指正，O(∩_∩)O谢谢*
