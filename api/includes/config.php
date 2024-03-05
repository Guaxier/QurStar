<?php
// 本php为中心数据配置文件，用于配置各种常量


// 数据库连接配置
define('DB_HOST', 'localhost');
define('DB_USER', 'videosql');
define('DB_PASS', 'videosql');
define('DB_NAME', 'videosql');

// 连接数据库
$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// 检查连接是否成功
if ($mysqli->connect_error) {
    die("连接失败: " . $mysqli->connect_error);
}









//smspend.php
define('ACCESS_KEY_ID', '111'); // 阿里云短信accessKeyId
define('ACCESS_KEY_SECRET', '222'); // 阿里云短信accessKeySecret
define('SECURITY_TOKEN', '333'); // 阿里云短信securityToken
