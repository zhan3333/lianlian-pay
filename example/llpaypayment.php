<?php
use LLPay\Util;

/* *
 * 功能：连连支付实时付款交易接口
 * 版本：1.0
 * 修改日期：2016-11-28
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
 */

/**
 * 付款结果异步通知
 *
 * 一共通知6次，频率为6分钟，付款成功，失败，退款都会进行通知
 *
 * 通过post方式通知客户端
 *
 * <pre>
 * [
 *	'oid_partner' => '',	// 商户id
 * 	'no_order' => '',		// 商户订单号
 * 	'dt_order' => '',		// 付款时间
 * 	'money_order' => '',	// 支付金额
 * 	'oid_paybill' => '',	// 连连支付单号
 * 	'info_order' => '',		// 订单描述
 * 	'result_pay' => '',		// 付款状态，付款成功，付款失败，付款退款
 * 	'settle_date' => '',	// 清算日期
 * ]
 * </pre>
 *
 * result_pay:
 * SUCCESS 付款成功
 * FAILURE 付款失败
 * CANCEL 付款退款
 *
 */

require_once '../vendor/autoload.php';
$llpay_config = require_once ("llpay.config.php");
Util::loadFunction();


file_put_contents('log.txt', '开始执行', FILE_APPEND);
/**************************请求参数**************************/

$no_order = Util::generateOrderNum();
$notify_url = 'http://host:port/notify_url.php';
//构造要请求的参数数组，无需改动
$parameter = array (
	"no_order" => $no_order,
	"money_order" => '0.01',
	"acct_name" => '詹光',
	"card_no" => '621660080000077****',
	"notify_url" => $notify_url,
	'info_order' => 'test测试',
	'dt_order' => Util::getPayTime()
);



$config = new \LLPay\Api\LLPayConfig($llpay_config);
$ll = new \LLPay\Api\LLPay\LLRealTimePay($config);

/**
 *	在实际使用中，当$ret['ret_code']不为0000时，需要调用一次查询接口再对订单进行修改
 */
$ret = $ll->submit($parameter);
echo '实时付款接口返回数据：' . json_encode($ret, JSON_UNESCAPED_UNICODE) . PHP_EOL;

if (! empty($ret['confirm_code'])) {
	echo "验证码存在，为：{$ret['confirm_code']}" . PHP_EOL;
	// 若验证码存在
	// 1. 进行查询操作
	$queryRet = $ll->queryPayResult(['no_order' => $parameter['no_order']]);
	echo "订单 {$no_order} 支付结果查询为：" . json_encode($queryRet, JSON_UNESCAPED_UNICODE) . PHP_EOL;

	$confirmParameter = [
		'no_order' => $no_order,				// 商户订单号
		'confirm_code' => $ret['confirm_code'],	// 验证码
		'notify_url' => $notify_url				// 异步通知地址
	];
	$confirmRet = $ll->confirmPay($confirmParameter);
	echo "订单 {$no_order} 确认支付结果为: " . json_encode($confirmRet, JSON_UNESCAPED_UNICODE) . PHP_EOL;
}

$queryRet = $ll->queryPayResult(['no_order' => $parameter['no_order']]);
echo "订单 {$no_order} 支付结果查询为：" . json_encode($queryRet, JSON_UNESCAPED_UNICODE) . PHP_EOL;
