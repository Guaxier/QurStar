<?php

//全局正则表达式验证函数
function validateInput($input, $type) {
    switch ($type) {
        case 'phone':
            // 手机号验证，11位数字
            return preg_match('/^1[3456789]\d{9}$/', $input) ? true : false;
        
        case 'email':
            // 邮箱验证
            return filter_var($input, FILTER_VALIDATE_EMAIL) ? true : false;
        
        case 'username':
            // 用户名验证，只能使用大小写字母和数字，长度大于7
            return preg_match('/^[a-zA-Z0-9]{8,}$/', $input) ? true : false;
        
        case 'password':
            // 密码验证，必须包含字母和数字，长度大于8
            return preg_match('/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/', $input) ? true : false;
        
        default:
            return false;
    }
}


// 生成随机 token
function generate_token() {
    return bin2hex(random_bytes(50));
}