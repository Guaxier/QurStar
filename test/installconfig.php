<?php

// 接收前端传来的数据库信息
// 读取提交的 JSON 数据
$postData = file_get_contents("php://input");
$request = json_decode($postData);

// 提取数据库信息
$dbName = $request->dbname;
$dbUser = $request->username;
$dbPass = $request->password;

// 连接数据库
$mysqli = new mysqli("localhost", $dbUser, $dbPass, $dbName);

if ($mysqli->connect_error) {
    http_response_code(400);
    echo json_encode(['message' => '数据库信息错误']);
    exit;
}

// SQL语句数组
$sqlStatements = [
    'CREATE TABLE IF NOT EXISTS user (
    -- 用户唯一标识符
    user_id INT PRIMARY KEY AUTO_INCREMENT COMMENT \'用户ID\',
    -- 用户的账号，用于系统登录，不可更改
    username varchar(255) NOT NULL COMMENT \'账号\',
    -- 经过hash加密的密码，非明文存储
    password varchar(255) NOT NULL COMMENT \'密码\',
    -- hash密码的盐值，用于登录时验证密码
    salt varchar(255) NOT NULL COMMENT \'盐\',
    -- 用户的用户名，区别于账号，可更改，无法用于登录
    name varchar(255) COMMENT \'用户名\',
    -- 用户绑定的邮箱账号
    email varchar(255) COMMENT \'邮箱\',
    -- 用户绑定的手机号码
    phone_number varchar(15) COMMENT \'手机号\', -- 使用 char(10)/varchar(15)
    -- 用户账号状态:0-正常1-禁言2-封禁
    is_active tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT \'用户状态\',
    -- 用户权限类型:0-普通用户1-会员用户2.高级会3.普通管理4.高级管理
    user_role tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT \'用户类型\',
    -- 是否已经完成实名验证:0-未验证1-已验证
    real_name_verified tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT \'实名状态\',
    -- 用户的真实姓名和实名认证保持一致，系统获取
    full_name varchar(255) COMMENT \'真实姓名\',
    -- 用户常用的登录地址，判断用户的IP归属
    location varchar(255) COMMENT \'常用地址\',
    -- 用户的登录标志，登录时系统自动写入，过期后删除
    token varchar(255) COMMENT \'用户token\',
    -- 鉴权token的过期时间，系统自动写入，出现请求后自动续期
    token_expires_at datetime COMMENT \'token过期时间\',
    -- 用户进行密码重置时系统自动写入，过期和完成后删除
    reset_password_token varchar(255) COMMENT \'密码重置token\',
    -- 重置密码时有效，用户重置密码时系统写入
    reset_password_token_expires_at datetime COMMENT \'重置token过期时间\',
    -- 用户账号的注册时间，系统写入
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT \'注册时间\',
    -- 最后一次修改账号信息的时间，系统自动写入
    updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT \'最后一次更新时间\',
    -- 上一次登录的时间，系统自动写入
    last_login_at datetime COMMENT \'最后一次登录时间\'
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT=\'用户信息表\';
    ',
    'CREATE TABLE IF NOT EXISTS user_info (
        user_info_id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT UNIQUE COMMENT \'用户ID\',
        full_name VARCHAR(100) COMMENT \'用户昵称\',
        FOREIGN KEY (user_id) REFERENCES user(user_id)
    )',

    'CREATE TABLE IF NOT EXISTS user_verification (
        verification_id INT PRIMARY KEY AUTO_INCREMENT,
        user_id INT UNIQUE COMMENT \'用户ID\',
        token VARCHAR(100) COMMENT \'验证令牌\',
        expiration DATETIME NOT NULL COMMENT \'过期时间\',
        FOREIGN KEY (user_id) REFERENCES user(user_id)
    )',

    'CREATE TABLE IF NOT EXISTS VerificationCodes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        code VARCHAR(10) NOT NULL COMMENT \'验证码\',
        user_id TEXT COMMENT \'用户ID\',
        creation_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT \'验证码写入时间\'
    )'
];

// 执行SQL语句并返回结果
$tablesCreated = [];
foreach ($sqlStatements as $sql) {
    if ($mysqli->query($sql)) {
        $tablesCreated[] = [
            'tableName' => getTableNameFromSql($sql),
            'status' => '创建成功'
        ];
    } else {
        $tablesCreated[] = [
            'tableName' => getTableNameFromSql($sql),
            'status' => '创建失败'
        ];
    }
}

// 关闭数据库连接
$mysqli->close();

// 返回结果
$response = [
    'message' => '数据库连接成功！',
    'tableResults' => $tablesCreated
];

http_response_code(200);
echo json_encode($response);

// 辅助函数：从SQL语句中提取表名
function getTableNameFromSql(string $sql): string
{
    // 使用正则表达式提取表名
    preg_match('/CREATE TABLE IF NOT EXISTS\s+(\S+)/', $sql, $matches);
    
    // 如果正则匹配到了表名，则返回第一个捕获组的内容，否则返回空字符串
    return isset($matches[1]) ? $matches[1] : '';
}

 





/**
 * 获取当前PHP版本
 *
 * @return string 返回当前PHP环境的完整版本字符串，如 "7.4.15"
 */
function getPhpVersion(): string
{
    return PHP_VERSION;
}

/**
 * 获取当前MySQL版本
 *
 * @param mysqli $connection 已经建立的MySQLi连接实例，用于执行查询获取MySQL服务器版本信息
 * @return string 返回当前MySQL服务器的版本字符串，如 "5.7.34"
 */
/*function getMysqlVersion(mysqli $connection): string
{
    return $connection->server_version;
}
*/
/**
 * 检查mysqli或pdo_mysql扩展是否已安装
 *
 * @return bool 如果至少有一个扩展（mysqli或pdo_mysql）已安装，则返回true；否则返回false
 */
function isMysqliOrPdoMysqlInstalled(): bool
{
    return extension_loaded('mysqli') || extension_loaded('pdo_mysql');
}

/**
 * 获取服务器IP地址（IPv4+IPv6）
 *
 * @return array 返回一个包含服务器IP地址（可能包括IPv4和IPv6）的数组。如果没有可用的IP地址，返回空数组。
 *               注意：此函数依赖于`$_SERVER`超全局变量中的信息，实际结果可能受服务器配置、代理设置等因素影响。
 */
function getServerIpAddress(): array
{
    $ipAddresses = [];

    // IPv4
    if (isset($_SERVER['SERVER_ADDR'])) {
        $ipAddresses[] = $_SERVER['SERVER_ADDR'];
    }

    // IPv6
    if (isset($_SERVER['SERVER_IPV6'])) {
        $ipAddresses[] = $_SERVER['SERVER_IPV6'];
    }

    // Fallback: 使用gethostbyname获取主机名对应的IP地址列表，以防SERVER_ADDR或SERVER_IPV6未设置
    if (empty($ipAddresses)) {
        $hostname = gethostname();
        $ipAddresses = array_merge($ipAddresses, gethostbynamel($hostname));
    }

    return $ipAddresses;
}

/**
 * 获取当前访问客户端IP地址（IPv4+IPv6）
 *
 * @return array 返回一个包含客户端IP地址（可能包括IPv4和IPv6）的数组。如果没有可用的IP地址，返回空数组。
 *               注意：此函数依赖于`$_SERVER`超全局变量中的信息，实际结果可能受代理设置、负载均衡器等因素影响。
 *               特别是当客户端通过代理访问时，可能需要处理"X-Forwarded-For"等代理头来获取真实客户端IP。
 */
function getClientIpAddress(): array
{
    $ipAddresses = [];

    // IPv4
    if (isset($_SERVER['REMOTE_ADDR'])) {
        $ipAddresses[] = $_SERVER['REMOTE_ADDR'];
    }

    // IPv6
    if (isset($_SERVER['REMOTE_IPV6'])) {
        $ipAddresses[] = $_SERVER['REMOTE_IPV6'];
    }

    // Fallback: 检查可能包含客户端IP的HTTP头部（如"HTTP_CLIENT_IP", "HTTP_X_FORWARDED_FOR", "HTTP_X_REAL_IP"）
    foreach (['HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP'] as $header) {
        if (isset($_SERVER[$header])) {
            $ipList = explode(',', $_SERVER[$header]);
            foreach ($ipList as $ip) {
                $ip = trim($ip);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    $ipAddresses[] = $ip;
                }
            }
        }
    }

    return $ipAddresses;
}


