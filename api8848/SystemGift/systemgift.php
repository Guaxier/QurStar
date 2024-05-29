<?php

//输出商品列表
function selectGitfShop()
{
    $shopdata = [
        [
            "imgsrc" => 'baidu.com',//商品图片
            "href" => "a.com",//商品详情地址
            "text" => "内容",//商品简介
            "title" => "标题",//商品标题
            "like" => "88",//点赞数量
            "cletime" => "2022/02/02 8:8:8"//发布日期
            "imgsrc" => 'baidu.com',//用户头像地址
            "text" => "内容", //用户昵称
            "href" => "a.com"
        ],
        [
            "imgsrc" => 'baidu.com',//商品图片
            "href" => "a.com",//商品详情地址
            "text" => "内容",//商品简介
            "title" => "标题",//商品标题
            "like" => "88",//点赞数量
            "cletime" => "2022/02/02 8:8:8"//发布日期
            "imgsrc" => 'baidu.com',//用户头像地址
            "text" => "内容", //用户昵称
            "href" => "a.com"
        ],
        [
            "imgsrc" => 'baidu.com',//商品图片
            "href" => "a.com",//商品详情地址
            "text" => "内容",//商品简介
            "title" => "标题",//商品标题
            "like" => "88",//点赞数量
            "cletime" => "2022/02/02 8:8:8"//发布日期
            "imgsrc" => 'baidu.com',//用户头像地址
            "text" => "内容", //用户昵称
            "href" => "a.com"
        ]
    ];
        // 将数组转换为JSON格式
        $jsonResult = json_encode($shopdata, JSON_UNESCAPED_UNICODE); // 为了保持中文字符不变，使用JSON_UNESCAPED_UNICODE
        return $jsonResult;
}
//输出商品种类
function selectGitfType()
{
    $shopType = ["男生","女生","爸爸","妈妈","孩子","老人","老师","客户","女生"];
        // 将数组转换为JSON格式
        $jsonResult = json_encode($shopType, JSON_UNESCAPED_UNICODE); // 为了保持中文字符不变，使用JSON_UNESCAPED_UNICODE
        return $jsonResult;
}