-- 创建用户表
CREATE TABLE user (
    user_id INT PRIMARY KEY AUTO_INCREMENT,-- 用户ID
    username VARCHAR(50) UNIQUE NOT NULL COMMENT '用户名', -- 用户名，唯一且不能为空
    email VARCHAR(100) COMMENT '电子邮件', -- 电子邮件
    phone_number VARCHAR(20) COMMENT '电话号码', -- 电话号码
    password VARCHAR(100) NOT NULL COMMENT '密码', -- 密码，不能为空
    salt VARCHAR(256) COMMENT '盐'
);

-- 创建用户信息表
CREATE TABLE user_info (
    user_info_id INT PRIMARY KEY AUTO_INCREMENT, -- 用户信息ID
    user_id INT UNIQUE COMMENT '用户ID', -- 关联的用户ID，唯一
    full_name VARCHAR(100) COMMENT '用户昵称', -- 昵称
    FOREIGN KEY (user_id) REFERENCES user(user_id) -- 外键，关联用户表中的用户ID
);

-- 创建用户验证表
CREATE TABLE user_verification (
    verification_id INT PRIMARY KEY AUTO_INCREMENT, -- 验证ID
    user_id INT UNIQUE COMMENT '用户ID', -- 关联的用户ID，唯一
    token VARCHAR(100) COMMENT '验证令牌', -- 验证令牌
    expiration DATETIME NOT NULL COMMENT '过期时间', -- 过期时间
    FOREIGN KEY (user_id) REFERENCES user(user_id) -- 外键，关联用户表中的用户ID
);

-- 创建验证码对照表
CREATE TABLE VerificationCodes (
    id INT AUTO_INCREMENT PRIMARY KEY, -- 验证码ID
    code VARCHAR(10) NOT NULL COMMENT '验证码', -- 验证码
    user_id TEXT COMMENT '用户ID',  -- 用户ID
    creation_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '验证码写入时间' -- 验证码写入时间
);



/*
用户表包含用户的登录信息，包括用户ID（user_id）、用户名（username）和密码（password）。
用户信息表包含用户的个人信息，包括用户信息ID（user_info_id）、关联的用户ID（user_id）、全名（full_name）、电子邮件（email）和电话号码（phone_number）。
用户验证表包含用户的验证信息，包括验证ID（verification_id）、关联的用户ID（user_id）、验证令牌（token）和过期时间（expiration）。
user_id列与用户表中的用户ID列关联，确保每个验证信息与一个用户关联。
expiration列存储验证令牌的过期时间，以便在一定时间后使验证失效。
*/




测试:
-- 数据库系统 格式化版
-- 角色表
CREATE TABLE Roles(
    RoleID INT AUTO_INCREMENT PRIMARY KEY COMMENT '角色ID',
    RoleName VARCHAR(255) NOT NULL UNIQUE COMMENT '角色名称',
    Description VARCHAR(255) COMMENT '角色的基本描述'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = '角色表'; 

-- 权限表
CREATE TABLE Permissions(
    PermissionID INT AUTO_INCREMENT PRIMARY KEY COMMENT '权限ID',
    PermissionName VARCHAR(255) NOT NULL UNIQUE COMMENT '权限名称',
    Description VARCHAR(255) COMMENT '权限的基本描述'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = '权限表'; 

-- 权限关联表
CREATE TABLE Role_Permissions(
    RoleID INT NOT NULL,
    PermissionID INT NOT NULL,
    PRIMARY KEY(RoleID, PermissionID),
    FOREIGN KEY(RoleID) REFERENCES Roles(RoleID) ON DELETE CASCADE,
    FOREIGN KEY(PermissionID) REFERENCES Permissions(PermissionID) ON DELETE CASCADE
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = '角色与权限关联表'; 

-- 用户表
CREATE TABLE users(
    -- 用户唯一标识符
    user_id INT PRIMARY KEY AUTO_INCREMENT COMMENT '用户ID',
    -- 用户的账号，用于系统登录，不可更改
    username VARCHAR(255) NOT NULL COMMENT '账号',
    -- 经过hash加密的密码，非明文存储
    password VARCHAR(255) NOT NULL COMMENT '密码',
    -- hash密码的盐值，用于登录时验证密码（理论不能为空，弃用的逻辑）
    salt VARCHAR(255) COMMENT '盐',
    -- 用户的用户名，区别于账号，可更改，无法用于登录
    name VARCHAR(255) COMMENT '用户名',
    -- 用户绑定的邮箱账号
    email VARCHAR(255) COMMENT '邮箱',
    -- 用户绑定的手机号码
    phone_number VARCHAR(15) COMMENT '手机号',
    -- 使用 char(10)/varchar(15)
    -- 用户账号状态:0-正常1-禁言2-封禁
    is_active TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户状态',
    -- 用户权限类型,与角色表进行绑定
    user_role INT COMMENT '权限',
    -- 是否已经完成实名验证:0-未验证1-已验证
    real_name_verified TINYINT(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '实名状态',
    -- 用户的真实姓名和实名认证保持一致，系统获取
    full_name VARCHAR(255) COMMENT '真实姓名',
    -- 用户常用的登录地址，判断用户的IP归属
    location VARCHAR(255) COMMENT '常用地址',
    -- 用户的登录标志，登录时系统自动写入，过期后删除
    token VARCHAR(255) COMMENT '用户token',
    -- 鉴权token的过期时间，系统自动写入，出现请求后自动续期
    token_expires_at DATETIME COMMENT 'token过期时间',
    -- 用户进行密码重置时系统自动写入，过期和完成后删除
    reset_password_token VARCHAR(255) COMMENT '密码重置token',
    -- 重置密码时有效，用户重置密码时系统写入
    reset_password_token_expires_at DATETIME COMMENT '重置token过期时间',
    -- 用户账号的注册时间，系统写入
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '注册时间',
    -- 最后一次修改账号信息的时间，系统自动写入
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后一次更新时间',
    -- 上一次登录的时间，系统自动写入
    last_login_at DATETIME COMMENT '最后一次登录时间'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = '用户信息表';
-- 外键约束单独添加，确保表创建成功后再添加约束
ALTER TABLE
    `users` ADD CONSTRAINT `fk_users_roles` FOREIGN KEY(`user_role`) REFERENCES `Roles`(`RoleID`);
    -- 为user_role创建索引以优化查询
CREATE INDEX `idx_user_role` ON
    `users`(`user_role`);

-- 登录表
CREATE TABLE UserLoginRecords(
    -- 主键，自增
    LoginRecordID INT AUTO_INCREMENT PRIMARY KEY COMMENT '登录记录ID',
    -- 用户关联，与user表中的user_id关联
    user_id INT NOT NULL COMMENT '关联用户ID',
    FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    -- 记录用户登录方式
    Login_Method VARCHAR(255) COMMENT '登录方式',
    -- 登录设备类型和操作系统信息
    Device VARCHAR(255) COMMENT '登录设备',
    -- 用户登录的具体时间
    Login_Time TIMESTAMP COMMENT '登录时间',
    -- 记录用户的登出时间，用于计算会话时长，允许为空表示会话未结束
    Logout_Time TIMESTAMP COMMENT '登出时间',
    -- 通过IP地址解析的登录地点
    Location VARCHAR(255) COMMENT '登录地点',
    -- 用户登录时的IP地址
    IP_Address VARCHAR(255) COMMENT 'IP地址',
    -- 登录时的浏览器类型和版本
    Browser_Info VARCHAR(255) COMMENT '浏览器信息',
    -- 登录尝试是否成功，使用TINYINT(4)或ENUM类型
    -- 可替换TINYINT(4)为 ENUM('成功', '失败')，
    -- Login_Status ENUM('成功', '失败') NOT NULL COMMENT '登录状态',
    -- TINYINT(4) '0-成功, 1-失败'
    Login_Status TINYINT(4) UNSIGNED NOT NULL COMMENT '登录状态',
    -- 登录验证的附加信息
    Authentication_Details VARCHAR(255) COMMENT '验证详情',
    -- 记录登录过程中的异常或信息
    Remarks VARCHAR(255) COMMENT '备注'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = '登录表'; 

-- 状态表
CREATE TABLE AccountStatus(
    -- 主键，自增
    AccountStatusID INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID',
    -- 用户关联，与user表中的user_id关联
    user_id INT NOT NULL COMMENT '关联用户',
    FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    -- 记录账号当前的状态，枚举或tinyint(4)类型，tinyint(4)类型归类如下:0-正常1-禁言2-封禁3-注销
    STATUS TINYINT
        (4) UNSIGNED NOT NULL COMMENT '状态',
        -- 被设置当前状态的原因
        reason VARCHAR(255) COMMENT '状态原因',
        -- 对于账号特殊情况的处理，方便扩展
        notes TEXT COMMENT '备注',
        -- 操作账号状态的执行人，可能是管理员或系统
        operator_id VARCHAR(255) COMMENT '操作者ID',
        -- 记录账号特殊状态的开始时间，如禁言或封号的开始时间
        start_time TIMESTAMP COMMENT '开始时间',
        -- 记录账号特殊状态的结束时间，如禁言或封号的结束时间
        end_time TIMESTAMP COMMENT '结束时间',
        -- 记录账号特殊状态的生效时间记录更新的时间
        update_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = '账号状态表';

-- 曾用邮箱表
CREATE TABLE UserEmailHistory(
    -- 主键，自增
    UserEmailHistoryID INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID',
    -- 用户关联，与user表中的user_id关联，级联删除
    user_id INT NOT NULL COMMENT '关联用户ID',
    FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    -- 用户更改后的邮箱
    NewEmail VARCHAR(255) NOT NULL COMMENT '新邮箱',
    -- 邮箱更改的其他备注信息
    Remarks VARCHAR(255) COMMENT '备注',
    -- 记录用户修改邮箱时的IP地址
    IPAddress VARCHAR(45) COMMENT 'IP地址',
    -- 追踪是谁执行了邮箱的修改操作
    OperatorID VARCHAR(255) COMMENT '操作者ID',
    -- 记录邮箱变更的具体时间，日期时间类型
    ChangeTimestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '变更时间',
    -- 变更原因，记录邮箱变更的原因或备注
    ChangeReason VARCHAR(255) COMMENT '原因或备注'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = '曾用邮箱表'; 

-- 曾用户名表
CREATE TABLE UsernameHistory(
    -- 主键，自增
    UsernameHistoryID INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID',
    -- 用户关联，与user表中的user_id关联，级联删除
    user_id INT NOT NULL COMMENT '关联用户ID',
    FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    -- 用户更改后的用户名
    NewUsername VARCHAR(255) NOT NULL COMMENT '新用户名',
    -- 用户名更改的其他备注信息
    Remarks VARCHAR(255) COMMENT '备注',
    -- 记录用户修改用户名时的IP地址
    IPAddress VARCHAR(45) COMMENT 'IP地址',
    -- 追踪是谁执行了用户名的修改操作
    OperatorID VARCHAR(255) COMMENT '操作者ID',
    -- 记录用户名变更的具体时间，日期时间类型
    ChangeTimestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '变更时间',
    -- 变更原因，记录用户名变更的原因或备注
    ChangeReason VARCHAR(255) COMMENT '原因或备注'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = '曾用户名表'; 

-- 曾用手机号表
CREATE TABLE UserPhoneHistory(
    -- 主键，自增
    UserPhoneHistoryID INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID',
    -- 用户关联，与user表中的user_id关联，级联删除
    user_id INT NOT NULL COMMENT '关联用户ID',
    FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    -- 用户更改后的手机号
    NewPhone VARCHAR(255) NOT NULL COMMENT '新手机',
    -- 手机号更改的其他备注信息
    Remarks VARCHAR(255) COMMENT '备注',
    -- 记录用户修改手机号时的IP地址
    IPAddress VARCHAR(45) COMMENT 'IP地址',
    -- 追踪是谁执行了手机号的修改操作
    OperatorID VARCHAR(255) COMMENT '操作者ID',
    -- 记录手机变更的具体时间，日期时间类型
    ChangeTimestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '变更时间',
    -- 变更原因，记录手机号变更的原因或备注
    ChangeReason VARCHAR(255) COMMENT '原因或备注'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = '曾用手机号表'; 

-- 曾用密码表
CREATE TABLE PasswordHistory(
    -- 主键，自增
    PasswordHistoryID INT AUTO_INCREMENT PRIMARY KEY COMMENT 'ID',
    -- 用户关联，与user表中的user_id关联
    user_id INT NOT NULL COMMENT '关联用户ID',
    FOREIGN KEY(user_id) REFERENCES users(user_id) ON DELETE CASCADE,
    -- 新密码的哈希值
    HashedPassword VARCHAR(255) NOT NULL COMMENT '新密码',
    -- 密码更改的其他备注信息
    Remarks VARCHAR(255) COMMENT '备注',
    -- 修改密码时的IP地址
    IPAddress VARCHAR(100) COMMENT 'IP地址',
    -- 密码变更周期，例如多少天变更一次
    PasswordChangeCycle INT COMMENT '变更周期',
    -- 记录密码变更的方式
    ChangeMethod VARCHAR(255) COMMENT '变更方式',
    -- 密码变更的原因
    ChangeReason VARCHAR(255) COMMENT '变更原因',
    -- 密码的更新时间，每次记录时自动刷新
    ChangeTimestamp TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '变更时间'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COMMENT = '曾用密码表';


