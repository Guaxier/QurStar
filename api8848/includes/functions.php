<?php
/**
 * 名称：validateInput
 * 时间：2024/05/13 创建
 * 作者：Guaxier
 * 功能：全局正则表达式验证函数，用于验证不同类型的输入数据是否符合预定义的格式。
 * 参数:
 * @param string $input 待验证的输入数据
 * @param string $type 验证类型，包括 'phone'（手机号）、'email'（邮箱）、'username'（用户名）、'password'（密码）
 * 
 * 返回:
 * @return bool 验证结果，true 表示验证通过，false 表示验证失败
 * 
 * 示例:
 * 
 * // 验证手机号
 * if (validateInput('13800138000', 'phone')) {
 *     echo '手机号格式正确';
 * } else {
 *     echo '手机号格式错误';
 * }
 * 
 * // 验证邮箱
 * if (validateInput('example@example.com', 'email')) {
 *     echo '邮箱格式正确';
 * } else {
 *     echo '邮箱格式错误';
 * }
 * 
 */
function validateInput(string $input, string $type): bool
{
    switch ($type) {
        case 'phone':
            // 手机号验证，11位数字，以1开头，第二位为3-9之间的数字
            return preg_match('/^1[3456789]\d{9}$/', $input);
        
        case 'email':
            // 邮箱验证，使用PHP内置函数filter_var进行检查
            return filter_var($input, FILTER_VALIDATE_EMAIL);
        
        case 'username':
            /** 用户名验证，支持两种格式：
             * 1. 以英文开头，包含大小写字母和数字，长度至少7
             * 2. 只包含2-4个中文字符
             */
            return (preg_match('/^[a-zA-Z][a-zA-Z0-9]{6,}$/', $input) 
            || preg_match('/^[\u4e00-\u9fa5]{2,4}$/', $input));
        
        case 'password':
            /**
             * 密码规则：
             * 
             * 至少一个字母和至少一个数字；
             * 至少一个字母和至少一个特殊字符；
             * 至少一个数字和至少一个特殊字符。
             * 定义的特殊字符集为 @!#$%&*-_+=
             */
            return preg_match('/^(?:(?=.*[A-Za-z])(?=.*\d)|(?=.*[A-Za-z])(?=.*[@!#$%&*-_+=])|(?=.*\d)(?=.*[@!#$%&*-_+=]))[A-Za-z\d@!#$%&*-_+=]{8,}$/', $input);
            
        
        default:
            return false; // 未知验证类型返回false
    }
}


// 生成随机 token
function generate_token() {
    return bin2hex(random_bytes(50));
}

/** 
 * 名称：generateAndOutputSecretKey
 * 名称：生成安全密钥
 * 时间：2024/05/13 创建
 * 作者：Guaxier
 * 功能：验证JWT格式的登录Token是否合法有效，包括检查签名、过期时间及基本结构。
 * 
 * 参数:
 * @param int $length 密钥的长度，默认为32位
 * @return void 直接输出生成的密钥
 * 
 * 示例:
 * 
 * 参数可选，如果需要生成不同长度的密钥，可以这样调用：例如，生成64位密钥：
 * generateAndOutputSecretKey(64); 
 */
function generateAndOutputSecretKey($length = 32) {
    // 使用random_bytes生成安全的随机字节
    $randomBytes = random_bytes($length);
    // 转换为易读的十六进制形式
    $secretKey = bin2hex($randomBytes);
    echo "Generated Secret Key: " . $secretKey . PHP_EOL;
}
