<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 老猫 <zxxjjforever@163.com>
// +----------------------------------------------------------------------

// [ 入口文件 ]

// 调试模式开关
 error_reporting(0);
define("APP_DEBUG", false);

// 定义CMF根目录,可更改此目录
define('CMF_ROOT', __DIR__ . '/../');

// 定义应用目录
define('APP_PATH', CMF_ROOT . 'app/');

// 定义CMF核心包目录
define('CMF_PATH', CMF_ROOT . 'simplewind/cmf/');

// 定义插件目录
define('PLUGINS_PATH', __DIR__ . '/plugins/');

// 定义扩展目录
define('EXTEND_PATH', CMF_ROOT . 'simplewind/extend/');
define('VENDOR_PATH', CMF_ROOT . 'simplewind/vendor/');

// 定义应用的运行时目录
define('RUNTIME_PATH', CMF_ROOT . 'data/runtime/');

// 定义CMF 版本号
define('THINKCMF_VERSION', '5.0.170925');

// 加载框架基础文件
require CMF_ROOT . 'simplewind/thinkphp/base.php';

require CMF_ROOT. 'api_sdk/vendor/autoload.php';

require CMF_ROOT. 'msg_sdk/vendor/autoload.php';

require CMF_ROOT . 'lib/TokenGetterForAlicom.php';
require CMF_ROOT . 'lib/TokenForAlicom.php';
require CMF_ROOT . 'simplewind/vendor/PHPExcel/Classes/PHPExcel.php';

// 执行应用
\think\App::run()->send();
