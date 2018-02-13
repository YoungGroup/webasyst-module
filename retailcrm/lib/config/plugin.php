<?php

$appConfig = wa()->getConfig()->getAppConfig('shop');
$workflowPath = $appConfig->getConfigPath('data/workflow.php', false);
$config = include ($workflowPath);
$handlers = array();

foreach ($config['actions'] as $ak => $vk) {
    $handlers["order_action." . $ak] = 'orderAdd';
}

return array(
    'name'          => 'Retailcrm',
    'description'   => 'Автоматизация интернет-продаж',
    'vendor'        => '1009747',
    'version'       => '3.0.1',
    'img'           => 'img/icon.png',
    'shop_settings' => true,
    'frontend'      => true,
    'handlers'      => $handlers
);
