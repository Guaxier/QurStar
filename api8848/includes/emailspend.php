<?php
require __DIR__.'/depend/PHPMailer/src/PHPMailer.php';
require __DIR__.'/depend/PHPMailer/src/SMTP.php';
require __DIR__.'/depend/PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



function sendEmail($toEmail, $verificationCode, $toName = '验证码') {
    // 实例化 PHPMailer
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8'; // 设置邮件字符编码为 UTF-8

    try {
        // 配置 SMTP 设置
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // SMTP 服务器地址
        $mail->SMTPAuth   = true;                // 启用 SMTP 验证
        $mail->Username   = 'Guaxier@Gmail.com'; // SMTP 邮箱账号
        $mail->Password   = 'waceclvmfkcfrwgv';     // SMTP 邮箱密码
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // 启用 TLS 加密
        $mail->Port       = 587; // 根据实际情况选择合适的端口

        // 设置发件人
        $mail->setFrom('Guaxier@Gmail.com', '七零网络');

        // 设置收件人
        $mail->addAddress($toEmail, $toName);

        // 设置邮件内容
        $mail->isHTML(true); // 将邮件内容设置为 HTML 格式
        $mail->Subject = '验证码'; // 邮件主题
        $mail->Body    = '【齐心创意】您正在尝试注册，验证码为： ' . $verificationCode . '(3分钟内有效)，为了您的安全，请不要将本验证码告诉任何人！如果您不知道自己为什么收到这个验证码，请忽略它！'; // 邮件内容

        // 发送邮件
        if (!$mail->send()) {
            // 将错误信息写入到1.txt文件
            file_put_contents('1.txt', $mail->ErrorInfo, FILE_APPEND);
            return false; // 发送失败
        }

        return true; // 发送成功
    } catch (Exception $e) {
        // 将异常信息写入到1.txt文件
        file_put_contents('1.txt', 'Caught exception: ' . $e->getMessage(), FILE_APPEND);
        return false; // 发送失败
    }
}



/*网易
function sendEmail($toEmail, $verificationCode, $toName = '验证码') {
    // 实例化 PHPMailer
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8'; // 设置邮件字符编码为 UTF-8

    try {
        // 配置 SMTP 设置
        $mail->isSMTP();
        $mail->Host       = 'smtp.163.com'; // SMTP 服务器地址
        $mail->SMTPAuth   = true;                // 启用 SMTP 验证
        $mail->Username   = '13419296036@163.com'; // SMTP 邮箱账号
        $mail->Password   = 'LXOYIZVSUBQUMWLN';     // SMTP 邮箱密码
        //$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // 启用 TLS 加密
        $mail->Port       = 25; // 根据实际情况选择合适的端口（这里应该是587而不是465，因为使用的是TLS）

        // 设置发件人
        $mail->setFrom('13419296036@163.com', '七零网络');

        // 设置收件人
        $mail->addAddress($toEmail, $toName);

        // 设置邮件内容
        $mail->isHTML(true); // 将邮件内容设置为 HTML 格式
        $mail->Subject = '验证码'; // 邮件主题
        $mail->Body    = '【齐心创意】您正在尝试注册，验证码为： ' . $verificationCode . '(3分钟内有效)，为了您的安全，请不要将本验证码告诉任何人！如果您不知道自己为什么收到这个验证码，请忽略它！'; // 邮件内容

        // 发送邮件
        if (!$mail->send()) {
            // 将错误信息写入到1.txt文件
            file_put_contents('1.txt', $mail->ErrorInfo, FILE_APPEND);
            return false; // 发送失败
        }

        return true; // 发送成功
    } catch (Exception $e) {
        // 将异常信息写入到1.txt文件
        file_put_contents('1.txt', 'Caught exception: ' . $e->getMessage(), FILE_APPEND);
        return false; // 发送失败
    }
}
*/
/*
// QQ
function sendEmail($toEmail, $verificationCode, $toName = '验证码') {
    // 实例化 PHPMailer
    $mail = new PHPMailer(true);
    $mail->CharSet = 'UTF-8'; // 设置邮件字符编码为 UTF-8

    try {
        // 配置 SMTP 设置
        $mail->isSMTP();
        $mail->Host       = 'smtp.qq.com'; // SMTP 服务器地址
        $mail->SMTPAuth   = true;                // 启用 SMTP 验证
        $mail->Username   = '2537094196@qq.com'; // SMTP 邮箱账号
        $mail->Password   = 'oumfjcrnoqwpeabf';     // SMTP 邮箱密码
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // 启用 TLS 加密
        $mail->Port       = 587; // SMTP 端口

        // 设置发件人
        $mail->setFrom('2537094196@qq.com', '七零网络');

        // 设置收件人
        $mail->addAddress($toEmail, $toName);

        // 设置邮件内容
        $mail->isHTML(true); // 将邮件内容设置为 HTML 格式
        $mail->Subject = '验证码'; // 邮件主题
        $mail->Body    = '【齐心创意】您正在尝试注册，验证码为： ' . $verificationCode . '(3分钟内有效)，为了您的安全，请不要将本验证码告诉任何人！如果您不知道自己为什么收到这个验证码，请忽略它！'; // 邮件内容

        // 发送邮件
        $mail->send();
        return true; // 发送成功
    } catch (Exception $e) {
        return false; // 发送失败
    }
}
*/