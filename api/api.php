<?php

// api.php
// 本api是主页面的项目接口，用于返回项目列表数据，返回数据格式为JSON，具体返回内容请查看数据库结构和页面文档 


//统一引入处理函数文件
require_once 'includes/functions.php'; // 导入共享函数文件
require_once 'includes/CaptchaManager.php'; // 导入验证码管理类
require_once 'includes/emailspend.php';//邮件设置

//统一引入处理api文件
require_once 'auth/login.php';//登录及其token验证api
//require_once 'auth/logout.php';//登出api
require_once 'auth/register.php';//注册api
//require_once 'auth/forget.php';//找回密码api

// 错误处理和日志记录
error_reporting(E_ERROR | E_WARNING);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'log/php_errors.log');

// 开启session
session_start(); 
// 数据库信息引入
include('includes/config.php');





// 判断请求类型
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取原始POST数据
    $json_data = file_get_contents("php://input");
    // 解码JSON数据
    $post_data = json_decode($json_data, true);
    // 获取请求类型
    $way = isset($post_data['way']) ? $post_data['way'] : '';
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // 将请求中way的值赋值给way变量
    $way = isset($_GET['way']) ? $_GET['way'] : '';
    // 保存请求参数
    $getdate = $_GET;
} else {
    echo "请求类型错误！";
    // 待扩展
    // 这是其他类型的请求，如PUT、DELETE等
}

// 请求验证
// 如果way为空，则输出提示信息并退出
if (empty($way)) {
    echo json_encode(["message" => "非法请求"]);
    exit;
}






// 根据传入的way参数，调用不同的api函数
if ($way) {
    switch ($way) {
        // 请求验证码(getcode)
        case 'getcode':
            getcode();//获取验证码
            break;




        //请求邮箱验证码
        case 'getemailcode':
            $toEmail = $post_data["email"];//邮箱
            getemailcode($toEmail);//发送邮箱验证码
            break;




        //请求登录
        case 'login':
            $username = $post_data["username"];//用户名
            $password = $post_data["password"];//密码
            $code = $post_data["code"];//验证码
            $codeid = $post_data["codeid"];//验证码id
            login($username,$password,$codeid,$code);//登录验证函数
            break;





        //请求注册
        case 'register':
            $username = $post_data["username"];//用户名
            $password = $post_data["password"];//密码
            $email = $post_data["email"] ?? null; // 邮箱
            $phone = $post_data["phone"] ?? null; // 手机号
            $code = $post_data["code"];//验证码
            $codeid = $post_data["codeid"];//验证码id
            register($username,$password,$email,$phone,$code,$codeid);//注册验证函数
            break;



        //状态检查
        case 'state':
            state();//状态检查
            break;





        case 'ts':
            //前端请求的类型，调用不同api
            break;





        default:
            echo "页面请求错误！";
    }
} else {
    echo "页面参数错误！";
}


//请求验证码函数
function getcode(){
    // 初始化结果数组
    $result = array();

    // 创建 CaptchaManager 验证码 实例
    $captcha_manager = new CaptchaManager();
    // 处理验证码请求
    $result = $captcha_manager->handle_captcha_request();
    // 返回 JSON 响应
    echo json_encode($result);
    // 关闭数据库连接
    global $mysqli;
    mysqli_close($mysqli);
}

//请求邮箱验证码函数
function getemailcode($toEmail) {
    // 初始化结果数组
    $result = array();

    // 验证邮箱是否合法
    if (validateInput($toEmail, 'email')) {
        // 判断邮箱是否存在
        if (!emailExists($toEmail)) {
            // 创建 CaptchaManager 验证码 实例
            $captcha_manager = new CaptchaManager();
            // 处理验证码请求
            $result = $captcha_manager->handle_captcha_request(2, $toEmail);
        }else{
            $result = array('message' => '邮箱已存在!', 'success' => false);
        }
    } else {
        $result = array('message' => '邮箱格式错误!', 'success' => false);
    }

    // 返回 JSON 响应
    echo json_encode($result);

    // 关闭数据库连接
    global $mysqli;
    mysqli_close($mysqli);
}


//登录函数
function login($username,$password,$codeid,$code){
    // 初始化结果数组
    $result = array();

    // 调用登录获取token
    $result = login_verification($username, $password,$codeid,$code);
    //返回token
    echo json_encode($result);
    // 关闭数据库连接
    global $mysqli;
    mysqli_close($mysqli);
}

//注册函数
function register($username,$password,$email,$phone,$code,$codeid){
    // 初始化结果数组
    $result = array();

    if (!empty($email)) {
        // 使用邮箱注册逻辑
        // 调用使用邮箱注册的函数
        $result = register_email($username,$password,$email,$code,$codeid);
    } elseif (!empty($phone)) {
        // 使用手机号注册逻辑
        // 调用使用手机号注册的函数
        $result = register_phone($username,$password,$phone,$code,$codeid);
    } else {
        // 处理错误，没有提供有效的注册方式
        $result = array(
            'message' => '没有提供有效的注册方式!',
            'success' => false,
        );
    }
    //返回 JSON 响应
    echo json_encode($result);
    // 关闭数据库连接
    global $mysqli;
    mysqli_close($mysqli);
}

    


//状态检查函数
function state(){
    //状态检查
    $username = "666666";// 用户名
    $token = "666666";// token
    $result = verify_token($username, $token);

    if ($result) {
        echo "登录成功";
    } else {
        echo "登录失败";
    }
    // 关闭数据库连接
    global $mysqli;
    mysqli_close($mysqli);
}















