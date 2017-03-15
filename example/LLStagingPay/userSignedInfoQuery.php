<?php
/**
 * 用户签约信息查询
 * User: zhan
 * Date: 2017/3/7
 * Time: 16:48
 */
require_once '../../vendor/autoload.php';
$ll_config = require_once '../llpay.config.php';

$parameter = [
    'user_id' => '1001',
//    'card_no' => '621660080000077****'
    'no_agree' => '2017030722009380'
];

$config = new \LLPay\Api\LLPayConfig($ll_config);
$ll = new \LLPay\Api\LLPay\LLStagingPay($config);
$result = $ll->userSignedInfoQuery($parameter);
var_dump($result);