<?php
require 'phpqrcode.php'; // 确保路径正确，根据您的文件结构可能需要调整

function getqrcode(){
    // 生成随机的会话ID作为二维码内容
    $sessionID = uniqid();
    $timestamp = time();

    // 设置二维码纠错级别以及大小
    $level = 'L';
    $size = 4;

    // 设置存储二维码图片的临时文件名
    $tempFile = tempnam(sys_get_temp_dir(), 'qrcode_');

    // 使用QRcode库直接生成二维码到文件而不是直接输出
    QRcode::png($sessionID, $tempFile, $level, $size);

    // 检查文件是否生成成功
    if (!file_exists($tempFile)) {
        return array(
            'success' => false,
            'message' => '获取二维码失败',
        );
    }

    // 读取文件内容并转换为Base64编码
    $base64_qrcode = 'data:image/jpeg;base64,' . base64_encode(file_get_contents($tempFile));

    // 清理临时文件（可选，根据实际需要决定是否删除）
    unlink($tempFile);

    // 返回信息
    return array(
        'success' => true,
        'qrcode' => $base64_qrcode, // 二维码图片的Base64编码
        'message' => $sessionID,    // 提示信息
    );
}