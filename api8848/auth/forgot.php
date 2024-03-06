<?php
// forgot_password.php

// 包含共享函数文件
require_once '../includes/functions.php';

// 检查请求方法是否为 POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 处理忘记密码请求
    // 在这里编写忘记密码逻辑
    // 可以包括验证用户输入、更新密码等步骤
    // 最后返回适当的响应
    // 示例：
    $email = $_POST['email'];
    
    // 处理忘记密码逻辑
    if (forgot_password($email)) {
        // 发送重置密码邮件成功
        $response = array(
            'success' => true,
            'message' => 'Password reset instructions sent to your email.'
        );
        echo json_encode($response);
    } else {
        // 发送重置密码邮件失败
        $response = array(
            'success' => false,
            'message' => 'Failed to send password reset instructions.'
        );
        echo json_encode($response);
    }
} else {
    // 请求方法不是 POST
    $response = array(
        'success' => false,
        'message' => 'Method not allowed.'
    );
    echo json_encode($response);
}
