<?php
/**
 * 银行卡还款扣款接口
 * User: zhan
 * Date: 2017/3/7
 * Time: 18:29
 *
 * error_code :
 * 8101 无此扣款计划信息
 * 8104 扣款计划已完成；提前扣款金额超过计划扣款金额
 *
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

$parameter = [
    'user_id' => '1001',
    'busi_partner' => '101001',
    'no_order' => Util::generateOrderNum(),
    'dt_order' => Util::getPayTime(),
    'name_goods' => '测试商品名称',
    'money_order' => 4.97,
    'notify_url' => 'http://host:port/notify_url.php',
    'risk_item' => $risk_item,
    'schedule_repayment_date' => '2017-03-08',
    'repayment_no' => '1008',
];

$parameter['no_agree'] = '2017030722009380';    // 签约协议号

$result = $ll->bankCardRepaymentDebit($parameter);

var_dump($result);