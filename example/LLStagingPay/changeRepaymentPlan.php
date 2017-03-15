<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2017/3/8
 * Time: 13:02
 */

use LLPay\Util;

require_once '../../vendor/autoload.php';

$ll_config = require_once '../llpay.config.php';
$config = new \LLPay\Api\LLPayConfig($ll_config);
$ll = new \LLPay\Api\LLPay\LLStagingPay($config);

$risk_item = [
    'user_info_full_name' => '詹光',
    'user_info_id_type' => '0',
    'user_info_id_no' => '420222************',
    'user_info_identify_state' => '0'
];
$repayment_plan = [
    'repaymentPlan' => [
        [
            'date' => '2017-03-14',
            'amount' => '0.01',
        ]
    ]
];
$sms_param = [
    'contract_type' => '贷款',
    'contact_way' => '0571-12345678'
];

$parameter = [
    'user_id' => '1001',
    'repayment_plan' => $repayment_plan,
    'repayment_no' => '1008',
    'sms_param' => $sms_param
];

$result = $ll->changeRepaymentPlan($parameter);

echo '修改还款计划结果为：' . json_encode($result, JSON_UNESCAPED_UNICODE) . PHP_EOL;