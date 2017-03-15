<?php
/**
 * 商户订单查询
 * User: zhan
 * Date: 2017/3/7
 * Time: 16:48
 */
require_once '../../vendor/autoload.php';
$ll_config = require_once '../llpay.config.php';

$parameter = [
    'no_order' => '62451080502192****',
    'dt_order' => '20170308130947',
    'oid_paybill' => ''
];

$config = new \LLPay\Api\LLPayConfig($ll_config);
$ll = new \LLPay\Api\LLPay\LLStagingPay($config);
$result = $ll->merchantPayResultQuery($parameter);
echo '商户订单查询结果为：' . json_encode($result, JSON_UNESCAPED_UNICODE) . PHP_EOL;