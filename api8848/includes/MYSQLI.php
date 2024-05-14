<?php

// 使用mysqli连接数据库
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 检查连接是否成功
if ($mysqli->connect_error) {
    die("连接失败: " . $mysqli->connect_error);
}