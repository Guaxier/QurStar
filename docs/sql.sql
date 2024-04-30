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

-- 创建用户表
CREATE TABLE user (
    -- 用户唯一标识符
    user_id INT PRIMARY KEY AUTO_INCREMENT COMMENT '用户ID',
    -- 用户的账号，用于系统登录，不可更改
    username varchar(255) NOT NULL COMMENT '账号',
    -- 经过hash加密的密码，非明文存储
    password varchar(255) NOT NULL COMMENT '密码',
    -- hash密码的盐值，用于登录时验证密码
    salt varchar(255) NOT NULL COMMENT '盐',
    -- 用户的用户名，区别于账号，可更改，无法用于登录
    name varchar(255) COMMENT '用户名',
    -- 用户绑定的邮箱账号
    email varchar(255) COMMENT '邮箱',
    -- 用户绑定的手机号码
    phone_number varchar(15) COMMENT '手机号', -- 使用 char(10)/varchar(15)
    -- 用户账号状态:0-正常1-禁言2-封禁
    is_active tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户状态',
    -- 用户权限类型:0-普通用户1-会员用户2.高级会3.普通管理4.高级管理
    user_role tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '用户类型',
    -- 是否已经完成实名验证:0-未验证1-已验证
    real_name_verified tinyint(4) UNSIGNED NOT NULL DEFAULT 0 COMMENT '实名状态',
    -- 用户的真实姓名和实名认证保持一致，系统获取
    full_name varchar(255) COMMENT '真实姓名',
    -- 用户常用的登录地址，判断用户的IP归属
    location varchar(255) COMMENT '常用地址',
    -- 用户的登录标志，登录时系统自动写入，过期后删除
    token varchar(255) COMMENT '用户token',
    -- 鉴权token的过期时间，系统自动写入，出现请求后自动续期
    token_expires_at datetime COMMENT 'token过期时间',
    -- 用户进行密码重置时系统自动写入，过期和完成后删除
    reset_password_token varchar(255) COMMENT '密码重置token',
    -- 重置密码时有效，用户重置密码时系统写入
    reset_password_token_expires_at datetime COMMENT '重置token过期时间',
    -- 用户账号的注册时间，系统写入
    created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '注册时间',
    -- 最后一次修改账号信息的时间，系统自动写入
    updated_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '最后一次更新时间',
    -- 上一次登录的时间，系统自动写入
    last_login_at datetime COMMENT '最后一次登录时间'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='用户信息表';