<?php
require_once('FirePHPCore/FirePHP.class.php');
$firephp = FirePHP::getInstance(true);
$firephp->log('Plain Message');
echo "FirePHP Test!";
