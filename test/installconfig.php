<?php
// 显示执行情况
echo '<div><h3>执行情况</h3><ul>';

// 获取POST请求中的数据库配置信息
$dbHost = $_POST['dbHost'];
$dbName = $_POST['dbName'];
$dbUser = $_POST['dbUser'];
$dbPassword = $_POST['dbPassword'];

// 尝试连接数据库并执行初始化操作
try {
    $connection = mysqli_connect($dbHost, $dbUser, $dbPassword, $dbName);

    if (!$connection) {
        throw new Exception("数据库连接失败: " . mysqli_connect_error());
    }

    // 初始化数据库，执行相关操作
    $sqlStatements = [
        // 角色表
        'CREATE TABLE IF NOT EXISTS Roles(
        RoleID INT AUTO_INCREMENT PRIMARY KEY COMMENT \'角色ID\',
        RoleName VARCHAR(255) NOT NULL UNIQUE COMMENT \'角色名称\',
        Description VARCHAR(255) COMMENT \'角色的基本描述\'
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = \'角色表\'',

    // 权限表
    'CREATE TABLE IF NOT EXISTS Permissions(
        PermissionID INT AUTO_INCREMENT PRIMARY KEY COMMENT \'权限ID\',
        PermissionName VARCHAR(255) NOT NULL UNIQUE COMMENT \'权限名称\',
        Description VARCHAR(255) COMMENT \'权限的基本描述\'
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = \'权限表\'',

    // 权限关联表
    'CREATE TABLE IF NOT EXISTS Role_Permissions(
        RoleID INT NOT NULL,
        PermissionID INT NOT NULL,
        PRIMARY KEY(RoleID, PermissionID),
        FOREIGN KEY(RoleID) REFERENCES Roles(RoleID) ON DELETE CASCADE,
        FOREIGN KEY(PermissionID) REFERENCES Permissions(PermissionID) ON DELETE CASCADE
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = \'角色与权限关联表\'',

    // 用户表
    'CREATE TABLE IF NOT EXISTS users(
        user_id INT PRIMARY KEY AUTO_INCREMENT COMMENT \'用户ID\',
        username VARCHAR(255) NOT NULL COMMENT \'账号\',
        password VARCHAR(255) NOT NULL COMMENT \'密码\',
        salt VARCHAR(255) COMMENT \'盐\',
        name VARCHAR(255) COMMENT \'用户名\',
        email VARCHAR(255) COMMENT \'邮箱\',
        phone_number VARCHAR(15) COMMENT \'手机号\',
        is_active TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT \'用户状态\',
        user_role INT COMMENT \'权限\',
        real_name_verified TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT \'实名状态\',
        full_name VARCHAR(255) COMMENT \'真实姓名\',
        location VARCHAR(255) COMMENT \'常用地址\',
        token VARCHAR(255) COMMENT \'用户token\',
        token_expires_at DATETIME COMMENT \'token过期时间\',
        reset_password_token VARCHAR(255) COMMENT \'密码重置token\',
        reset_password_token_expires_at DATETIME COMMENT \'重置token过期时间\',
        created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT \'注册时间\',
        updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT \'最后一次更新时间\',
        last_login_at DATETIME COMMENT \'最后一次登录时间\'
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = \'用户信息表\';',

    // 登录表
    'CREATE TABLE IF NOT EXISTS UserLoginRecords(
        LoginRecordID INT AUTO_INCREMENT PRIMARY KEY COMMENT \'登录记录ID\',
        user_id INT NOT NULL COMMENT \'关联用户ID\',
        FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        Login_Method VARCHAR(255) COMMENT \'登录方式\',
        Device VARCHAR(255) COMMENT \'登录设备\',
        Login_Time TIMESTAMP COMMENT \'登录时间\',
        Logout_Time TIMESTAMP COMMENT \'登出时间\',
        Location VARCHAR(255) COMMENT \'登录地点\',
        IP_Address VARCHAR(255) COMMENT \'IP地址\',
        Browser_Info VARCHAR(255) COMMENT \'浏览器信息\',
        Login_Status TINYINT(4) UNSIGNED NOT NULL COMMENT \'登录状态\',
        Authentication_Details VARCHAR(255) COMMENT \'验证详情\',
        Remarks VARCHAR(255) COMMENT \'备注\'
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = \'登录表\'',

    // 状态表
    'CREATE TABLE IF NOT EXISTS AccountStatus(
        AccountStatusID INT AUTO_INCREMENT PRIMARY KEY COMMENT \'ID\',
        user_id INT NOT NULL COMMENT \'关联用户\',
        FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        STATUS TINYINT(4) UNSIGNED NOT NULL COMMENT \'状态\',
        reason VARCHAR(255) COMMENT \'状态原因\',
        notes TEXT COMMENT \'备注\',
        operator_id VARCHAR(255) COMMENT \'操作者ID\',
        start_time TIMESTAMP COMMENT \'开始时间\',
        end_time TIMESTAMP COMMENT \'结束时间\',
        update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT \'更新时间\'
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = \'账号状态表\'',

    // 曾用邮箱表
    'CREATE TABLE IF NOT EXISTS UserEmailHistory(
        UserEmailHistoryID INT AUTO_INCREMENT PRIMARY KEY COMMENT \'ID\',
        user_id INT NOT NULL COMMENT \'关联用户ID\',
        FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        NewEmail VARCHAR(255) NOT NULL COMMENT \'新邮箱\',
        Remarks VARCHAR(255) COMMENT \'备注\',
        IPAddress VARCHAR(45) COMMENT \'IP地址\',
        OperatorID VARCHAR(255) COMMENT \'操作者ID\',
        ChangeTimestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT \'变更时间\',
        ChangeReason VARCHAR(255) COMMENT \'原因或备注\'
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = \'曾用邮箱表\'',

    // 曾用户名表
    'CREATE TABLE IF NOT EXISTS UsernameHistory(
        UsernameHistoryID INT AUTO_INCREMENT PRIMARY KEY COMMENT \'ID\',
        user_id INT NOT NULL COMMENT \'关联用户ID\',
        FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        NewUsername VARCHAR(255) NOT NULL COMMENT \'新用户名\',
        Remarks VARCHAR(255) COMMENT \'备注\',
        IPAddress VARCHAR(45) COMMENT \'IP地址\',
        OperatorID VARCHAR(255) COMMENT \'操作者ID\',
        ChangeTimestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT \'变更时间\',
        ChangeReason VARCHAR(255) COMMENT \'原因或备注\'
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = \'曾用户名表\'',

    // 曾用手机号表
    'CREATE TABLE IF NOT EXISTS UserPhoneHistory(
        UserPhoneHistoryID INT AUTO_INCREMENT PRIMARY KEY COMMENT \'ID\',
        user_id INT NOT NULL COMMENT \'关联用户ID\',
        FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        NewPhone VARCHAR(255) NOT NULL COMMENT \'新手机\',
        Remarks VARCHAR(255) COMMENT \'备注\',
        IPAddress VARCHAR(45) COMMENT \'IP地址\',
        OperatorID VARCHAR(255) COMMENT \'操作者ID\',
        ChangeTimestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT \'变更时间\',
        ChangeReason VARCHAR(255) COMMENT \'原因或备注\'
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = \'曾用手机号表\'',

    // 曾用密码表
    'CREATE TABLE IF NOT EXISTS PasswordHistory(
        PasswordHistoryID INT AUTO_INCREMENT PRIMARY KEY COMMENT \'ID\',
        user_id INT NOT NULL COMMENT \'关联用户ID\',
        FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
        HashedPassword VARCHAR(255) NOT NULL COMMENT \'新密码\',
        Remarks VARCHAR(255) COMMENT \'备注\',
        IPAddress VARCHAR(100) COMMENT \'IP地址\',
        PasswordChangeCycle INT COMMENT \'变更周期\',
        ChangeMethod VARCHAR(255) COMMENT \'变更方式\',
        ChangeReason VARCHAR(255) COMMENT \'变更原因\',
        ChangeTimestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT \'变更时间\'
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = \'曾用密码表\'',

    // 创建用户信息表
    'CREATE TABLE IF NOT EXISTS user_info (
        user_info_id INT PRIMARY KEY AUTO_INCREMENT COMMENT \'用户信息ID\',
        user_id INT UNIQUE COMMENT \'用户ID\',
        full_name VARCHAR(100) COMMENT \'用户昵称\',
        FOREIGN KEY (user_id) REFERENCES users(user_id)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = \'用户信息表\'',

    // 创建用户验证表
    'CREATE TABLE IF NOT EXISTS user_verification (
        verification_id INT PRIMARY KEY AUTO_INCREMENT COMMENT \'验证ID\',
        user_id INT UNIQUE COMMENT \'用户ID\',
        token VARCHAR(100) COMMENT \'验证令牌\',
        expiration DATETIME NOT NULL COMMENT \'过期时间\',
        FOREIGN KEY (user_id) REFERENCES users(user_id)
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = \'用户验证表\'',

    // 创建验证码对照表
    'CREATE TABLE IF NOT EXISTS VerificationCodes (
        id INT AUTO_INCREMENT PRIMARY KEY COMMENT \'验证码ID\',
        code VARCHAR(10) NOT NULL COMMENT \'验证码\',
        user_id TEXT COMMENT \'用户ID\',
        creation_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT \'验证码写入时间\'
    ) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = \'验证码对照表\'',

    'ALTER TABLE users ADD CONSTRAINT fk_users_roles FOREIGN KEY(user_role) REFERENCES Roles(RoleID);',
    'CREATE INDEX idx_user_role ON users(user_role);',
                // 添加更多的初始化操作...
    ];

    foreach ($sqlStatements as $sql) {
        if (mysqli_query($connection, $sql)) {
            $tableName = preg_match('/CREATE TABLE IF NOT EXISTS (\w+)/', $sql, $matches);
            if (isset($matches[1])) {
                echo "<p class='success'>执行SQL语句成功，{$matches[1]}表创建成功</p>";
            } else {
                echo "<p class='success'>执行SQL语句成功，数据关联成功！";
            }
        } else {
            $tableName = preg_match('/CREATE TABLE IF NOT EXISTS (\w+)/', $sql, $matches);
            if (isset($matches[1])) {
                echo "<p class='error'>执行SQL语句失败，{$matches[1]}表可能已存在或创建失败</p>";
            } else {
                echo "<p class='error'>执行SQL语句失败，操作未完成，请检查并重试！";
            }
        }
        usleep(500000); // 0.5秒延迟
        flush(); // 清空输出缓冲区
    }

    // 关闭数据库连接
    mysqli_close($connection);

    // 返回成功信息
    echo "<p class='success'>初始化完成！</p>";

} catch (Exception $e) {
    // 记录错误信息到文件
    file_put_contents('1.txt', date('[Y-m-d H:i:s]'). ' Error: ' . $e->getMessage() . "\n", FILE_APPEND);

    // 向用户展示友好的错误信息
    echo "<p class='unavailable'>数据库配置可能出现异常：<br>
    如果状态栏显示数据库已经全部创建，则该异常可忽略，这可能涉及到数据库关联错误，如果您是专业人士，请查看日志信息了解详情</p>";
}

echo '</ul></div>';
?>