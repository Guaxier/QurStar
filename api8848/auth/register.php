<?php
// register.php

//邮箱注册
function register_email($username, $password, $email, $code,$codeid) {
    //引入数据库信息
    global $pdo;
    // 验证用户名、密码、邮箱
    if (!validateInput($username, 'username')) {
        return array('message' => '账号格式错误!','success' => false);
    } elseif (!validateInput($password, 'password')) {
        return array('message' => '密码格式错误!','success' => false);
    } elseif (!validateInput($email, 'email')) {
        return array('message' => '邮箱格式错误!','success' => false);
    } elseif (isUsernameExist($pdo,$username)) {
        return array('message' => '账号已存在!','success' => false);
    }elseif (isEmailExist($pdo,$email)) {
        return array('message' => '邮箱已存在!','success' => false);
    }else {
        //验证码校验
        $result = verifyCode($codeid, $code, $email);
        if ($result['success']) {
            // 生成加密密码
            $hashedPassword = generateHashedPassword($password);
            //调用邮箱注册信息写入函数
            $userId = registerWithEmail($pdo,$username,$hashedPassword,$email);
            //根据返回的ID判断是否注册成功，方便扩展
            if ($userId !== false) {
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
    //引入数据库信息
    global $pdo;
    // 验证用户名、密码、邮箱
    if (!validateInput($username, 'username')) {
        return array('message' => '账号格式错误!','success' => false);
    } elseif (!validateInput($password, 'password')) {
        return array('message' => '密码格式错误!','success' => false);
    } elseif (!validateInput($phone, 'phone')) {
        return array('message' => '手机号格式错误!','success' => false);
    } elseif (isUsernameExist($pdo,$username)) {
        return array('message' => '账号已存在!','success' => false);
    }elseif (isPhoneNumberExist($pdo,$phone)) {
        return array('message' => '手机号已存在!','success' => false);
    }else {
        //验证码校验
        $result = verifyCode($codeid, $code);
        if ($result['success']) {
            // 生成加密密码
            $hashedPassword = generateHashedPassword($password);
            //调用邮箱注册信息写入函数
            $userId = registerWithPhone($pdo,$username,$hashedPassword,$phone);
            //根据返回的ID判断是否注册成功，方便扩展
            if ($userId !== false) {
                return array("success" => true, "message" => "注册成功！");
            } else {
                return array("success" => false, "message" => "注册失败！");
            }

        }
        return array('message' => '验证码错误!','success' => false);
    }

}