<?php
require_once('Gateway.php');
// 设置GatewayWorker服务的Register服务ip和端口，请根据实际情况改成实际值
Gateway::$registerAddress = '106.75.32.143:1238';
//$uid = Gateway::getUidByClientId('7f0000010b5500000003');
//$cid = Gateway::getClientIdByUid('mubwVKe13g');
//print_r($cid);exit;
// 向任意uid的网站页面发送数据
Gateway::sendToUid('mubwVKe13g', '23');
//Gateway::sendToAll('aaaabbbb');