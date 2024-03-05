<?php
// login.php
// 登录接口的实现
// 验证登录状态，返回登录结果和 token





//登录验证(账号+密码)
function login_verification($username, $password, $codeid, $code) {
    // 参数校验
    if (empty($username) || empty($password) || empty($code) || empty($codeid)) {
        return array("success" => false, "message" => "用户名、密码、验证码不能为空");
    }
    //验证码校验
    $result = verifyCode($codeid, $code);
    if ($result['success']) {
        // 引入数据库信息
        global $mysqli;
        // 准备 SQL 查询语句，检查用户名是否存在并获取盐值
        $query = "SELECT user_id, password, salt FROM user WHERE username = ?";
        $stmt = $mysqli->prepare($query);

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            // 如果用户存在，绑定结果
            $stmt->bind_result($user_id, $hashedPassword, $salt);
            $stmt->fetch();

            // 对提供的密码和盐值生成哈希密码
            $inputHashedPassword = generateHashedPassword($password, $salt);

            // 检查哈希密码是否与数据库中的哈希密码匹配
            if ($inputHashedPassword = $hashedPassword) {
                
                // 查询token是否存在
                $query = "SELECT token, expiration FROM user_verification WHERE user_id = ?";
                $stmt = $mysqli->prepare($query);

                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $stmt->store_result();
                
                // 如果记录已经存在
                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($token, $expiration);
                    $stmt->fetch(); // 获取结果

                    // 更新token的过期时间为当前时间加七天
                    $expiration = date('Y-m-d H:i:s', strtotime('+7 days'));
                    $update_query = "UPDATE user_verification SET expiration = ? WHERE user_id = ?";
                    $update_stmt = $mysqli->prepare($update_query);

                    $update_stmt->bind_param("si", $expiration, $user_id);
                    $update_stmt->execute();
                    $update_stmt->close(); // 关闭预处理语句
                } else {
                    // 生成一个新的token
                    $token = generate_token();

                    // 将token写入用户验证表，并设置过期时间为当前时间加七天
                    $expiration = date('Y-m-d H:i:s', strtotime('+7 days'));
                    $insert_query = "INSERT INTO user_verification (user_id, token, expiration) VALUES (?, ?, ?)";
                    $insert_stmt = $mysqli->prepare($insert_query);

                    $insert_stmt->bind_param("iss", $user_id, $token, $expiration);
                    $insert_stmt->execute();
                    $insert_stmt->close(); // 关闭预处理语句
                }
                // 返回token以及登录成功消息
                return array("success" => true, "message" => "登录成功！", "token" => $token);
            } else {
                return array("success" => false, "message" => "用户名或密码不正确！");
            }
        } else {
            return array("success" => false, "message" => "用户名或密码不正确！");
        }
    }
    return array("success" => false, "message" => "验证码错误！");
}

//登录验证(邮箱+密码)
function login_verification_email($username, $password, $codeid, $code) {
    // 参数校验
    if (empty($username) || empty($password) || empty($code) || empty($codeid)) {
        return array("success" => false, "message" => "用户名、密码、验证码不能为空");
    }
    //验证码校验
    $result = verifyCode($codeid, $code);
    if ($result['success']) {
        // 引入数据库信息
        global $mysqli;
        // 准备 SQL 查询语句，检查用户名是否存在并获取盐值
        $query = "SELECT user_id, password, salt FROM user WHERE email = ?";
        $stmt = $mysqli->prepare($query);

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            // 如果用户存在，绑定结果
            $stmt->bind_result($user_id, $hashedPassword, $salt);
            $stmt->fetch();

            // 对提供的密码和盐值生成哈希密码
            $inputHashedPassword = generateHashedPassword($password, $salt);

            // 检查哈希密码是否与数据库中的哈希密码匹配
            if ($inputHashedPassword = $hashedPassword) {
                
                // 查询token是否存在
                $query = "SELECT token, expiration FROM user_verification WHERE user_id = ?";
                $stmt = $mysqli->prepare($query);

                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $stmt->store_result();
                
                // 如果记录已经存在
                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($token, $expiration);
                    $stmt->fetch(); // 获取结果

                    // 更新token的过期时间为当前时间加七天
                    $expiration = date('Y-m-d H:i:s', strtotime('+7 days'));
                    $update_query = "UPDATE user_verification SET expiration = ? WHERE user_id = ?";
                    $update_stmt = $mysqli->prepare($update_query);

                    $update_stmt->bind_param("si", $expiration, $user_id);
                    $update_stmt->execute();
                    $update_stmt->close(); // 关闭预处理语句
                } else {
                    // 生成一个新的token
                    $token = generate_token();

                    // 将token写入用户验证表，并设置过期时间为当前时间加七天
                    $expiration = date('Y-m-d H:i:s', strtotime('+7 days'));
                    $insert_query = "INSERT INTO user_verification (user_id, token, expiration) VALUES (?, ?, ?)";
                    $insert_stmt = $mysqli->prepare($insert_query);

                    $insert_stmt->bind_param("iss", $user_id, $token, $expiration);
                    $insert_stmt->execute();
                    $insert_stmt->close(); // 关闭预处理语句
                }
                // 返回token以及登录成功消息
                return array("success" => true, "message" => "登录成功！", "token" => $token);
            } else {
                return array("success" => false, "message" => "邮箱或密码不正确！");
            }
        } else {
            return array("success" => false, "message" => "邮箱或密码不正确！");
        }
    }
    return array("success" => false, "message" => "验证码错误！");
}

//登陆验证(手机+密码)
function login_verification_phone($username, $password, $codeid, $code) {
    // 参数校验
    if (empty($username) || empty($password) || empty($code) || empty($codeid)) {
        return array("success" => false, "message" => "用户名、密码、验证码不能为空");
    }
    //验证码校验
    $result = verifyCode($codeid, $code);
    if ($result['success']) {
        // 引入数据库信息
        global $mysqli;
        // 准备 SQL 查询语句，检查用户名是否存在并获取盐值
        $query = "SELECT user_id, password, salt FROM user WHERE phone_number = ?";
        $stmt = $mysqli->prepare($query);

        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            // 如果用户存在，绑定结果
            $stmt->bind_result($user_id, $hashedPassword, $salt);
            $stmt->fetch();

            // 对提供的密码和盐值生成哈希密码
            $inputHashedPassword = generateHashedPassword($password, $salt);

            // 检查哈希密码是否与数据库中的哈希密码匹配
            if ($inputHashedPassword = $hashedPassword) {
                
                // 查询token是否存在
                $query = "SELECT token, expiration FROM user_verification WHERE user_id = ?";
                $stmt = $mysqli->prepare($query);

                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $stmt->store_result();
                
                // 如果记录已经存在
                if ($stmt->num_rows > 0) {
                    $stmt->bind_result($token, $expiration);
                    $stmt->fetch(); // 获取结果

                    // 更新token的过期时间为当前时间加七天
                    $expiration = date('Y-m-d H:i:s', strtotime('+7 days'));
                    $update_query = "UPDATE user_verification SET expiration = ? WHERE user_id = ?";
                    $update_stmt = $mysqli->prepare($update_query);

                    $update_stmt->bind_param("si", $expiration, $user_id);
                    $update_stmt->execute();
                    $update_stmt->close(); // 关闭预处理语句
                } else {
                    // 生成一个新的token
                    $token = generate_token();

                    // 将token写入用户验证表，并设置过期时间为当前时间加七天
                    $expiration = date('Y-m-d H:i:s', strtotime('+7 days'));
                    $insert_query = "INSERT INTO user_verification (user_id, token, expiration) VALUES (?, ?, ?)";
                    $insert_stmt = $mysqli->prepare($insert_query);

                    $insert_stmt->bind_param("iss", $user_id, $token, $expiration);
                    $insert_stmt->execute();
                    $insert_stmt->close(); // 关闭预处理语句
                }
                // 返回token以及登录成功消息
                return array("success" => true, "message" => "登录成功！", "token" => $token);
            } else {
                return array("success" => false, "message" => "手机号或密码不正确！");
            }
        } else {
            return array("success" => false, "message" => "手机号或密码不正确！");
        }
    }
    return array("success" => false, "message" => "验证码错误！");
}
















// 验证 token
function verify_token($username, $token) {
    global $mysqli;
    
    // 使用预处理语句防止SQL注入攻击
    $stmt = $mysqli->prepare("SELECT * FROM user_verification WHERE user_id = (SELECT user_id FROM user WHERE username = ?) AND token = ?");
    $stmt->bind_param("ss", $username, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    // 关闭预处理语句
    $stmt->close();
    if ($result->num_rows > 0) {
        // 如果 token 存在，检查是否过期
        $row = $result->fetch_assoc();
        $expiration = new DateTime($row['expiration']);
        $now = new DateTime();
        if ($expiration > $now) {
            //重置过期时间为当前时间加7天
            $stmt_update = $mysqli->prepare("UPDATE user_verification SET expiration = DATE_ADD(NOW(), INTERVAL 7 DAY) WHERE user_id = (SELECT user_id FROM user WHERE username = ?) AND token = ?");
            $stmt_update->bind_param("ss", $username, $token);
            $stmt_update->execute();
            // token 未过期，返回 true
            return true;
        } else {
            // token 已过期,返回 false
            return false;
        }
    } else {
        // token 不存在,返回 false
        return false;
        
    }
}





