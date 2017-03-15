<?php


/* *
 * 功能：连连支付服务器异步通知页面
 * 版本：2.0
 * 日期：2014-10-16
 * 说明：
 * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。


 *************************页面功能说明*************************
 * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
 * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
 */
require_once '../vendor/autoload.php';
require_once "llpay.config.php";

file_put_contents('log.txt', '接收到回调数据' . PHP_EOL, FILE_APPEND);
if (!empty($_GET)) file_put_contents('log.txt', 'GET数据为：' . json_encode($_GET) . PHP_EOL, FILE_APPEND);
if (!empty($_POST)) file_put_contents('log.txt', 'POST数据为：' . json_encode($_GET) . PHP_EOL, FILE_APPEND);
$config = new \LLPay\Api\LLPayConfig($llpay_config);
$ll = new \LLPay\Api\LLPay\LLRealTimePay($config);
$data = file_get_contents('php://input');
file_put_contents('log.txt', '异步通知原始数据：' . $data . PHP_EOL, FILE_APPEND);
$parameter = $ll->getNotifyParameter($data);
$isVerify = $ll->verifyNotify($data);
if ($isVerify) {
	//获取连连支付的通知返回参数，可参考技术文档中服务器异步通知参数列表
	$no_order = $parameter['no_order'];//商户订单号
	$oid_paybill = $parameter['oid_paybill'];//连连支付单号
	$result_pay = $parameter['result_pay'];//支付结果，SUCCESS：为支付成功
	$money_order = $parameter['money_order'];// 支付金额
	if($result_pay == "SUCCESS"){
		//请在这里加上商户的业务逻辑程序代(更新订单状态、入账业务)
		//——请根据您的业务逻辑来编写程序——
		//payAfter($llpayNotify->notifyResp);
	}
	file_put_contents("log.txt", "异步通知 验证成功\n", FILE_APPEND);
	$ret = [
		'ret_code' => '0000',
		'ret_msg' => '交易成功'
	];
	echo json_encode($ret);
} else {
	file_put_contents("log.txt", "异步通知 验证失败\n", FILE_APPEND);
	//验证失败
	$ret = [
		'ret_code' => '9999',
		'ret_msg' => '验签失败'
	];
	echo json_encode($ret);
	//调试用，写文本函数记录程序运行情况是否正常
	//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
}