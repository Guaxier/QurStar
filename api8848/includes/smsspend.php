<?php

// 引入所需命名空间
use AlibabaCloud\SDK\Sample\Sample;

/**
 * 发送短信并检查发送结果
 *
 * @param string $toPhone 接收短信的电话号码
 * @param mixed  $result  需要发送的内容或参数
 * 
 * @return bool 发送成功返回true，否则返回false
 */
function sendSMS($toPhone, $result){
    // 调用 SDK 发送短信
    $response = Sample::sendSMS($toPhone, $result);

// 检查短信发送是否成功
if (isset($response->body->code) && $response->body->code === "OK") {
    return array('success' => true, 'message' => '获取验证码成功！');
} else {
    $errorCode = isset($response->body->code) ? $response->body->code : 'Unknown'; // 如果没有code字段，则设为'Unknown'
    return array('success' => false, 'message' => '获取验证码失败，错误码：' . $errorCode);
}

}