<?php
/**
 * 连连支付签约授权接口 （提交计划与银行卡信息，如果未签约过则进行签约。）
 *
 * 这是一个创建订单的接口
 *
 * 商户的服务端可以通过连连支付API用户签约信息查询服务来查询
 * 用户在连连支付已绑定的银行卡列表信息。
 * 用户签约信息查询采用https post 方式提交，格式采用 json 报文格式。
 *
 * User: zhan
 * Date: 2017/3/7
 * Time: 15:19
 */

/**
 * 异步回调数据
 *
 * 前端页面会跳转到 url_return 指定地址，地址将接收到post数据如下
 * {"agreeno":"2017030722009380","oid_partner":"201701171001425788","repayment_no":"1002","sign":"D4X/lOA1jLcWtk0y/hVw7U070QMT729/mRCuhBZHlOb5l5gyPfWY3AQYH/qjU+Qjcpsw6EfjZRzD+47YOKExLAzxAg7/CA7ZwwXkFST9Qg2cpKR28qH1I6yFSlMcI1U/H0B20O/ZbPugHLdXOorGcC0uaU7E0DqWSI5A/Xv2EsM=","sign_type":"RSA","user_id":"1001"}
 *
 * 格式化后：
 * [
 *  'status' => '0000', // 结果代码
 *  'result' => [
 *      'od_partner' => '', // 商户号
 *      'user_id' => '',    // 商户用户唯一编号
 *      'agreeno' => '',    // 签约协议号
 *      'sign_type' => '',  // 签名类型
 *      'sign' => '',       // 签名
 *  ]
 * ]
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

$parameter = [
    'user_id' => '1001',
    'id_no' => '420222************',
    'acct_name' => '詹光',
    'card_no' => '621660080000077****',            // 银行卡号
    'risk_item' => $risk_item,
    'url_return' => 'http://host:port/notify_url.php',
    'repayment_plan' => $repayment_plan,
    'repayment_no' => '1009',
    'sms_param' => $sms_param
];

$result = $ll->wapSignedAuthorization($parameter);
$order = $result['order'];
$orderStr = json_encode($order);
echo '签约授权结果：' . json_encode($result['order']) . PHP_EOL;