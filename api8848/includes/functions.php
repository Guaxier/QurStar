<?php
/**
 * 名称：validateInput
 * 名称：正则校验
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



/**
 * 名称：isTableExists
 * 功能：检查数据库中指定表是否存在
 * 时间：2024/05/10 创建
 * 作者：依据您的信息填写
 * 说明：通过表名查询information_schema.tables，判断指定表是否存在于数据库中。
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param string $tableName 要查询的表名
 * 
 * 返回:
 * @return bool 表存在返回true，否则返回false
 * 
 * 示例:
 * 
 * if (isTableExists($pdo, 'users')) {
 *     echo '表已存在';
 * } else {
 *     echo '表不存在';
 * }
 * 
 */
function isTableExists(PDO $pdo, string $tableName): bool 
{
    try {
        // SQL查询语句，检查information_schema.tables中是否存在指定的表名
        $sql = "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = DATABASE() AND table_name = :tableName";
        
        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':tableName', $tableName, PDO::PARAM_STR);
        
        // 执行查询
        $stmt->execute();
        
        // 获取查询结果，如果计数大于0则表存在
        $exists = (bool) $stmt->fetchColumn();
        
        // 返回结果并根据要求格式化响应
        $response = ['exists' => $exists];
        return $response['exists'];
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("查询表存在状态失败！" . $e->getMessage());
        $response = ['exists' => false];
        return $response['exists']; // 在异常情况下默认认为表不存在
    }
}



/**
 * 名称：selectMessage
 * 名称：用户名是否存在
 * 名称：信息验证/查询
 * 时间：2024/05/16 创建
 * 作者：Guaxier
 * 功能：根据提供的用户名、邮箱、电话或令牌检查其存在性，并以JSON形式响应。
 *
 * 参数:
 * @param string|null $name 用户名（是否存在）
 * @param string|null $email 电子邮件地址（是否存在）
 * @param string|null $phone 电话号码（是否存在）
 * @param string|null $token 登录令牌（是否有效）
 * 
 * 返回:
 * @return void 本函数不直接返回值，而是输出一个JSON响应到HTTP流，包含键'exists'来指示查询的结果。
 *
 * 示例：
 * 检查用户名是否存在
 * selectMessage('usernameExample', null, null, null);
 * 
 */
function selectMessage(?string $name, ?string $email, ?string $phone, ?string $token): void {
    // 初始化响应数组
    $response = ['exists' => false];

    // 初始化数据库连接（确保在函数外部已定义并全局可用）
    global $pdo;

    // 检查并执行相应的查询
    if ($name !== null && $email === null && $phone === null && $token === null) {
        $isNameExist = isUsernameExist($pdo, $name);
        $response['exists'] = $isNameExist;
    } elseif ($email !== null && $name === null && $phone === null && $token === null) {
        $isEmailExist = isEmailExist($pdo, $email);
        $response['exists'] = $isEmailExist;
    } elseif ($phone !== null && $name === null && $email === null && $token === null) {
        $isPhoneExist = isPhoneNumberExist($pdo, $phone);
        $response['exists'] = $isPhoneExist;
    } elseif ($token !== null && $name === null && $email === null && $phone === null) {
        $userId = validateLoginToken($token); // 验证登录令牌
        $response['exists'] = ($userId !== false);
    } else {
        // 如果没有有效的参数组合，标记为不存在
        $response['exists'] = false;
    }

    // 输出JSON响应
    header('Content-Type: application/json');
    echo json_encode($response);
}



/**
 * 名称：isUsernameExist
 * 名称：账号是否存在
 * 时间：2024/05/10 创建
 * 作者：Guaxier
 * 功能：根据账号查询账号是否存在
 * 说明：通过username查询users表，判断指定账号是否存在。
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param string $username 要查询的账号
 * 
 * 返回:
 * @return bool 账号存在返回true，否则返回false
 * 
 * 示例:
 * 
 * if (isUsernameExist($pdo, 'exampleUser')) {
 *     echo '账号存在';
 * } else {
 *     echo '账号不存在';
 * }
 * 
 */
function isUsernameExist(PDO $pdo, string $username): bool 
{
    try {
        // SQL查询语句，检查users表中是否存在指定的username
        $sql = "SELECT COUNT(*) FROM users WHERE username = :username";
        
        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        
        // 执行查询
        $stmt->execute();
        
        // 获取查询结果，如果计数大于0则账号存在
        return (bool) $stmt->fetchColumn();
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("查询账号存在状态失败！" . $e->getMessage());
        return false; // 在异常情况下默认认为账号不存在
    }
}



/**
 * 名称：isPhoneNumberExist
 * 名称：手机号是否存在
 * 时间：2024/05/10 创建
 * 作者：Guaxier
 * 功能：根据手机号查询手机号是否存在
 * 说明：通过手机号查询users表，判断指定手机号是否已被注册。
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param string $phoneNumber 要查询的手机号
 * 
 * 返回:
 * @return bool 手机号存在返回true，否则返回false
 * 
 * 示例:
 * 
 * if (isPhoneNumberExist($pdo, '13800000000')) {
 *     echo '手机号已注册';
 * } else {
 *     echo '手机号未注册';
 * }
 * 
 */
function isPhoneNumberExist(PDO $pdo, string $phoneNumber): bool 
{
    try {
        // SQL查询语句，检查users表中是否存在指定的phone
        $sql = "SELECT COUNT(*) FROM users WHERE phone = :phoneNumber";
        
        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':phoneNumber', $phoneNumber, PDO::PARAM_STR);
        
        // 执行查询
        $stmt->execute();
        
        // 获取查询结果，如果计数大于0则手机号存在
        return (bool) $stmt->fetchColumn();
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("查询手机号存在状态失败！" . $e->getMessage());
        return false; // 在异常情况下默认认为手机号不存在
    }
}



/**
 * 名称：isEmailExist
 * 名称：邮箱是否存在
 * 时间：2024/05/10 创建
 * 作者：Guaxier
 * 功能：根据邮箱查询邮箱是否存在
 * 说明：通过邮箱查询users表，判断指定邮箱是否已被注册。
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param string $email 要查询的邮箱地址
 * 
 * 返回:
 * @return bool 邮箱存在返回true，否则返回false
 * 
 * 示例:
 * 
 * if (isEmailExist($pdo, 'example@example.com')) {
 *     echo '邮箱已注册';
 * } else {
 *     echo '邮箱未注册';
 * }
 * 
 */
function isEmailExist(PDO $pdo, string $email): bool 
{
    try {
        // SQL查询语句，检查users表中是否存在指定的email
        $sql = "SELECT COUNT(*) FROM users WHERE email = :email";
        
        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        
        // 执行查询
        $stmt->execute();
        
        // 获取查询结果，如果计数大于0则邮箱存在
        return (bool) $stmt->fetchColumn();
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("查询邮箱存在状态失败！" . $e->getMessage());
        return false; // 在异常情况下默认认为邮箱不存在
    }
}



/**
 * 名称：registerWithEmail
 * 名称：邮箱注册
 * 时间：2024/05/13 创建
 * 作者：Guaxier
 * 功能：实现邮箱注册功能，写入用户信息，返回用户ID，失败或异常均返回false
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param string $username 账号
 * @param string $hashedPassword 密码
 * @param string $email 邮箱地址
 * 
 * 返回:
 * @return int|false 成功返回用户ID，失败或异常返回false
 * 
 * 示例:
 * 
 * $userId = registerWithEmail($pdo, 'exampleUser', '$2y$10$yourHashedPassword', 'user@example.com');
 * if ($userId !== false) {
 *     echo '注册成功，用户ID: ' . $userId;
 * } else {
 *     echo '注册失败';
 * }
 * 
 */
function registerWithEmail(PDO $pdo, string $username, string $hashedPassword, string $email): int|false 
{
    try {
        // SQL插入语句，准备插入新用户数据
        $sqlInsert = "INSERT INTO users (username, PASSWORD, email) VALUES (:username, :hashedPassword, :email)";
        
        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sqlInsert);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':hashedPassword', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        
        // 执行插入操作
        $insertResult = $stmt->execute();
        
        // 如果插入成功，返回新插入的用户ID
        if ($insertResult) {
            return (int)$pdo->lastInsertId(); // 获取自动增长的用户ID
        }
        
        return false; // 插入失败
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("邮箱注册失败！" . $e->getMessage());
        return false; // 在异常情况下返回false
    }
}



/**
 * 名称：registerWithPhone
 * 名称：手机号注册
 * 时间：2024/05/13 创建
 * 作者：Guaxier
 * 功能：实现手机号注册功能，写入用户信息，返回用户ID，失败或异常均返回false
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param string $username 账号
 * @param string $hashedPassword 加密后的密码
 * @param string $phoneNumber 手机号码
 * 
 * 返回:
 * @return int|false 成功返回用户ID，失败或异常返回false
 * 
 * 示例:
 * 
 * $userId = registerWithPhone($pdo, 'exampleUser', '$2y$10$yourHashedPassword', '12345678901');
 * if ($userId !== false) {
 *     echo '注册成功，用户ID: ' . $userId;
 * } else {
 *     echo '注册失败';
 * }
 * 
 */
function registerWithPhone(PDO $pdo, string $username, string $hashedPassword, string $phoneNumber): int|false 
{
    try {
        // SQL插入语句，准备插入新用户数据
        $sqlInsert = "INSERT INTO users (username, PASSWORD, phone) VALUES (:username, :hashedPassword, :phoneNumber)";
        
        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sqlInsert);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':hashedPassword', $hashedPassword, PDO::PARAM_STR);
        $stmt->bindParam(':phoneNumber', $phoneNumber, PDO::PARAM_STR);
        
        // 执行插入操作
        $insertResult = $stmt->execute();
        
        // 如果插入成功，返回新插入的用户ID
        if ($insertResult) {
            return (int)$pdo->lastInsertId(); // 获取自动增长的用户ID
        }
        
        return false; // 插入失败
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("手机号注册失败！" . $e->getMessage());
        return false; // 在异常情况下返回false
    }
}



/**
 * 名称：generateHashedPassword
 * 名称：哈希密码生成
 * 时间：2024/05/16 创建
 * 作者：Guaxier
 * 功能：使用bcrypt算法生成安全的密码散列，适用于存储用户密码。
 *
 * 参数:
 * @param string $password 明文密码字符串，由用户输入或提供。
 *
 * 返回:
 * @return string 返回一个散列后的密码字符串，适合存储在数据库中。
 *
 * 注释：
 * 此函数使用PHP内置的`password_hash`函数与bcrypt算法，
 * 并设置了cost因子为12，这是一种平衡计算时间和安全性的方式。
 * Cost值越高，散列过程越慢，对攻击者的暴力破解也更加困难；
 * 但同时也会增加服务器在验证密码时的CPU负载。
 *
 * 示例：
 * $hashedPwd = generateHashedPassword('userChosenPassword');
 * // 然后将$hashedPwd保存到数据库中。
 */
function generateHashedPassword(string $password): string {
    $options = [
        'cost' => 12, // 默认cost值，可根据硬件性能进行调整
    ];
    return password_hash($password, PASSWORD_BCRYPT, $options);
}



/**
 * 名称：getUserIdByUsernameAndPassword
 * 名称：账号+密码登录
 * 时间：2024/05/11 创建
 * 作者：Guaxier
 * 功能：根据账号和密码查询用户ID，使用password_verify验证密码
 * 说明：先通过username查询数据库获取哈希密码，然后用password_verify验证输入的密码是否匹配，匹配成功则返回用户ID。
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param string $username 用户账号
 * @param string $password 用户输入的原始密码
 * 
 * 返回:
 * @return int|false 匹配成功返回用户ID，失败返回false
 * 
 * 示例:
 * 
 * $userId = getUserIdByUsernameAndPassword($pdo, 'exampleUser', 'userPassword123');
 * 
 * if ($userId !== false) {
 *      echo '用户ID: ' . $userId;
 * } else {
 *     echo '账号或密码错误';
 * }
 * 
 */
function getUserIdByUsernameAndPassword(PDO $pdo, string $username, string $password): int|false 
{
    try {
        // 第一步：根据username查询数据库获取哈希密码
        $sqlGetHashedPassword = "SELECT password FROM users WHERE username = :username";
        $stmtGetHashedPassword = $pdo->prepare($sqlGetHashedPassword);
        $stmtGetHashedPassword->bindParam(':username', $username, PDO::PARAM_STR);
        $stmtGetHashedPassword->execute();
        
        // 获取哈希密码
        $hashedPasswordFromDB = $stmtGetHashedPassword->fetchColumn();
        
        // 如果未找到用户或密码字段为空，则直接返回false
        if ($hashedPasswordFromDB === false) {
            return false;
        }
        
        // 第二步：使用password_verify验证密码
        if (password_verify($password, $hashedPasswordFromDB)) {
            // 密码验证成功，再次查询获取用户ID
            $sqlGetUserId = "SELECT user_id FROM users WHERE username = :username";
            $stmtGetUserId = $pdo->prepare($sqlGetUserId);
            $stmtGetUserId->bindParam(':username', $username, PDO::PARAM_STR);
            $stmtGetUserId->execute();
            
            // 返回用户ID
            return (int)$stmtGetUserId->fetchColumn();
        }
        
        return false; // 密码验证失败
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("查询用户ID或验证密码失败！" . $e->getMessage());
        return false; // 在异常情况下返回false
    }
}



/**
 * 名称：getUserIdByPhoneAndPassword
 * 名称：手机号登录
 * 时间：2024/05/11 创建
 * 作者：Guaxier
 * 功能：根据手机号和密码查询用户ID，使用password_verify验证密码
 * 说明：先通过phone查询数据库获取哈希密码，然后用password_verify验证输入的密码是否匹配，匹配成功则返回用户ID。
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param string $phone 用户手机号
 * @param string $password 用户输入的原始密码
 * 
 * 返回:
 * @return int|false 匹配成功返回用户ID，失败返回false
 * 
 * 示例:
 * 
 * $userId = getUserIdByphoneAndPassword($pdo, 'exampleUser', 'userPassword123');
 * 
 * if ($userId !== false) {
 *      echo '用户ID: ' . $userId;
 * } else {
 *     echo '手机号或密码错误';
 * }
 * 
 */
function getUserIdByPhoneAndPassword(PDO $pdo, string $phone, string $password): int|false 
{
    try {
        // 第一步：根据phone查询数据库获取哈希密码
        $sqlGetHashedPassword = "SELECT password FROM users WHERE phone = :phone";
        $stmtGetHashedPassword = $pdo->prepare($sqlGetHashedPassword);
        $stmtGetHashedPassword->bindParam(':phone', $phone, PDO::PARAM_STR);
        $stmtGetHashedPassword->execute();
        
        // 获取哈希密码
        $hashedPasswordFromDB = $stmtGetHashedPassword->fetchColumn();
        
        // 如果未找到用户或密码字段为空，则直接返回false
        if ($hashedPasswordFromDB === false) {
            return false;
        }
        
        // 第二步：使用password_verify验证密码
        if (password_verify($password, $hashedPasswordFromDB)) {
            // 密码验证成功，再次查询获取用户ID
            $sqlGetUserId = "SELECT user_id FROM users WHERE phone = :phone";
            $stmtGetUserId = $pdo->prepare($sqlGetUserId);
            $stmtGetUserId->bindParam(':phone', $phone, PDO::PARAM_STR);
            $stmtGetUserId->execute();
            
            // 返回用户ID
            return (int)$stmtGetUserId->fetchColumn();
        }
        
        return false; // 密码验证失败
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("查询用户ID或验证密码失败！" . $e->getMessage());
        return false; // 在异常情况下返回false
    }
}



/**
 * 名称：getUserIdByEmailAndPassword
 * 名称：邮箱登录
 * 时间：2024/05/11 创建
 * 作者：Guaxier
 * 功能：根据邮箱和密码查询用户ID，使用password_verify验证密码
 * 说明：先通过email查询数据库获取哈希密码，然后用password_verify验证输入的密码是否匹配，匹配成功则返回用户ID。
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param string $email 用户邮箱
 * @param string $password 用户输入的原始密码
 * 
 * 返回:
 * @return int|false 匹配成功返回用户ID，失败返回false
 * 
 * 示例:
 * 
 * $userId = getUserIdByemailAndPassword($pdo, 'exampleUser', 'userPassword123');
 * 
 * if ($userId !== false) {
 *      echo '用户ID: ' . $userId;
 * } else {
 *     echo '邮箱或密码错误';
 * }
 * 
 */
function getUserIdByEmailAndPassword(PDO $pdo, string $email, string $password): int|false 
{
    try {
        // 第一步：根据email查询数据库获取哈希密码
        $sqlGetHashedPassword = "SELECT password FROM users WHERE email = :email";
        $stmtGetHashedPassword = $pdo->prepare($sqlGetHashedPassword);
        $stmtGetHashedPassword->bindParam(':email', $email, PDO::PARAM_STR);
        $stmtGetHashedPassword->execute();
        
        // 获取哈希密码
        $hashedPasswordFromDB = $stmtGetHashedPassword->fetchColumn();
        
        // 如果未找到用户或密码字段为空，则直接返回false
        if ($hashedPasswordFromDB === false) {
            return false;
        }
        
        // 第二步：使用password_verify验证密码
        if (password_verify($password, $hashedPasswordFromDB)) {
            // 密码验证成功，再次查询获取用户ID
            $sqlGetUserId = "SELECT user_id FROM users WHERE email = :email";
            $stmtGetUserId = $pdo->prepare($sqlGetUserId);
            $stmtGetUserId->bindParam(':email', $email, PDO::PARAM_STR);
            $stmtGetUserId->execute();
            
            // 返回用户ID
            return (int)$stmtGetUserId->fetchColumn();
        }
        
        return false; // 密码验证失败
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("查询用户ID或验证密码失败！" . $e->getMessage());
        return false; // 在异常情况下返回false
    }
}



/**
 * 名称：generateLoginToken
 * 名称：token生成
 * 时间：2024/05/14 创建
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
    
    // 对荷载进行Base64Url编码
    $encodedPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payloadJson));
    
    // 使用HMAC-SHA256算法和密钥对编码后的荷载进行签名
    $signature = hash_hmac('sha256', $encodedPayload, $secretKey, true);
    $encodedSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
    file_put_contents('1.txt', '原token' . $encodedPayload . '.' . $encodedSignature, FILE_APPEND); // 调试
    // 返回JWT格式的Token字符串
    return $encodedPayload . '.' . $encodedSignature;
}



/**
 * 名称：validateLoginToken
 * 名称：Token有效性验证
 * 时间：2024/05/14 创建
 * 作者：Guaxier
 * 功能：验证JWT格式的登录Token的有效性，并返回用户的ID。如果Token无效、过期或签名不匹配，则返回false。
 * 
 * 参数:
 * @param string $token JWT格式的登录Token字符串。
 * @param string $secretKey 用于验证Token签名的密钥。
 * 
 * 返回:
 * @return mixed 验证成功返回用户ID(int)，验证失败返回false。
 * 
 * 示例：
 * 
 * $userId = getUserIdByUsernameAndPassword($pdo,$username,$password);
 * if ($userId !== false) {
 *      echo $userId;
 * } else {
 *      echo 'Token无效或已过期';
 * }
 * 
 */
function validateLoginToken($token, $secretKey = DB_secretKey) {
    // 分割JWT的两部分：荷载与签名
    list($encodedPayload, $encodedSignature) = explode('.', $token);
    if (count([$encodedPayload, $encodedSignature]) !== 2) {
        return false; // Token格式错误
    }
    
    // Base64Url解码荷载
    $payloadJson = base64_decode(strtr($encodedPayload, '-_', '+/'));
    if ($payloadJson === false) {
        return false; // 荷载解码失败
    }
    
    // 解析荷载中的数据
    $payload = json_decode($payloadJson, true);
    if (json_last_error() !== JSON_ERROR_NONE || !isset($payload['userId']) || !isset($payload['iat']) || !isset($payload['exp'])) {
        return false; // 荷载解析失败或缺少必要字段
    }
    
    // 检查Token是否已过期
    if ($payload['exp'] < time()) {
        return false; // Token已过期
    }
    
    // 重新计算签名进行比较
    $expectedSignature = hash_hmac('sha256', $encodedPayload, $secretKey, true);
    $expectedSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($expectedSignature));
    if (!hash_equals($expectedSignature, $encodedSignature)) {
        return false; // 签名不匹配
    }
    
    // 如果所有检查通过，返回用户ID
    return (int)$payload['userId'];
}


