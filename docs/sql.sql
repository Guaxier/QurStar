-- 创建用户表
CREATE TABLE user (
    user_id INT PRIMARY KEY AUTO_INCREMENT, -- 用户ID
    username VARCHAR(50) UNIQUE NOT NULL, -- 用户名，唯一且不能为空
    email VARCHAR(100) COMMENT '电子邮件', -- 电子邮件
    phone VARCHAR(20) COMMENT '电话号码', -- 电话号码
    salt VARCHAR(32), -- 盐值字段
    password VARCHAR(100) NOT NULL -- 密码，不能为空
);


-- 创建用户信息表
CREATE TABLE user_info (
    user_info_id INT PRIMARY KEY AUTO_INCREMENT, -- 用户信息ID
    user_id INT UNIQUE COMMENT '用户ID', -- 关联的用户ID，唯一
    full_name VARCHAR(100) COMMENT '全名', -- 全名
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
    user_id INT NOT NULL COMMENT '用户ID',  -- 用户ID
    creation_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '验证码写入时间' -- 验证码写入时间
);



/*
用户表包含用户的登录信息，包括用户ID（user_id）、用户名（username）和密码（password）。
用户信息表包含用户的个人信息，包括用户信息ID（user_info_id）、关联的用户ID（user_id）、全名（full_name）、电子邮件（email）和电话号码（phone_number）。
用户验证表包含用户的验证信息，包括验证ID（verification_id）、关联的用户ID（user_id）、验证令牌（token）和过期时间（expiration）。
user_id列与用户表中的用户ID列关联，确保每个验证信息与一个用户关联。
expiration列存储验证令牌的过期时间，以便在一定时间后使验证失效。
*/