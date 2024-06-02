<?php

// api.php
// 本api是主页面的项目接口，用于返回项目列表数据，返回数据格式为JSON，具体返回内容请查看数据库结构和页面文档 


//统一引入处理函数文件
require_once 'includes/functions.php'; // 导入共享函数文件
require_once 'includes/CaptchaManager.php'; // 导入验证码管理类
require_once 'includes/emailspend.php';//邮件发送
require_once 'includes/smsspend.php';//短信发送

require_once 'includes/depend/SMSALI/vendor/autoload.php'; // 确保引入了自动加载文件

use AlibabaCloud\SDK\Sample\Sample;


//统一引入处理api文件
require_once 'auth/login.php';//登录及其token验证api
//require_once 'auth/logout.php';//登出api
require_once 'auth/register.php';//注册api
//require_once 'auth/forget.php';//找回密码api
require_once 'systemgift/systemgift.php';//奇幻星礼物API

//统一引入数据库处理函数（调试）
//require_once 'auth/sqlapi/users.php';

// 错误处理和日志记录
error_reporting(E_ERROR | E_WARNING);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'log/php_errors.log');

// 开启session
session_start();
// 数据库和配置信息引入
include ('includes/config.php');
include ('includes/PDO.php');
include ('includes/MYSQLI.php');





// 判断请求类型
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 获取原始POST数据
    $json_data = file_get_contents("php://input");

    //请求日志记录
    $log_file = 'log.txt';
    file_put_contents($log_file, $json_data, FILE_APPEND);

    // 解码JSON数据
    $post_data = json_decode($json_data, true);

    // 解码后的请求写入日志文件
    file_put_contents($log_file, $post_data, FILE_APPEND);

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
            $toEmail = $post_data["email"] ?? '';//邮箱
            $toPhone = $post_data["phone"] ?? '';//手机号
            $codetype = $post_data["codetype"];//验证码
            getcode($codetype, $toEmail, $toPhone);//获取验证码
            break;




        //请求邮箱验证码
        case 'getemailcode':


            break;



        //请求手机验证码
        case 'getphonecode':


            break;




        //请求登录
        case 'login':
            $username = $post_data["username"] ?? '';//用户名
            $password = $post_data["password"];//密码
            $email = $post_data["email"] ?? ''; // 邮箱
            $phone = $post_data["phone"] ?? ''; // 手机号
            $code = $post_data["code"] ?? '';//验证码
            $codeid = $post_data["codeid"] ?? '';//验证码id
            login($username, $password, $email, $phone, $code, $codeid);//登录验证函数
            break;





        //请求注册
        case 'register':
            $username = $post_data["username"];//用户名
            $password = $post_data["password"];//密码
            $email = $post_data["email"] ?? ''; // 邮箱
            $phone = $post_data["phone"] ?? ''; // 手机号
            $code = $post_data["code"];//验证码
            $codeid = $post_data["codeid"];//验证码id
            register($username, $password, $email, $phone, $code, $codeid);//注册验证函数
            break;



        //找回密码
/*        case 'forgetpassword':
            $toEmail = $post_data["email"];//邮箱
            $toPhone = $post_data["phone"];//手机号
            $code = $post_data["code"];//验证码
            $codeid = $post_data["codeid"];//验证码id
            forgetpassword($toEmail,$toPhone,$code,$codeid);//找回密码验证函数
            break;
*/
        //状态检查
        case 'state':
            break;



        //用户查重处理/token状态检查
        case 'selectname':
            // 从POST数据获取参数
            $name = isset($post_data["name"]) ? $post_data["name"] : null;
            $email = isset($post_data["email"]) ? $post_data["email"] : null;
            $phone = isset($post_data["phone"]) ? $post_data["phone"] : null;
            $token = isset($post_data["token"]) ? $post_data["token"] : null;
            //调用方法
            selectmessage($name, $email, $phone, $token);
            break;
        

        //查询表
        case 'selecttable' :
            //从POST获取参数
            $table = isset($post_data["table"]) ? $post_data["table"] : null;
            //引入数据库信息
            global $pdo;
            //调用方法
            isTableExists($pdo,$table);
            break;


        //礼物查询
        case 'selectgifttype':
            //引入数据库信息
            global $pdo;
            //获取主题帖子
            $topic = selectGiftTopic();
            //获取礼物类型
            $gifttype = selectGiftType();
            //获取推荐ico
            $banner = selectGiftBanner();
            //获取轮播图
            $header = selectGiftHeader();
            //获取商品推荐
            $shop = selectGiftShop();
            //返回结果
            $result = array("success" => true, "message" => "获取成功", "topic" => $topic, "gifttype" => $gifttype, "banner" => $banner, "header" => $header, "shop" => $shop);
            echo json_encode($result);
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


//请求图形验证码函数
function getcode($codetype, $toEmail, $toPhone)
{
    // 初始化结果数组
    $result = array();

    if ($codetype === 'email') {// 验证码类型：邮箱注册
        $result = getemailcode($toEmail);//发送邮箱验证码

    } elseif ($codetype === 'phone') {// 验证码类型:手机注册
        $result = getphonecode($toPhone);//发送手机验证码

    } elseif ($codetype === "image") {// 验证码类型:普通图片验证码
        $captcha_manager = new CaptchaManager();// 创建 CaptchaManager 验证码 实例
        $result = $captcha_manager->handle_captcha_request();//发送验证码图片

    } else {
        $result = array('success' => false, 'message' => '非法请求!');
    }

    // 返回 JSON 响应
    echo json_encode($result);
    // 关闭数据库连接
    global $mysqli;
    mysqli_close($mysqli);
}

//请求邮箱验证码函数
function getemailcode($toEmail)
{
    //引入数据库信息
    global $pdo;
    // 验证邮箱是否合法
    if (!validateInput($toEmail, 'email')) {
        $result = array('message' => '邮箱格式错误!', 'success' => false);
    } elseif (!isEmailExist($pdo, $toEmail)) {// 判断邮箱是否存在
        // 创建 CaptchaManager 验证码 实例
        $captcha_manager = new CaptchaManager();
        // 处理验证码请求
        $result = $captcha_manager->handle_captcha_request(2, $toEmail, null);

    } else {
        $result = array('message' => '邮箱已存在!请直接登陆!', 'success' => false);
    }
    return $result;
}

//请求手机验证码函数
function getphonecode($toPhone)
{
    //引入数据库信息
    global $pdo;
    // 验证手机号是否合法
    if (!validateInput($toPhone, 'phone')) {
        $result = array('message' => '手机号格式错误!', 'success' => false);
    } elseif (isPhoneNumberExist($pdo, $toPhone)) {// 判断手机号是否存在
        // 创建 CaptchaManager 验证码 实例
        $captcha_manager = new CaptchaManager();
        //处理验证码请求
        $result = $captcha_manager->handle_captcha_request(3, null, $toPhone);

    } else {
        $result = array('message' => '手机号已存在!请直接登陆!', 'success' => false);
    }
    return $result;
}


//登录函数
function login($username, $password, $email, $phone, $code, $codeid)
{
    // 初始化结果数组
    $result = array();
    if (validateInput($email, 'email')) {
        // 调用登录获取token(邮箱+密码登陆)
        $result = login_verification_email($email, $password, $codeid, $code);
    } elseif (validateInput($phone, 'phone')) {
        // 调用登录获取token(手机号+密码登陆)
        $result = login_verification_phone($phone, $password, $codeid, $code);
    } else {
        // 调用登录获取token(账号+密码登陆)
        $result = login_verification($username, $password, $codeid, $code);
    }

    //返回token
    echo json_encode($result);
    // 关闭数据库连接
    global $mysqli;
    mysqli_close($mysqli);
}

//注册函数
function register($username, $password, $email, $phone, $code, $codeid)
{
    // 初始化结果数组
    $result = array();

    if (validateInput($email, 'email')) {
        // 使用邮箱注册逻辑
        // 调用使用邮箱注册的函数
        $result = register_email($username, $password, $email, $code, $codeid);

    } elseif (validateInput($phone, 'phone')) {
        // 使用手机号注册逻辑
        // 调用使用手机号注册的函数
        $result = register_phone($username, $password, $phone, $code, $codeid);

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













