<?php
// login.php
// 登录接口的实现
// 验证登录状态，返回登录结果和 token





//登录验证(账号+密码)
function login_verification($username, $password, $codeid, $code)
{
    // 参数校验
    if (empty($username) || empty($password)) {
        return array("success" => false, "message" => "用户名、密码不能为空");
    }
    //验证码校验
    //$result = verifyCode($codeid, $code);
    $result = array('message' => '登录测试代码，生产环境请注释', 'success' => true, );
    if ($result['success']) {
        //引入pdo
        global $pdo;
        //调用登录验证获取用户ID
        $userId = getUserIdByUsernameAndPassword($pdo,$username,$password);
        if ($userId !== false) {
            //获取新的token
            $token = generateLoginToken($userId,DB_secretKey,7200);
            // 返回token以及登录成功消息
            return array("success" => true, "message" => "登录成功！", "token" => $token);
        } else {
            return array("success" => false, "message" => "账号或密码错误！");
        }
    }
    return array("success" => false, "message" => "验证码错误！");
}

//登录验证(邮箱+密码)
function login_verification_email($email, $password, $codeid, $code)
{
    // 参数校验
    if (empty($email) || empty($password)) {
        return array("success" => false, "message" => "邮箱、密码不能为空");
    }
    //验证码校验
    //$result = verifyCode($codeid, $code);
    $result = array('message' => '登录测试代码，生产环境请注释', 'success' => true, );
    if ($result['success']) {
        //引入pdo
        global $pdo;
        //调用登录验证获取用户ID
        $userId = getUserIdByEmailAndPassword($pdo,$email,$password);
        if ($userId !== false) {
            //获取新的token
            $token = generateLoginToken($userId,DB_secretKey,7200);
            // 返回token以及登录成功消息
            return array("success" => true, "message" => "登录成功！", "token" => $token);
        } else {
            return array("success" => false, "message" => "账号或密码错误！");
        }
    }
    return array("success" => false, "message" => "验证码错误！");
}

//登陆验证(手机+密码)
function login_verification_phone($phone, $password, $codeid, $code)
{
    // 参数校验
    if (empty($phone) || empty($password)) {
        return array("success" => false, "message" => "手机号、密码不能为空");
    }
    //验证码校验
    //$result = verifyCode($codeid, $code);
    $result = array('message' => '登录测试代码，生产环境请注释', 'success' => true, );
    if ($result['success']) {
        //引入pdo
        global $pdo;
        //调用登录验证获取用户ID
        $userId = getUserIdByPhoneAndPassword($pdo,$phone,$password);
        if ($userId !== false) {
            //获取新的token
            $token = generateLoginToken($userId,DB_secretKey,7200);
            // 返回token以及登录成功消息
            return array("success" => true, "message" => "登录成功！", "token" => $token);
        } else {
            return array("success" => false, "message" => "账号或密码错误！");
        }
    }
    return array("success" => false, "message" => "验证码错误！");
}
















// 验证 token
function verify_token($username, $token)
{
    global $mysqli;

    // 使用预处理语句防止SQL注入攻击
    $stmt = $mysqli->prepare("SELECT * FROM user WHERE user_id = (SELECT user_id FROM user WHERE username = ?) AND token = ?");
    $stmt->bind_param("ss", $username, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    // 关闭预处理语句
    $stmt->close();
    if ($result->num_rows > 0) {
        // 如果 token 存在，检查是否过期
        $row = $result->fetch_assoc();
        $token_expires_at = new DateTime($row['token_expires_at']);
        $now = new DateTime();
        if ($token_expires_at > $now) {
            //重置过期时间为当前时间加7天
            $stmt_update = $mysqli->prepare("UPDATE user SET token_expires_at = DATE_ADD(NOW(), INTERVAL 7 DAY) WHERE user_id = (SELECT user_id FROM user WHERE username = ?) AND token = ?");
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





