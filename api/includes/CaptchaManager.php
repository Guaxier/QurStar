<?php

class CaptchaManager {
    private $captcha_storage = [];
    
    // 构造函数
    public function __construct() {
        session_start(); // 开启 Session
    }
    
    // 生成加法验证码
    private function generate_addition_code() {
        $num1 = rand(0, 40);
        $num2 = rand(0, 40); 
        $result = $num1 + $num2;
        return ["$num1 + $num2", strval($result)];
    }
    
    // 生成减法验证码
    private function generate_subtraction_code() {
        $num1 = rand(0, 40);
        $num2 = rand(0, $num1); // 确保结果为正
        $result = $num1 - $num2;
        return ["$num1 - $num2", strval($result)];
    }
    
    // 生成乘法验证码
    private function generate_multiplication_code() {
        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        $result = $num1 * $num2;
        return ["$num1 × $num2", strval($result)];
    }
    // 生成除法验证码
    private function generate_division_code() {
        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        $product = $num1 * $num2;
        return ["$product ÷ $num1", strval($num2)];
    }
    
    // 获取一个数的所有因数对
    private function get_factors($num) {
        $factors = [];
        for ($i = 1; $i <= sqrt($num); $i++) {
            if ($num % $i == 0) {
                $factors[] = [$i, $num / $i];
            }
        }
        return $factors;
    }
    
    // 随机生成加减乘除法验证码
    private function generate_math_code() {
        $operations = ['+', '-', '*', '/'];
        $operation = $operations[array_rand($operations)];
        switch ($operation) {
            case '+':
                return $this->generate_addition_code();
            case '-':
                return $this->generate_subtraction_code();
            case '*':
                return $this->generate_multiplication_code();
            case '/':
                return $this->generate_division_code();
            default:
                return null;
        }
    }
    
    // 生成验证码图片
    private function generate_code_image($equation, $img_width=200, $img_height=100, $font_size=40) {
        // 创建图片对象，并设置白色背景
        $img = imagecreatetruecolor($img_width, $img_height);
        if (!$img) {
            throw new Exception("Failed to create image.");
        }
        $background_color = imagecolorallocate($img, 255, 255, 255);
        if ($background_color === false) {
            throw new Exception("Failed to allocate background color.");
        }
        imagefill($img, 0, 0, $background_color); // 填充白色背景
        $text_color = imagecolorallocate($img, 0, 0, 0);
        if ($text_color === false) {
            throw new Exception("Failed to allocate text color.");
        }
    
        // 获取字体
        $font = __DIR__."/abc.ttf";
        
        // 替换除号为 ÷
        $equation = str_replace('/', '÷', $equation);
        
        // 计算文字位置
        $bbox = imagettfbbox($font_size, 0, $font, $equation);
        if ($bbox === false) {
            throw new Exception("Failed to calculate text bounding box.");
        }
        $text_width = $bbox[2] - $bbox[0];
        $text_height = $bbox[1] - $bbox[7];
        $x = ($img_width - $text_width) / 2;
        $y = ($img_height - $text_height) / 2 + $text_height;
        // 绘制文字
        //if (!imagettftext($img, $font_size, 0, $x, $y, $text_color, $font, $equation)) {
        //    throw new Exception("Failed to draw text.");
        //}
        
        // 创建原始文字图像
        $text_img = imagecreatetruecolor($text_width, $text_height);
        imagefill($text_img, 0, 0, $background_color);
        imagettftext($text_img, $font_size, 0, 0, $text_height, $text_color, $font, $equation);
        
        // 扭曲文字
        $twisted_text_img = imagecreatetruecolor($text_width, $text_height);
        imagefill($twisted_text_img, 0, 0, $background_color);
        for ($x_offset = 0; $x_offset < $text_width; $x_offset++) {
            for ($y_offset = 0; $y_offset < $text_height; $y_offset++) {
                $x_source = $x_offset + sin($y_offset / 10) * 5; // 调整sin的参数以控制扭曲程度
                $y_source = $y_offset;
                if ($x_source >= 0 && $x_source < $text_width && $y_source >= 0 && $y_source < $text_height) {
                    $color = imagecolorat($text_img, $x_source, $y_source);
                    imagesetpixel($twisted_text_img, $x_offset, $y_offset, $color);
                }
            }
        }
        
        // 将扭曲后的文字绘制到验证码图片中
        imagecopyresampled($img, $twisted_text_img, $x, $y - $text_height, 0, 0, $text_width, $text_height, $text_width, $text_height);
        
        // 添加噪点
        $noise_color = imagecolorallocate($img, 0, 0, 0);
        for ($i = 0; $i < 100; $i++) {
            imagesetpixel($img, rand(0, $img_width), rand(0, $img_height), $noise_color);
        }
    
        // 添加干扰线
        for ($i = 0; $i < 5; $i++) {
            $line_color = imagecolorallocate($img, rand(0, 255), rand(0, 255), rand(0, 255));
            imageline($img, 0, rand() % $img_height, $img_width, rand() % $img_height, $line_color);
        }
        
        // 返回图片对象
        return $img;
    }

    
// 生成验证码
public function generate_captcha() {
    list($equation, $result) = $this->generate_math_code();
    $image = $this->generate_code_image($equation);
    //$timestamp = time();
    //$_SESSION['captcha_result'] = $result;//保存验证码
    //$_SESSION['captcha_timestamp'] = $timestamp;//保存时间戳
    //$this->captcha_storage[$result] = [$equation, $image, $timestamp];
    
    // 将验证码写入数据库
    $verification_code = $this->generate_verification_code($result);
    return [$equation,$result, $image, $verification_code]; // 修改
}

// 生成邮箱验证码
public function getcode_email($toEmail) {
    $characters = '0123456789';
    $code = '';
    $max = strlen($characters) - 1;
    for ($i = 0; $i < 6; $i++) {
        $code .= $characters[mt_rand(0, $max)];
    }
    // 将验证码写入数据库
    $verification_code = $this->generate_verification_code($code,$toEmail);
    return [$code,$verification_code]; // 验证码和验证码ID
}

// 处理验证码请求
public function handle_captcha_request($codetype = '1',$toEmail = null) {
    //创建一个switch
    switch ($codetype) {
        case '1'://为默认值，生成普通验证码
            // 生成新的验证码
            list($equation,$result, $image, $verification_code_id) = $this->generate_captcha(); // 修改
            if (!$equation || !$image) {
                return array('success' => false,'message' => '获取验证码失败！');
            }

            // 将验证码图片转换为 Base64 编码的字符串
            ob_start(); // 开启缓冲区
            imagejpeg($image); // 将图片输出到缓冲区
            $image_data = ob_get_clean(); // 读取缓冲区的内容并清除缓冲区
            $base64_image = 'data:image/jpeg;base64,' . base64_encode($image_data);

            // 返回信息
            return array(
                'success' => true,
                'codeid' => $verification_code_id, // 验证码id
                'image' => $base64_image,//验证码图片
                'message' => '获取验证码成功！',//提示信息
                '题目（测试使用）' => $equation,//验证码题目（测试使用）
                '答案（测试使用）' => $result, // 验证码答案（测试使用）
                
            );
        case '2'://获取邮箱验证码
            list($result,$verification_code_id,) = $this->getcode_email($toEmail);
            // 验证码状态
            if (!$result) {
                return array('success' => false,'message' => '获取验证码失败！');
            }
            // 发送邮件
            if (sendEmail($toEmail, $result)) {
                return array('success' => true,'codeid' => $verification_code_id,'message' => '邮件已发送！');
            } else {
                return array('success' => false,'codeid' => $verification_code_id,'message' => '邮件服务器异常！');
            }
            
        default:
            return array('success' => false,'message' => '获取验证码失败！');
    }
}

// 存储验证码到数据库
function generate_verification_code($code,$user = null) {
    global $mysqli;


    //判断验证码是否具有指向性
    if($user){
        // 指向性验证码
        $sql = "INSERT INTO VerificationCodes (code, user_id, creation_time) VALUES (?, ?, NOW())";
    }else{
        // 普通验证码
        $sql = "INSERT INTO VerificationCodes (code, creation_time) VALUES (?, NOW())";
    }

    // 使用预处理语句防止 SQL 注入攻击
    $stmt = $mysqli->prepare($sql);

    // 绑定参数
    if($user){
        // 指向性验证码
        $stmt->bind_param("ss", $code, $user); // 第一个参数为参数类型，s 表示字符串，i 表示整数
    }else{
        // 普通验证码
        $stmt->bind_param("s", $code); // 第一个参数为参数类型，s 表示字符串，i 表示整数
    }

    // 执行预处理语句
    $stmt->execute();

    // 获取插入的自增ID
    $verification_code_id = $stmt->insert_id;

    // 关闭预处理语句
    $stmt->close();
    return $verification_code_id;
}

}

//验证码校验
function verifyCode($id, $code,$user = null) {
    // 引入数据库信息
    global $mysqli;
    // 准备 SQL 语句
    //邮箱是否存在
    if($user){
        $sql = "SELECT IF(creation_time >= NOW() - INTERVAL 3 MINUTE, '未过期', '已过期') AS status 
            FROM VerificationCodes 
            WHERE id = ? AND code = ? AND user_id = ? AND creation_time >= NOW() - INTERVAL 2 MINUTE";
    }else{
        $sql = "SELECT IF(creation_time >= NOW() - INTERVAL 2 MINUTE, '未过期', '已过期') AS status 
            FROM VerificationCodes 
            WHERE id = ? AND code = ? AND creation_time >= NOW() - INTERVAL 2 MINUTE";
    }
    // 预处理 SQL 语句
    $stmt = $mysqli->prepare($sql);
    // 预处理 SQL 语句
    if($user){
        // 绑定参数
        $stmt->bind_param("iss", $id, $code, $user); // 注意参数类型描述符和参数数量
    } else {
        // 绑定参数
        $stmt->bind_param("is", $id, $code); // 注意参数类型描述符和参数数量
    }
    // 执行查询
    $stmt->execute();
    // 存储结果
    $stmt->store_result();
    // 绑定结果变量
    $stmt->bind_result($status);
    // 获取查询结果
    if ($stmt->fetch()) {
        $stmt->close(); // 关闭预处理语句
        return array(
            'success' => true,
            'message' => '验证码未过期！',//提示信息
        );
    } else {
        $stmt->close(); // 关闭预处理语句
        return array(
            'success' => false,
            'message' => '验证码过期或不存在！',//提示信息
        );
    }
}


