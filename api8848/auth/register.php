<?php
// register.php

//邮箱注册
function register_email($username, $password, $email, $code,$codeid) {
    // 验证用户名、密码、邮箱
    if (!validateInput($username, 'username')) {
        return array('message' => '用户名格式错误!','success' => false);
    } elseif (!validateInput($password, 'password')) {
        return array('message' => '密码格式错误!','success' => false);
    } elseif (!validateInput($email, 'email')) {
        return array('message' => '邮箱格式错误!','success' => false);
    } elseif (usernameExists($username)) {
        return array('message' => '用户名已存在!','success' => false);
    }elseif (emailExists($email)) {
        return array('message' => '邮箱已存在!','success' => false);
    }else {
        //验证码校验
        $result = verifyCode($codeid, $code, $email);
        if ($result['success']) {
            // 引入数据库信息
            global $mysqli; 
            // 生成盐值
            $salt = bin2hex(random_bytes(16));
            
            // 使用盐值和密码生成加密密码
            $hashedPassword = generateHashedPassword($password);
            
            // 插入用户数据到数据库
            $query = "INSERT INTO user (username, email, password, salt) VALUES (?, ?, ?, ?)";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ssss", $username, $email, $hashedPassword, $salt);
            
            if ($stmt->execute()) {
                return array("success" => true, "message" => "注册成功！");
            } else {
                return array("success" => false, "message" => "注册失败！");
            }

        }
        return array('message' => '验证码错误!','success' => false);
    }

}

//手机号注册
function register_phone($username, $password, $phone, $code,$codeid) {
    // 验证用户名、密码、邮箱
    if (!validateInput($username, 'username')) {
        return array('message' => '用户名格式错误!','success' => false);
    } elseif (!validateInput($password, 'password')) {
        return array('message' => '密码格式错误!','success' => false);
    } elseif (!validateInput($phone, 'phone')) {
        return array('message' => '手机号格式错误!','success' => false);
    } elseif (usernameExists($username)) {
        return array('message' => '用户名已存在!','success' => false);
    }elseif (phoneNumberExists($phone)) {
        return array('message' => '手机号已存在!','success' => false);
    }else {
        //验证码校验
        $result = verifyCode($codeid, $code);
        if ($result['success']) {
            // 引入数据库信息
            global $mysqli;
            // 生成盐值
            $salt = bin2hex(random_bytes(16));
            
            // 使用盐值和密码生成加密密码
            $hashedPassword = generateHashedPassword($password);
            
            // 插入用户数据到数据库
            $query = "INSERT INTO user (username, phone, password, salt) VALUES (?, ?, ?, ?)";
            $stmt = $mysqli->prepare($query);
            $stmt->bind_param("ssss", $username, $phone, $hashedPassword, $salt);
            
            if ($stmt->execute()) {
                return array("success" => true, "message" => "注册成功！");
            } else {
                return array("success" => false, "message" => "注册失败！");
            }

        }
        return array('message' => '验证码错误!','success' => false);
    }

}









// 检查用户名是否存在的函数
function usernameExists($username) {
    global $mysqli;
    $query = "SELECT COUNT(*) as count FROM user WHERE username = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];
    $stmt->close();
    return $count > 0;
}

// 检查邮箱是否存在的函数
function emailExists($email) {
    global $mysqli;
    $query = "SELECT COUNT(*) as count FROM user WHERE email = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];
    $stmt->close();
    return $count > 0;
}

// 检查手机号是否存在的函数
function phoneNumberExists($phoneNumber) {
    global $mysqli;
    $query = "SELECT COUNT(*) as count FROM user WHERE phone_number = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("s", $phoneNumber);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $count = $row['count'];
    $stmt->close();
    return $count > 0;
}

// 使用密码生成加密密码
function generateHashedPassword($password) {
    $options = [
        'cost' => 12,
    ];
    return password_hash($password, PASSWORD_BCRYPT, $options);
}