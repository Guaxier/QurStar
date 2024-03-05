


    * 系统名称: 齐心创艺礼物系统（API部分）
    * 作者信息: 郭影（www.qilingwl.com）
    * 最后修改: 2024/02/27
    * 项目介绍: 一个礼物平台后端模板，遵循"GPL-3.0 license"
 
    * 项目更新：2024/02/27【首次创建】
                ----2024/02/27【用户表设计, token表设计, 个人资料表设计】
                ----2024/02/28【图形验证码系统, 验证码Api，登录Api，token Api】
                ----2024/02/29【优化登录的接口调用，项目结构优化，重构API函数】
                ----2024/03/01【注册API, 修改密码API】 个人信息API, 个人资料API, 个人资料修改API】
                ----2024/03/02【优化注册和登录逻辑，验证接口调整，账号系统调试】
                ----2024/03/03【手机验证码系统】
                ----2024/03/04【摆烂】
                ----2024/03/05【找回密码API，状态检查】
                ----2024/03/06【邮箱验证API，接口优化】
    
    * 依赖环境：Nginx1.24,PHP8.3,Mysql5.7,Vue3
                ----php模块：PHPMailer.php,SMTP.php
                ----Vue模块:;

---
---

API接口:
```
具体的API调用方法请参照docs/index.html
```
项目结构:


```
project/
│
├── api/                        # API 路径
│   ├── auth/                   # 用户认证相关脚本
│   │   ├── login.php           # 处理用户登录的脚本
│   │   ├── register.php        # 处理用户注册的脚本
│   │   └── forgot_password.php # 处理用户忘记密码的脚本
│   │
│   │
│   │
│   │
│   ├── includes/               # 项目核心文件
│   │   ├── depend              # 依赖文件 
│   │   │   └── PHPMailer       # PHP 邮件类
│   │   │ 
│   │   ├── abc.ttf             # 字体文件(验证码使用)
│   │   ├── config.php          # 数据库连接配置文件
│   │   ├── CaptchaManager.php  # 验证码管理类
│   │   └── functions.php       # 共享函数文件
│   │
│   ├── log/                    # 日志目录
│   │   └── php_errors.log      # PHP 错误日志
│   │
│   ├── api.php                 # API出口
│   └── .htaccess               # Apache 配置文件，用于 URL 重写
│
├── web/                        # 前端页面目录
│   ├── index.html              # 主页   
│   └── index.php               # 路由处理脚本
│
├── docs/                       # API 文档目录
│   ├── sql.sql                 # 数据库初始化脚本
│   └── index.html              # 项目文档首页
│
├── .gitignore                  # Git 忽略配置文件
├── LICENSE                     # 项目许可协议文件
└── README.md                   # 项目说明文档
```

结构说明：
```
api/ 目录包含所有的 API 脚本。
auth/ 子目录包含处理身份验证相关功能的脚本，如登录、注册和忘记密码。
includes/ 目录包含共享的配置和功能文件。
.htaccess 文件用于 Apache 服务器的 URL 重写，以美化 URL 结构。
docs/ 目录用于存放 API 文档，其中 index.html 可以作为项目文档的入口。
.gitignore 用于 Git 版本控制时指定不需要追踪的文件和目录。
README.md 是项目的说明文档，可以包含项目的介绍、安装和使用说明等信息。