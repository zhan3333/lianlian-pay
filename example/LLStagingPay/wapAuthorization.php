<?php
/**
 * 用户授权接口
 *
 * 该接口为无首付分期计划接口
 *
 * 调用该接口前需要先查询用户签约信息，有签约信息才调用这个接口
 *
 * 商户的服务端可以通过连连支付授权申请 API 接口给已经签约过
 * 的用户进行单独授权。授权申请 API 接口采用 https post 方式提交，
 * 格式采用 json 报文格式。
 *
 * User: zhan
 * Date: 2017/3/7
 * Time: 17:46
 */


require_once '../../vendor/autoload.php';

$ll_config = require_once '../llpay.config.php';
$config = new \LLPay\Api\LLPayConfig($ll_config);
$ll = new \LLPay\Api\LLPay\LLStagingPay($config);

// 风控参数
$risk_item = [
    'user_info_full_name' => '詹光',
    'user_info_id_type' => '0',
    'user_info_id_no' => '420222************',
    'user_info_identify_state' => '0'
];

// 还款计划
$repayment_plan = [
    'repaymentPlan' => [
        [
            'date' => '2017-03-08',
            'amount' => '5',
        ],
        [
            'date' => '2017-03-09',
            'amount' => '5',
        ],
        [
            'date' => '2017-03-10',
            'amount' => '5',
        ]
    ]
];

// 短信配置
$sms_param = [
    'contract_type' => '优贷贷款',      // 合同名称
    'contact_way' => '15102728750'      // 联系方式
];

$user_id = '1001';                  // 用户id
$card_no = '621660080000077****';   // 银行卡号



// 先调用签约查询接口
$queryRet = $ll->userSignedInfoQuery([
    'user_id' => $user_id,
    'card_no' => $card_no
]);
if ($queryRet['ret_code'] == '0000') {
    if (intval($queryRet['count']) > 0) {
        // 存在签约信息，则可以使用 4.3 用户授权接口

        $no_agree = $queryRet['agreement_list'][0]['no_agree']; // 签约号

        // 授权参数
        $parameter = [
            'user_id' => '1001',
            'risk_item' => $risk_item,
            'repayment_plan' => $repayment_plan,
            'repayment_no' => '1008',
            'sms_param' => $sms_param,
            'no_agree' => $no_agree,
        ];
        echo '请求参数为: ' . json_encode($parameter, JSON_UNESCAPED_UNICODE) . PHP_EOL;
        $authRet = $ll->wapAuthorization($parameter);
        echo 'post请求结果：' . json_encode($authRet, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    }
} else {
    echo '查询失败！' . PHP_EOL;
}