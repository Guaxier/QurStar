<?php

function selectgitftype()
{
    $data = [
        ["数据标识",
        "物品分类列表",
        "对象分类列表",
        "精选数据",
        ],
        ["数据内容",
            [
                [
                    "href" => "baidu.com",
                    "class" => "aaa",
                    "text"=>"内容"
                ],
                [
                    "href" => "baidu.com",
                    "class" => "aaa",
                    "text"=>"内容"
                ],
                [
                    "href" => "baidu.com",
                    "class" => "aaa",
                    "text"=>"内容"
                ],
            ],

            [
                "class"=>"内容",
                "href"=>"a.com"
            ],
            [
                [
                    "imgsrc" => 'baidu.com',//商品图片
                    "href" => "a.com",//商品详情地址
                    "text" => "内容",//商品简介
                    "title" => "标题",//商品标题
                    "like" => "88",//点赞数量
                    "cletime" => "2022/02/02 8:8:8"//发布日期
                ],
                [
                    "imgsrc" => 'baidu.com',//用户头像地址
                    "text" => "内容", //用户昵称
                    "href" => "a.com"
                ]
            ],
        ]










    ];
        // 将数组转换为JSON格式
        $jsonResult = json_encode($data, JSON_UNESCAPED_UNICODE); // 为了保持中文字符不变，使用JSON_UNESCAPED_UNICODE
        return $jsonResult;
}

