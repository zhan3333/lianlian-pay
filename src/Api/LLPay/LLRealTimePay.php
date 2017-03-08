<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2017/3/7
 * Time: 12:58
 */

namespace LLPay\Api\LLPay;


use LLPay\Api\LLPayBase;
use LLPay\Api\LLPayConfig;
use LLPay\Util;

class LLRealTimePay extends LLPayBase
{

    // 连连支付实时付款接口
    const LL_REAL_TIME_PAY_URL = 'https://instantpay.lianlianpay.com/paymentapi/payment.htm';

    // 查询支付结果接口
    const LL_QUERY_PAY_RESULT_URL = 'https://instantpay.lianlianpay.com/paymentapi/queryPayment.htm';

    // 确认付款接口
    const LL_CONFIRM_PAY_URL = 'https://instantpay.lianlianpay.com/paymentapi/confirmPayment.htm';

    /**
     * LLRealTimePay constructor.
     * @param LLPayConfig $llpay_config
     */
    public function __construct($llpay_config)
    {
        parent::__construct($llpay_config);
    }

    /**
     * 连连支付实时付款交易接口
     *
     * @param array $parameter
     * <pre>
     * [
     *  'no_order' => '',       // 商户订单号
     *  'money_order' => '',    // 付款金额
     *  'card_no' => '',        // 银行卡号
     *  'acct_name' => '',      // 收款人姓名
     *  'notify_url' => '',     // 异步通知地址
     *  'info_order' => '',     // 订单描述
     *  'dt_order' => '',       // 订单提交时间
     * ]
     * </pre>
     * @return array   返回请求结果
     * <pre>
     * [
     *  'ret_code' => '',   // 结果代码
     *  'ret_msg' => '',    // 交易结果描述
     *  'oid_partner' => '',    // 商户id
     *  'no_order' => '',   // 商户订单号
     *  'oid_paybill' => '',    // 连连订单号，创建订单成功时有返回
     *  'confirm_code' => '',   // 当商户配置了要验证疑似订单和卡号姓名验证不一致，不一致时返回校验码
     * ]
     * </pre>
     */
    public function submit($parameter)
    {
        $data = [
            'oid_partner' => $this->llpay_config->oid_partner,
            'api_version' => '1.0',
            'sign_type' => $this->llpay_config->sign_type,
            'no_order' => $parameter['no_order'],
            'dt_order' => $parameter['dt_order'],
            'money_order' => $parameter['money_order'], // 付款金额
            'card_no' => $parameter['card_no'],         // 收款人银行卡号
            'acct_name' => $parameter['acct_name'],     // 收款人姓名
            'flag_card' => '0',
            'notify_url' => $parameter['notify_url'],   // 异步通知地址
            'info_order' => $parameter['info_order']
        ];

        //对参数排序加签名
        $sortPara = $this->buildRequestPara($data);

        //传json字符串
        $json = json_encode($sortPara);

        $parameterRequest = array (
            "oid_partner" => trim($this->llpay_config->oid_partner),
            "pay_load" => ll_encrypt($json, $this->llpay_config->LIANLIAN_PUBLICK_KEY) //请求参数加密
        );

        // 返回数据
        $retJson = $this->buildRequestJSON($parameterRequest, self::LL_REAL_TIME_PAY_URL);

        return json_decode($retJson, true);
    }

    /**
     * 确认付款接口
     *
     * 在submit接口返回有验证码时，需要调用此接口确认支付
     *
     * @param array $parameter
     * <pre>
     * [
     *  'no_order' => '',   // 商户订单号
     *  'confirm_code' => '',   // 由 submit 返回的订单号
     *  'notify_url' => '',     // 异步通知地址
     * ]
     * </pre>
     *
     * @return array 确认支付后返回结果
     * <pre>
     * [
     *  'ret_code' => '',       // 结果代码
     *  'ret_msg' => '',        // 结果描述
     *  'sign_type' => '',      // 签名方法
     *  'sign' => '',           // 签名
     *  'oid_partner' => '',    // 商户id
     *  'no_order' => '',       // 商户单号
     *  'oid_paybill' => '',    // 连连单号
     * ]
     * </pre>
     */
    public function confirmPay($parameter)
    {
        $data = [
            'oid_partner' => $this->llpay_config->oid_partner,
            'sign_type' => $this->llpay_config->sign_type,
            'api_version' => '1.0',
            'no_order' => $parameter['no_order'],
            'confirm_code' => $parameter['confirm_code'],
            'notify_url' => $parameter['notify_url'],
        ];

        //对参数排序加签名
        $sortPara = $this->buildRequestPara($data);

        //传json字符串
        $json = json_encode($sortPara);

        $parameterRequest = array (
            "oid_partner" => trim($this->llpay_config->oid_partner),
            "pay_load" => ll_encrypt($json, $this->llpay_config->LIANLIAN_PUBLICK_KEY) //请求参数加密
        );

        // 返回数据
        $retJson = $this->buildRequestJSON($parameterRequest, self::LL_CONFIRM_PAY_URL);

        return json_decode($retJson, true);
    }

    /**
     * 查询付款结果
     *
     * @param array $parameter
     * <pre>
     * [
     *  'no_order' => '',       // 商户订单号
     * ]
     * </pre>
     * @return array    订单查询结果
     * <pre>
     * [
     *  'ret_code' => '',
     *  'ret_msg' => '',
     *  'oid_partner' => '',    // 商户id
     *  'no_order' => '',       // 商户订单号
     *  'dt_order' => '',       // 商户下单时间 格式：YYYYMMDDH24MISS
     *  'money_order' => '',    // 付款金额
     *  'oid_paybill' => '',    // 连连支付单号，订单创建成功才会有
     *  'result_pay' => '',     // 付款结果
     * ]
     * </pre>
     *
     * result_pay 字段可能有 :
     * APPLY 付款申请
     * CHECK 复核申请
     * SUCCESS 付款成功
     * PROCESSING 付款处理中CANCEL 退款
     * FAILURE 失败
     * CLOSED 关闭
     *
     */
    public function queryPayResult($parameter)
    {
        $data = [
            'oid_partner' => $this->llpay_config->oid_partner,
            'api_version' => '1.0',
            'sign_type' => $this->llpay_config->sign_type,
            'no_order' => $parameter['no_order'],
        ];

        //对参数排序加签名
        $sortPara = $this->buildRequestPara($data);

        // 返回数据
        $retJson = $this->buildRequestJSON($sortPara, self::LL_QUERY_PAY_RESULT_URL);

        return json_decode($retJson, true);
    }

    /**
     * 处理返回回调数据
     *
     * @param string    $data   原始请求数据
     * @return bool|void    返回是否验证签名成功
     */
    public function verifyNotify($data = '')
    {
        //生成签名结果
        if (empty($data) || ! is_string($data)) return false;
        $parameter = $this->getNotifyParameter($data);
        $oid_partner = $parameter['oid_partner'];
        $sign = $parameter['sign'];
        unset($parameter['sign']);

        //首先对获得的商户号进行比对
        if ($oid_partner != $this->llpay_config['oid_partner']) {
            //商户号错误
            return false;
        }

        if (!$this->getSignVeryfy($parameter, $sign)) {
            return false;
        }

        return true;
    }

    /**
     * 解析原始数据为数组
     *
     * @param string $data 原始数据
     *
     * @return array
     */
    public function getNotifyParameter($data = '')
    {
        if (empty($data)) $str = file_get_contents("php://input");
        else $str = $data;
        $val = json_decode($str, true);
        $oid_partner = getJsonVal($val,'oid_partner' );
        $sign_type = getJsonVal($val,'sign_type' );
        $sign = getJsonVal($val,'sign' );
        $dt_order = getJsonVal($val,'dt_order' );
        $no_order = getJsonVal($val,'no_order' );
        $oid_paybill = getJsonVal($val,'oid_paybill' );
        $money_order = getJsonVal($val,'money_order' );
        $result_pay = getJsonVal($val,'result_pay' );
        $settle_date = getJsonVal($val,'settle_date' );
        $info_order = getJsonVal($val,'info_order');
        $pay_type = getJsonVal($val,'pay_type' );
        $bank_code = getJsonVal($val,'bank_code' );
        $no_agree = getJsonVal($val,'no_agree' );
        $id_type = getJsonVal($val,'id_type' );
        $id_no = getJsonVal($val,'id_no' );
        $acct_name = getJsonVal($val,'acct_name' );

        $parameter = array (
            'oid_partner' => $oid_partner,
            'sign_type' => $sign_type,
            'dt_order' => $dt_order,
            'no_order' => $no_order,
            'oid_paybill' => $oid_paybill,
            'money_order' => $money_order,
            'result_pay' => $result_pay,
            'settle_date' => $settle_date,
            'info_order' => $info_order,
            'pay_type' => $pay_type,
            'bank_code' => $bank_code,
            'no_agree' => $no_agree,
            'id_type' => $id_type,
            'id_no' => $id_no,
            'acct_name' => $acct_name,
            'sign' => $sign
        );

        return $parameter;
    }

    /**
     * 获取返回时的签名验证结果
     * @param array $para_temp 通知返回来的参数数组
     * @param string    $sign 返回的签名结果
     * @return bool 签名验证结果
     */
    private function getSignVeryfy($para_temp, $sign) {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = paraFilter($para_temp);

        //对待签名参数数组排序
        $para_sort = argSort($para_filter);

        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = createLinkstring($para_sort);

        //file_put_contents("log.txt", "原串:" . $prestr . "\n", FILE_APPEND);
        //file_put_contents("log.txt", "sign:" . $sign . "\n", FILE_APPEND);
        $isSgin = false;
        switch (strtoupper(trim($this->llpay_config['sign_type']))) {
            case "MD5" :
                $isSgin = md5Verify($prestr, $sign, $this->llpay_config['key']);
                break;
            case "RSA" :
                $isSgin = Rsaverify($prestr, $sign);
                break;
            default :
                $isSgin = false;
        }

        return $isSgin;
    }
}