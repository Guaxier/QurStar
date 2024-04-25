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
        user_id INT PRIMARY KEY AUTO_INCREMENT,
        username VARCHAR(50) UNIQUE NOT NULL,
        email VARCHAR(100) COMMENT \'电子邮件\',
        phone_number VARCHAR(20) COMMENT \'电话号码\',
        password VARCHAR(100) NOT NULL
    )',

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


