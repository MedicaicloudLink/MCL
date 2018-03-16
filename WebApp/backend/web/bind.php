<?php
require_once('Gateway.php');

// 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值
Gateway::$registerAddress = '106.75.32.143:1238';
// 假设用户已经登录，用户uid和群组id在session中
$uid       = $_POST['userid'];
$client_id = $_POST['client_id'];
//$group_id = $_SESSION['group'];
// client_id与uid绑定
Gateway::bindUid($client_id, $uid);