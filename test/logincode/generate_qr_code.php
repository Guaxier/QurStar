<?php

require 'phpqrcode.php'; // 确保已安装并正确引用 QRcode 库

session_start(); // 启动会话

// 生成随机的会话ID作为二维码内容
$sessionID = uniqid();
$timestamp = time();

// 保存会话ID和生成时间到session或者数据库中
$_SESSION['qr_session_id'] = $sessionID;
$_SESSION['qr_timestamp'] = $timestamp;

// 确保 qr_codes 目录存在并具有写入权限
$qrCodeDir = 'qr_codes';
if (!is_dir($qrCodeDir)) {
    mkdir($qrCodeDir, 0777, true); // 创建目录并设置权限
}

$qrCodeFile = $qrCodeDir . '/' . $sessionID . '.png';

QRcode::png($sessionID, $qrCodeFile); // 使用QRcode库生成二维码

// 返回二维码图片URL给前端
echo $qrCodeFile;
