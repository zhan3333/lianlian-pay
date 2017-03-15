<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2017/3/7
 * Time: 15:19
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
            'date' => '2017-03-08',
            'amount' => '0.01',
        ],
        [
            'date' => '2017-03-09',
            'amount' => '0.01',
        ],
        [
            'date' => '2017-03-10',
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
    'busi_partner' => '101001',
    'no_order' => Util::generateOrderNum(),
    'dt_order' => Util::getPayTime(),
    'name_goods' => '测试商品名称',
    'money_order' => '0.01',
    'notify_url' => 'http://host:port/notify_url.php',
    'url_return' => 'http://host:port/notify_url.php',
    'id_no' => '420222************',
    'acct_name' => '詹光',
    'risk_item' => $risk_item,
    'repayment_plan' => $repayment_plan,
    'repayment_no' => '1006',
    'sms_param' => $sms_param
];

$result = $ll->wapSignedAuthorizationPay($parameter);
$order = $result['order'];
$orderStr = json_encode($order);
$url = $result['url'];
$query = Requests::get($url . "?res_data={$orderStr}", []);
file_put_contents('log.txt', $query->body, FILE_APPEND);
echo json_encode($result['order']) . PHP_EOL;