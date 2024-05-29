<?php

//输出帖子列表
function selectGitfTopic()
{
    $topic = [
        [
            "imgsrc" => 'baidu.com',//图片
            "href" => "a.com",//详情地址
            "text" => "内容",//简介
            "title" => "标题",//标题
            "like" => "88",//点赞数量
            "cletime" => "2022/02/02 8:8:8"//发布日期
            "imgsrc" => 'baidu.com',//用户头像地址
            "text" => "内容"//用户昵称
        ],
        [
            "imgsrc" => 'baidu.com',//图片
            "href" => "a.com",//详情地址
            "text" => "内容",//简介
            "title" => "标题",//标题
            "like" => "88",//点赞数量
            "cletime" => "2022/02/02 8:8:8"//发布日期
            "imgsrc" => 'baidu.com',//用户头像地址
            "text" => "内容"//用户昵称
        ],
        [
            "imgsrc" => 'baidu.com',//图片
            "href" => "a.com",//详情地址
            "text" => "内容",//简介
            "title" => "标题",//标题
            "like" => "88",//点赞数量
            "cletime" => "2022/02/02 8:8:8"//发布日期
            "imgsrc" => 'baidu.com',//用户头像地址
            "text" => "内容"//用户昵称
        ]
    ];
        // 将数组转换为JSON格式
        $jsonResult = json_encode($topic, JSON_UNESCAPED_UNICODE); // 为了保持中文字符不变，使用JSON_UNESCAPED_UNICODE
        return $jsonResult;
}

//输出商品种类
function selectGitfType()
{
    $shopType = ["男生","女生","爸爸","妈妈","孩子","老人","老师","客户","结婚礼物","创意礼物","实用礼物","更多"];
        // 将数组转换为JSON格式
        $jsonResult = json_encode($shopType, JSON_UNESCAPED_UNICODE); // 为了保持中文字符不变，使用JSON_UNESCAPED_UNICODE
        return $jsonResult;
}

//输出banner列表
function selectGitfBanner()
{
    $banner = [
        "iocclass" => 'uiBlock',//显示的图标
        "bannertext" => "礼物街", //显示的文字
        "href" => "a.com"//链接地址
    ];
        // 将数组转换为JSON格式
        $jsonResult = json_encode($banner, JSON_UNESCAPED_UNICODE); // 为了保持中文字符不变，使用JSON_UNESCAPED_UNICODE
        return $jsonResult;
}

//输出banner列表
function selectGitfHeader()
{
    $header = [
        "headerimgurl" => 'uiBlock',//图片地址
        "headerurl" => "礼物街", //详情地址
    ];
        // 将数组转换为JSON格式
        $jsonResult = json_encode($header, JSON_UNESCAPED_UNICODE); // 为了保持中文字符不变，使用JSON_UNESCAPED_UNICODE
        return $jsonResult;
}

//输出商品列表
function selectGitfTopic()
{
    $shopdata = [
        [
            "imgsrc" => 'baidu.com',//商品图片
            "href" => "a.com",//商品链接地址
            "text" => "内容",//商品简介
            "title" => "标题",//商品标题
            "price" => "价格",//
            "love" => "888"//收藏数量
        ],
        [
            "imgsrc" => 'baidu.com',//商品图片
            "href" => "a.com",//商品链接地址
            "text" => "内容",//商品简介
            "title" => "标题",//商品标题
            "price" => "价格",//
            "love" => "888"//收藏数量
        ],
        [
            "imgsrc" => 'baidu.com',//商品图片
            "href" => "a.com",//商品链接地址
            "text" => "内容",//商品简介
            "title" => "标题",//商品标题
            "price" => "价格",//
            "love" => "888"//收藏数量
        ]
    ];
        // 将数组转换为JSON格式
        $jsonResult = json_encode($shopdata, JSON_UNESCAPED_UNICODE); // 为了保持中文字符不变，使用JSON_UNESCAPED_UNICODE
        return $jsonResult;
}