<?php
// 本php为中心数据配置文件，用于配置各种常量


// 数据库连接配置
define('DB_HOST', 'localhost');
define('DB_USER', 'videosql');
define('DB_PASS', 'videosql');
define('DB_NAME', 'videosql');

//服务器配置信息
define('DB_secretKey','这里填入Token的服务端验证秘钥，安全性要求较高，建议定期更换，防止出现泄漏风险！');





//smspend.php，阿里云短信发送配置

define('ACCESS_KEY_ID', '您的 AccessKey ID');
define('ACCESS_KEY_SECRET', '您的 AccessKey Secret');
define('SECURITY_TOKEN','您的 Security Token');
define('ACCESS_type', '鉴权方式，默认sts,表明使用 STS 方式');


