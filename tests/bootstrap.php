<?php

// change the following paths if necessary
$yiit=dirname(__FILE__).'/../../../../lib/php/yii/yii-1.1.10.r3566/framework/yiit.php';
$config=dirname(__FILE__).'/../config/test.php';

require_once($yiit);
require_once(dirname(__FILE__).'/WebTestCase.php');

defined('YII_DEBUG') or define('YII_DEBUG',true);
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',0);

Yii::createWebApplication($config);
Yii::getLogger()->autoFlush = 1;
// when sending a message to log routes, also notify them to dump the message
// into the corresponding persistent storage (e.g. DB, email)
Yii::getLogger()->autoDump = true;