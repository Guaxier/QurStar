<?php

//输出帖子列表
/**
 * 名称：selectGiftTopic
 * 名称：主页帖子数据
 * 时间：2024/05/29 创建
 * 作者：Guaxier
 * 功能：获取主页推荐(热门)帖子数据
 * 参数:
 * 系统计算推送，无参数
 * 
 * 返回:
 * 成功返回帖子列表数组
 * 
 * 
 * 示例:
 * 
 * 
 */
function selectGiftTopic()
{
    $topic = [
        [
            "topic_imgsrc" => 'https://video.qilingwl.com/test/upload/file/jpg/520.jpg',//图片
            "topic_href" => "https://video.qilingwl.com/",//详情地址
            "topic_text" => "我们整理了10种适合送爱听歌女生的音乐礼物，推荐给你。这份礼物清单有女生都喜欢的Roseonly音乐永生玫瑰花，价格小贵但寓意美好；有和乐器有关的卡林巴拇指琴、女生尤克里里小吉他，也有创意音响和猫耳耳机，还有音乐抱枕和音乐书灯等，这些与音乐有关的礼物推荐给你，挑一款送她吧。",//简介
            "topic_title" => "送喜欢听歌女生的10种音乐礼物推荐",//标题
            "topic_like" => "235",//点赞数量
            "topic_time" => "7月6日",//发布日期(生产环境建议使用2022/02/02 8:8:8格式交由前端处理)
            "topic_userimgsrc" => 'http://q1.qlogo.cn/g?b=qq&nk=3533326199&s=100',//用户头像地址
            "topic_username" => "云锦"//用户昵称
        ],
        [
            "topic_imgsrc" => 'https://video.qilingwl.com/test/upload/file/jpg/520.jpg',//图片
            "topic_href" => "https://video.qilingwl.com/",//详情地址
            "topic_text" => "送礼物是比较常见的表达感谢的做法。对于帮助过我们的贵人，如果表达感谢，礼物势必要用心挑选。答谢贵人的礼物既要表达你的感谢，也要足够实用，才能让对方感受到你的心意。这10种感谢礼物是大多数人都比较认可的，推荐给你。有创意满分的工艺摆件，象征着喜事连连的喜上梢喜鹊灯，实用且贵气的钢笔礼盒，也有缓解疲劳的颈部按摩仪，实用的车载空气净化器等。这些礼物观赏性和实用性兼具，送这些礼物答谢贵人，一定错不了。",//简介
            "topic_title" => "答谢贵人？送这10种感谢礼物错不了",//标题
            "topic_like" => "995",//点赞数量
            "topic_time" => "12月12日",//发布日期(生产环境建议使用2022/02/02 8:8:8格式交由前端处理)
            "topic_userimgsrc" => 'http://q1.qlogo.cn/g?b=qq&nk=2537094196&s=100',//用户头像地址
            "topic_username" => "柠檬大王"//用户昵称
        ],
        [
            "topic_imgsrc" => 'https://video.qilingwl.com/test/upload/file/jpg/520.jpg',//图片
            "topic_href" => "https://video.qilingwl.com/",//详情地址
            "topic_text" => "七夕节又叫乞巧节，是中国的情人节。眼看2021年的七夕情人节就要来了，送什么礼物给女朋友会显得更有意义一些呢？如果你还在为什么礼物而烦恼，那么这份2021年送女友的七夕情人节礼物排行榜推荐应该能够帮你挑到合适的礼物。这份榜单有象征浪漫的永生玫瑰和四叶草手链，也有女生都爱不释手的口红和香水，还有创意满分的月球台灯和太空沙许愿瓶和竹简情书，更有吃货们都爱的零食礼包和巧克力。快来挑选一款礼物送你心爱的她吧。",//简介
            "topic_title" => "2024年送女友的七夕情人节礼物排行榜推荐",//标题
            "topic_like" => "11",//点赞数量
            "topic_time" => "4月20日",//发布日期(生产环境建议使用2022/02/02 8:8:8格式交由前端处理)
            "topic_userimgsrc" => 'http://q1.qlogo.cn/g?b=qq&nk=2537094196&s=100',//用户头像地址
            "topic_username" => "是一圆呀"//用户昵称
        ]
    ];
    return $topic;
}

//输出商品种类
/**
 * 名称：selectGiftType
 * 名称：主页商品种类
 * 时间：2024/05/29 创建
 * 作者：Guaxier
 * 功能：获取主页礼物分类列表数据
 * 参数:
 * api数据库推送，无参数
 * 
 * 返回:
 * 成功返回类别数组
 * 
 * 
 * 示例:
 * 
 * 
 */

function selectGiftType()
{
    $shopType = ["男生","女生","爸爸","妈妈","孩子","老人","老师","客户","结婚礼物","创意礼物","实用礼物","更多"];
        return $shopType;
}

/**
 * 名称：selectGiftBanner
 * 名称：主页商品推荐
 * 时间：2024/05/29 创建
 * 作者：Guaxier
 * 功能：获取主页banner数据
 * 参数:
 * api数据库推送，无参数
 * 
 * 返回:
 * 成功返回banner数组
 * 
 * 
 * 示例:
 * 
 * 
*/

function selectGiftBanner()
{
    $banner = [
        [
            "banner_ioc" => '&#xe610;',//显示的图标
            "banner_title" => "礼物街", //显示的文字
            "banner_href" => "a.com"//链接地址
        ],
        [
            "banner_ioc" => '&#xe602;',//显示的图标
            "banner_title" => "猜Ta喜欢", //显示的文字
            "banner_href" => "a.com"//链接地址
        ],
        [
            "banner_ioc" => '&#xe64a;',//显示的图标
            "banner_title" => "游记攻略", //显示的文字
            "banner_href" => "a.com"//链接地址
        ],
        [
            "banner_ioc" => '&#xe608;',//显示的图标
            "banner_title" => "送礼问答", //显示的文字
            "banner_href" => "a.com"//链接地址
        ]
    ];
    return $banner;
}

/**
 * 名称：selectGiftHeader
 * 名称：主页轮播图
 * 时间：2024/05/29 创建
 * 作者：Guaxier
 * 功能：获取主页轮播图数据
 * 参数:
 * api数据库推送，无参数
 * 
 * 返回:
 * 成功返回轮播图数组
 * 
 * 
 * 示例:
 * 
 *
 */
function selectGiftHeader()
{
    $header = [
        [
            "header_imgurl" => 'uiBlock',//图片地址
            "header_url" => "礼物街", //详情地址
        ],
        [
            "header_imgurl" => 'uiBlock',//图片地址
            "header_url" => "礼物街", //详情地址
        ],
        [
            "header_imgurl" => 'uiBlock',//图片地址
            "header_url" => "礼物街", //详情地址
        ],
    ];
    return $header;
}

/**
 * 名称：selectGiftShop
 * 名称：主页商品列表
 * 时间：2024/05/29 创建
 * 作者：Guaxier
 * 功能：获取主页推荐(热门)帖子数据
 * 参数:
 * 系统计算推送，无参数
 * 
 * 返回:
 * 成功返回商品列表数组
 * 
 * 
 * 示例:
 * 
 * 
*/
function selectGiftShop()
{
    $shopdata = [
        [
            "shop_imgsrc" => 'https://video.qilingwl.com/test/upload/file/jpg%2F520.jpg',//商品图片
            "shop_href" => "a.com",//商品链接地址
            "shop_text" => "迷你便携的可爱小狗音响，无线蓝牙链接手机电脑，可随时随地播放，适合年轻一族的时尚数码礼物。",//商品简介
            "shop_title" => "萌宠小旺蓝牙音响",//商品标题
            "shop_price" => "99.99",//
            "shop_love" => "16"//收藏数量
        ]
    ];
    return $shopdata;
}