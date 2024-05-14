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
 * 名称：generateLoginToken
 * 名称：Token生成
 * 时间：2024/05/13 创建
 * 作者：Guaxier
 * 功能：生成JWT格式的登录Token，包含用户ID、签发时间及过期时间，使用HS256算法签名确保安全性。
 * 
 * 参数:
 * @param int $userId 用户ID，用于标识用户身份。
 * @param string $secretKey 签名密钥，用于生成Token的签名，确保Token的完整性和来源可靠性。
 * @param int $expiryTimeInSeconds Token过期时间（秒，默认3600秒即1小时）。
 * 
 * 返回:
 * @return string 生成的JWT格式的登录Token字符串。
 * 
 * 示例:
 * 
 * $token = generateLoginToken(123, 'your-secret-key', 3600);
 * // 使用生成的$token进行后续认证流程
 * 
 */
function generateLoginToken($userId, $secretKey, $expiryTimeInSeconds = 3600) {
    // 当前时间戳，用于Token的过期时间
    $iat = time();
    // 计算Token的过期时间
    $exp = $iat + $expiryTimeInSeconds;
    
    // 构建Token荷载(payload)，包含用户ID、签发时间和过期时间
    $payload = [
        'userId' => $userId,
        'iat' => $iat,
        'exp' => $exp,
    ];
    
    // 将荷载转换为JSON字符串
    $payloadJson = json_encode($payload);
    
    // 使用HMAC-SHA256算法和密钥对荷载进行签名
    $signature = hash_hmac('sha256', $payloadJson, $secretKey, true);
    
    // 对荷载和签名进行Base64Url编码，组成JWT
    $encodedPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payloadJson));
    $encodedSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
    // 返回JWT格式的Token字符串
    return $encodedPayload . '.' . $encodedSignature;
}

/**
 * 名称：validateLoginToken
 * 名称：Token验证
 * 时间：2024/05/13 创建
 * 作者：Guaxier
 * 功能：验证JWT格式的登录Token是否合法有效，包括检查签名、过期时间及基本结构。
 * 
 * 参数:
 * @param string $token 待验证的JWT Token字符串。
 * @param string $secretKey 用于验证Token签名的密钥。
 * 
 * 返回:
 * @return bool 如果Token有效且未过期返回true，否则返回false。
 * 
 * 示例:
 * 
 * $isValid = validateLoginToken($userToken, $mySecretKey);
 * if ($isValid) {
 *     echo 'Token有效';
 * } else {
 *     echo 'Token无效或已过期';
 * }
 * 
 */
function validateLoginToken($token, $secretKey) {
    // 分割Token为荷载和签名两部分，JWT标准格式应包含两部分由点分隔
    $parts = explode('.', $token);
    if (count($parts) !== 2) {
        // 格式错误，直接返回无效
        return false;
    }
    
    list($encodedPayload, $encodedSignature) = $parts;
    
    // Base64Url解码荷载部分
    $decodedPayload = json_decode(base64_decode(strtr($encodedPayload, '-_', '+/')), true);
    
    // 验证解码过程及荷载内容完整性
    if (json_last_error() !== JSON_ERROR_NONE || 
        !isset($decodedPayload['userId']) || 
        !isset($decodedPayload['iat']) || 
        !isset($decodedPayload['exp'])) {
        // 解码失败或缺少必要字段，Token无效
        return false;
    }
    
    // 检查Token是否已过期
    if ($decodedPayload['exp'] < time()) {
        // 已过期，Token无效
        return false;
    }
    
    // 使用相同的密钥重新计算签名
    $expectedSignature = hash_hmac('sha256', $encodedPayload, $secretKey, true);
    // 转换为Base64Url编码以便与Token中的签名比较
    $expectedSignatureBase64Url = strtr(base64_encode($expectedSignature), '+/', '-_');
    
    // 比较计算出的签名与Token中的签名，判断Token是否被篡改
    if ($encodedSignature !== $expectedSignatureBase64Url) {
        // 签名不匹配，Token无效
        return false;
    }
    
    // 所有验证步骤通过，Token有效
    return true;
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
