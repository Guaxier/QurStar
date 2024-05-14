<?php
/**
 * 名称：getUserInfoById
 * 时间：2024/05/10 创建
 * 作者：Guaxier
 * 功能：根据用户ID查询用户信息
 * 说明：根据给定的用户ID从users表中获取用户的全部信息
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param int $userId 用户ID
 * 
 * 返回:
 * @return array 用户信息数组，如果没有找到则返回null
 * 
 * 示例:
 * 
 * $userInfo = getUserInfoById($pdo, 123);
 * if ($userInfo) {
 *     print_r($userInfo);
 * } else {
 *     echo '用户不存在';
 * }
 * 
 */
function getUserInfoById(PDO $pdo, int $userId): ?array 
{
    try {
        // SQL查询语句，根据$user_id获取users表中的用户信息
        $sql = "SELECT * FROM users WHERE user_id = :userId";
        
        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        
        // 执行查询
        $stmt->execute();
        
        // 获取第一条结果（基于user_id应该是唯一的，所以期望最多一条记录）
        $userInfo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        return $userInfo; // 直接返回查询结果，如果没有记录则为null
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("查询用户信息失败！" . $e->getMessage());
        return null;
    }
}

/**
 * 名称：isTokenExpired
 * 时间：2024/05/10 创建
 * 作者：Guaxier
 * 功能：根据用户ID查询用户token是否已过期
 * 说明：通过比较当前时间与用户记录中的token_expires_at字段判断token是否过期。
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param int $userId 用户ID
 * 
 * 返回:
 * @return bool Token已过期返回true，未过期返回false
 * 
 * 示例:
 * 
 * if (isTokenExpired($pdo, 123)) {
 *     echo 'Token已过期';
 * } else {
 *     echo 'Token有效';
 * }
 * 
 */
function isTokenExpired(PDO $pdo, int $userId): bool 
{
    try {
        // SQL查询语句，获取指定user_id的token过期时间
        $sql = "SELECT token_expires_at FROM users WHERE user_id = :userId";
        
        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        
        // 执行查询
        $stmt->execute();
        
        // 获取token过期时间
        $tokenExpiresAt = $stmt->fetchColumn();
        
        // 检查token过期时间是否为空或已过期
        if ($tokenExpiresAt === false || strtotime($tokenExpiresAt) < time()) {
            return true;
        }
        
        return false;
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("查询token过期状态失败！" . $e->getMessage());
        return true; // 在异常情况下，默认认为token可能已过期或有问题
    }
}

/**
 * 名称：isRealNameVerifiedById
 * 时间：2024/05/10 创建
 * 作者：Guaxier
 * 功能：根据用户ID查询用户是否已完成实名认证
 * 说明：通过用户ID查询users表中real_name_verified字段，判断用户是否已进行实名认证。
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param int $userId 用户ID
 * 
 * 返回:
 * @return int 1表示已验证，0表示未验证，false查询失败
 * 
 * 示例:
 * 
 * $verifiedStatus = isRealNameVerifiedById($pdo, 123);
 * if ($verifiedStatus !== false) {
 *     echo $verifiedStatus == 1 ? '已实名认证' : '未实名认证';
 * } else {
 *     echo '查询实名认证状态失败';
 * }
 * 
 */
function isRealNameVerifiedById(PDO $pdo, int $userId): ?int 
{
    try {
        // SQL查询语句，获取指定user_id的实名认证状态
        $sql = "SELECT real_name_verified FROM users WHERE user_id = :userId";
        
        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        
        // 执行查询
        $stmt->execute();
        
        // 获取实名认证状态
        $verifiedStatus = $stmt->fetchColumn();
        
        return $verifiedStatus !== false ? (int)$verifiedStatus : null;
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("查询实名认证状态失败！" . $e->getMessage());
        return null; // 在异常情况下返回null表示查询失败
    }
}

/**
 * 名称：isUsernameExist
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
 * 名称：isFullNameExist
 * 时间：2024/05/10 创建
 * 作者：Guaxier
 * 功能：根据用户名查询用户名是否存在
 * 说明：通过full_name查询users表，判断指定用户名是否存在。
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param string $fullName 要查询的用户名（全名）
 * 
 * 返回:
 * @return bool 用户名存在返回true，否则返回false
 * 
 * 示例:
 * 
 * if (isFullNameExist($pdo, '张三')) {
 *     echo '用户名存在';
 * } else {
 *     echo '用户名不存在';
 * }
 * 
 */
function isFullNameExist(PDO $pdo, string $fullName): bool 
{
    try {
        // SQL查询语句，检查users表中是否存在指定的full_name
        $sql = "SELECT COUNT(*) FROM users WHERE full_name = :fullName";
        
        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':fullName', $fullName, PDO::PARAM_STR);
        
        // 执行查询
        $stmt->execute();
        
        // 获取查询结果，如果计数大于0则用户名存在
        return (bool) $stmt->fetchColumn();
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("查询用户名存在状态失败！" . $e->getMessage());
        return false; // 在异常情况下默认认为用户名不存在
    }
}

/**
 * 名称：checkUserIsActive
 * 时间：2024/05/10 创建
 * 作者：Guaxier
 * 功能：根据用户ID查询用户状态是否为正常（0表示正常）
 * 说明：通过用户ID查询users表，判断用户状态字段is_active是否为0，表示状态正常。
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param int $userId 用户ID
 * 
 * 返回:
 * @return bool 状态正常返回true，否则返回false
 * 
 * 示例:
 * 
 * if (checkUserIsActive($pdo, 123)) {
 *     echo '用户状态正常';
 * } else {
 *     echo '用户状态异常';
 * }
 * 
 */
function checkUserIsActive(PDO $pdo, int $userId): bool 
{
    try {
        // SQL查询语句，根据user_id检查is_active字段是否为0
        $sql = "SELECT is_active FROM users WHERE user_id = :userId";
        
        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        
        // 执行查询
        $stmt->execute();
        
        // 获取查询结果，如果is_active为0则状态正常
        $isActive = (int)$stmt->fetchColumn();
        return $isActive === 0;
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("查询用户状态失败！" . $e->getMessage());
        return false; // 在遇到数据库错误时默认返回状态异常
    }
}

/**
 * 名称：isPhoneNumberExist
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
        // SQL查询语句，检查users表中是否存在指定的phone_number
        $sql = "SELECT COUNT(*) FROM users WHERE phone_number = :phoneNumber";
        
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
 * 名称：isValidTokenByAccountAndToken
 * 时间：2024/05/10 创建
 * 作者：Guaxier
 * 功能：根据账号和token查询token是否过期，账号与token不匹配时同样返回false。
 * 说明：通过username和token查询users表，验证token有效性及是否过期。
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param string $username 用户账号
 * @param string $token 用户的token
 * 
 * 返回:
 * @return bool token有效且未过期返回true，否则返回false
 * 
 * 示例:
 * 
 * if (isValidTokenByAccountAndToken($pdo, 'exampleUser', 'sometoken')) {
 *     echo 'Token有效';
 * } else {
 *     echo 'Token无效或已过期';
 * }
 * 
 */
function isValidTokenByAccountAndToken(PDO $pdo, string $username, string $token): bool 
{
    try {
        // SQL查询语句，根据username和token检查token有效性及是否过期
        $sql = "SELECT token_expires_at FROM users WHERE username = :username AND token = :token";
        
        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':token', $token, PDO::PARAM_STR);
        
        // 执行查询
        $stmt->execute();
        
        // 获取token过期时间
        $tokenExpiresAt = $stmt->fetchColumn();
        
        // 验证token是否匹配且未过期
        if ($tokenExpiresAt !== false && strtotime($tokenExpiresAt) > time()) {
            return true;
        }
        
        return false;
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("查询token有效性失败！" . $e->getMessage());
        return false; // 在异常情况下默认认为token无效
    }
}

/**
 * 名称：deleteResetPasswordTokenById
 * 时间：2024/05/10 创建
 * 作者：Guaxier
 * 功能：根据用户ID删除密码重置token及过期时间
 * 说明：通过用户ID更新users表，清空reset_password_token和reset_password_token_expires_at字段。
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param int $userId 用户ID
 * 
 * 返回:
 * @return bool 操作成功返回true，失败返回false
 * 
 * 示例:
 * 
 * if (deleteResetPasswordTokenById($pdo, 123)) {
 *     echo '密码重置token删除成功';
 * } else {
 *     echo '删除操作失败';
 * }
 * 
 */
function deleteResetPasswordTokenById(PDO $pdo, int $userId): bool 
{
    try {
        // SQL更新语句，根据user_id清空reset_password_token和reset_password_token_expires_at
        $sql = "UPDATE users SET reset_password_token = NULL, reset_password_token_expires_at = NULL WHERE user_id = :userId";
        
        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        
        // 执行更新操作
        $affectedRows = $stmt->execute();
        
        // 判断影响行数来确定操作是否成功
        return $affectedRows > 0;
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("删除密码重置token失败！" . $e->getMessage());
        return false; // 在异常情况下默认操作失败
    }
}

/**
 * 名称：getUserIdByUsernameAndPassword
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
 * 名称：getUserIdByEmailAndPassword
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
 * 名称：getUserIdByPhoneAndPassword
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
 * 名称：createUserWithHashedPassword
 * 时间：2024/05/11 创建
 * 作者：Guaxier
 * 功能：根据账号和密码创建新用户
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param string $username 用户账号
 * @param string $password 用户密码（明文）
 * 
 * 返回:
 * @return int|false 成功返回用户ID，失败返回false
 * 
 * 示例:
 * 
 * $userId = createUserWithHashedPassword($pdo, 'newUser', 'securePass123!');
 * if ($userId !== false) {
 *     echo '用户创建成功，用户ID: ' . $userId;
 * } else {
 *     echo '用户创建失败';
 * }
 * 
 */
function createUserWithHashedPassword(PDO $pdo, string $username, string $password): int|false 
{
    try {
        // 生成哈希密码
        $hashedPassword = generateHashedPassword($password);
        
        // SQL插入语句，准备插入新用户数据
        $sqlInsert = "INSERT INTO users (username, PASSWORD) VALUES (:username, :hashedPassword)";
        
        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sqlInsert);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->bindParam(':hashedPassword', $hashedPassword, PDO::PARAM_STR);
        
        // 执行插入操作
        $insertResult = $stmt->execute();
        
        // 如果插入成功，返回新插入的用户ID
        if ($insertResult) {
            return (int)$pdo->lastInsertId(); // 获取自动增长的用户ID
        }
        
        return false; // 插入失败
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("创建用户失败！" . $e->getMessage());
        return false; // 在异常情况下返回false
    }
}

/**
 * 名称：getEmailByUserId
 * 时间：2024/05/11 创建
 * 作者：Guaxier
 * 功能：根据用户ID查询邮箱是否绑定，已绑定返回邮箱地址，未绑定返回false
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param int $userId 用户ID
 * 
 * 返回:
 * @return string|false 邮箱地址或false（未绑定）
 * 
 * 示例:
 * 
 * $email = getEmailByUserId($pdo, 1);
 * if ($email !== false) {
 *     echo '绑定的邮箱: ' . $email;
 * } else {
 *     echo '邮箱未绑定';
 * }
 * 
 */
function getEmailByUserId(PDO $pdo, int $userId): string|false 
{
    try {
        // SQL查询语句，根据user_id查询email
        $sql = "SELECT email FROM users WHERE user_id = :userId";
        
        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        
        // 执行查询
        $stmt->execute();
        
        // 获取查询结果
        $email = $stmt->fetchColumn();
        
        // 如果查询到邮箱地址则返回，否则返回false表示未绑定
        return $email !== false ? $email : false;
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("查询邮箱绑定状态失败！" . $e->getMessage());
        return false; // 在异常情况下返回false
    }
}

/**
 * 名称：getPhoneNumberByUserId
 * 时间：2024/05/11 创建
 * 作者：Guaxier
 * 功能：根据用户ID查询手机号是否绑定，已绑定返回手机号，未绑定返回false
 * 参数:
 * @param PDO $pdo 数据库连接实例
 * @param int $userId 用户ID
 * 
 * 返回:
 * @return string|false 手机号码或false（未绑定）
 * 
 * 示例:
 * 
 * $phoneNumber = getPhoneNumberByUserId($pdo, 1);
 * if ($phoneNumber !== false) {
 *     echo '绑定的手机号: ' . $phoneNumber;
 * } else {
 *     echo '手机号未绑定';
 * }
 * 
 */
function getPhoneNumberByUserId(PDO $pdo, int $userId): string|false 
{
    try {
        // SQL查询语句，根据user_id查询phone_number
        $sql = "SELECT phone_number FROM users WHERE user_id = :userId";
        
        // 准备语句并绑定参数
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);
        
        // 执行查询
        $stmt->execute();
        
        // 获取查询结果
        $phoneNumber = $stmt->fetchColumn();
        
        // 如果查询到手机号则返回，否则返回false表示未绑定
        return $phoneNumber !== false ? $phoneNumber : false;
    } catch (PDOException $e) {
        // 错误处理，记录日志或抛出异常等
        error_log("查询手机号绑定状态失败！" . $e->getMessage());
        return false; // 在异常情况下返回false
    }
}

/**
 * 名称：registerWithEmail
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
        $sqlInsert = "INSERT INTO users (username, PASSWORD, phone_number) VALUES (:username, :hashedPassword, :phoneNumber)";
        
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

// /**
//  * 名称：validateLoginToken
//  * 名称：token验证
//  * 时间：2024/05/14 创建
//  * 作者：Guaxier
//  * 功能：验证JWT格式的登录Token是否合法有效，包括检查签名、过期时间及基本结构。
//  * 
//  * 参数:
//  * @param string $token 待验证的JWT Token字符串。
//  * @param string $secretKey 用于验证Token签名的密钥。
//  * 
//  * 返回:
//  * @return bool 如果Token有效且未过期返回true，否则返回false。
//  * 
//  * 示例:
//  * 
//  * $isValid = validateLoginToken($userToken, $mySecretKey);
//  * if ($isValid) {
//  *     echo 'Token有效';
//  * } else {
//  *     echo 'Token无效或已过期';
//  * }
//  * 
//  */
// function validateLoginToken($token, $secretKey) {
//     // 分割Token为荷载和签名两部分，JWT标准格式应包含两部分由点分隔
//     $parts = explode('.', $token);
//     if (count($parts) !== 2) {
//         // 格式错误，直接返回无效
//         return false;
//     }
    
//     list($encodedPayload, $encodedSignature) = $parts;
    
//     // Base64Url解码荷载部分
//     $decodedPayload = json_decode(base64_decode(strtr($encodedPayload, '-_', '+/')), true);
    
//     // 验证解码过程及荷载内容完整性
//     if (json_last_error() !== JSON_ERROR_NONE || 
//         !isset($decodedPayload['userId']) || 
//         !isset($decodedPayload['iat']) || 
//         !isset($decodedPayload['exp'])) {
//         // 解码失败或缺少必要字段，Token无效
//         return false;
//     }
    
//     // 检查Token是否已过期
//     if ($decodedPayload['exp'] < time()) {
//         // 已过期，Token无效
//         return false;
//     }
    
//     // 使用相同的密钥重新计算签名
//     $expectedSignature = hash_hmac('sha256', $encodedPayload, $secretKey, true);
//     // 转换为Base64Url编码以便与Token中的签名比较
//     $expectedSignatureBase64Url = strtr(base64_encode($expectedSignature), '+/', '-_');
    
//     // 比较计算出的签名与Token中的签名，判断Token是否被篡改
//     if ($encodedSignature !== $expectedSignatureBase64Url) {
//         // 签名不匹配，Token无效
//         return false;
//     }
    
//     // 所有验证步骤通过，Token有效
//     return true;
// }

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
    
    // 使用HMAC-SHA256算法和密钥对荷载进行签名
    $signature = hash_hmac('sha256', $payloadJson, $secretKey, true);
    
    // 对荷载和签名进行Base64Url编码，组成JWT
    $encodedPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payloadJson));
    $encodedSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
    
    // 返回JWT格式的Token字符串
    return $encodedPayload . '.' . $encodedSignature;
}

/**
 * 名称：extendTokenExpiration
 * 时间：2024/05/14 创建
 * 作者：Guaxier
 * 功能：延长给定JWT Token的有效期，并返回新的Token字符串。
 * 
 * 参数:
 * @param string $token 待延长的JWT Token字符串。
 * @param string $secretKey 用于验证和重新签名Token的密钥。
 * @param int $additionalExpiryTimeInSeconds 希望延长的过期时间（秒）。
 * 
 * 返回:
 * @return string 更新过期时间后的JWT格式的登录Token字符串，如果原Token无效则返回null。
 * 
 * 示例:
 * 
 * $newToken = extendTokenExpiration($existingToken, $mySecretKey, 3600);
 * if ($newToken) {
 *     echo 'Token有效期已延长';
 * } else {
 *     echo '无法延长Token有效期，原Token可能已失效';
 * }
 * 
 */
function extendTokenExpiration($token, $secretKey, $additionalExpiryTimeInSeconds) {
    // 首先验证原始Token是否有效
    if (validateLoginToken($token, $secretKey === false)) {
        // 如果Token无效，直接返回null
        return null;
    }
    
    // 解析Token荷载获取原始过期时间
    $decodedPayload = json_decode(base64_decode(strtr(explode('.', $token)[0], '-_', '+/')), true);
    if (json_last_error() !== JSON_ERROR_NONE) {
        // 解码失败，返回null
        return null;
    }
    
    // 延长过期时间
    $newExp = $decodedPayload['exp'] + $additionalExpiryTimeInSeconds;
    
    // 重新生成Token，使用新的过期时间
    return generateLoginToken($decodedPayload['userId'], $secretKey, $newExp - time());
}

/**
 * 名称：validateLoginToken
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
function validateLoginToken($token, $secretKey) {
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
    $actualSignature = base64_decode(strtr($encodedSignature, '-_', '+/'), true);
    if ($actualSignature === false || !hash_equals($expectedSignature, $actualSignature)) {
        return false; // 签名不匹配
    }
    
    // 如果所有检查通过，返回用户ID
    return (int)$payload['userId'];
}







// 使用密码生成哈希密码
function generateHashedPassword($password) {
    $options = [
        'cost' => 12,
    ];
    return password_hash($password, PASSWORD_BCRYPT, $options);
}