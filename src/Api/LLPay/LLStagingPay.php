<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2017/3/7
 * Time: 14:53
 */

namespace LLPay\Api\LLPay;


use LLPay\Api\LLPayBase;
use LLPay\Api\LLPayConfig;


/**
 * 分期付接口
 *
 * WAP签约授权支付接口 为有首付的签约授权支付接口
 *
 * WAP签约授权接口 为未签约用户无首付的签约授权接口。在调用此接口前需先进行用户的签约查询，已签约的用户的调用此接口将无效
 *
 * WAP授权申请接口: 为已签约用户无首付签约授权接口。在调用此接口前需先进行用户的签约查询。同一计划号下时，重复提交此接口，将刷新用户的还款记录
 *
 * 还款计划变更接口: 计划中钱总额不得超过原定计划中剩余未支付总额
 *
 * 银行卡还款扣款接口: 商户需调用此接口，执行还款操作
 *
 * 用户签约信息查询接口: 查询用户签约信息
 *
 * 商户支付结果查询接口: 查询商户支付结果
 *
 * Class LLStagingPay
 * @package LLPay\Api\LLPay
 */
class LLStagingPay extends LLPayBase
{
    // WAP签约授权支付接口
    const WAP_SIGNED_AUTHORIZATION_PAY_URL = 'https://wap.lianlianpay.com/installment.htm';

    // WAP签约授权接口
    const WAP_SIGNED_AUTHORIZATION_URL = 'https://yintong.com.cn/llpayh5/signApply.htm';

    // WAP授权接口
    const WAP_AUTHORIZATION_URL = 'https://repaymentapi.lianlianpay.com/agreenoauthapply.htm';

    // 还款计划变更接口
    const CHANGE_REPAYMENT_PLAN_URL = 'https://repaymentapi.lianlianpay.com/repaymentplanchange.htm';

    // 银行卡还款扣款接口
    const BANK_CARD_REPAYMENT_DEBIT_URL = 'https://repaymentapi.lianlianpay.com/bankcardrepayment.htm';

    // 银行卡卡BIN查询接口
    const BANK_CARD_BIN_QUERY_URL = 'https://queryapi.lianlianpay.com/bankcardbin.htm';

    // 用户签约信息查询接口
    const USER_SIGNED_INFO_QUERY_URL = 'https://queryapi.lianlianpay.com/bankcardbindlist.htm';

    // 商户支付结果查询接口
    const MERCHANT_PAY_RESULT_QUERY_URL = 'https://queryapi.lianlianpay.com/orderquery.htm';

    /**
     * LLStagingPay constructor.
     * @param LLPayConfig $config
     */
    public function __construct($config)
    {
        parent::__construct($config);
    }

    /**
     * WAP签约授权支付接口
     *
     * @param array $parameter
     * <pre>
     * [
     *  'user_id' => '',        // 用户在平台唯一标识
     *  'busi_partner' => '',   // 商户业务类型,虚拟：101001，实物：109001
     *  'no_order' => '',       // 商户唯一订单号
     *  'dt_order' => '',       // 商户订单时间 'YYYYMMDDH24MISS', 14位数字
     *  'name_goods' => '',     // 商品名称
     *  'money_order' => '',    // 订单总金额
     *  'notify_url' => '',     // 异步通知地址
     *  'id_no' => '',          // 证件号码
     *  'acct_name' => '',      // 银行账号姓名
     *  'risk_item' => '',      // 风险控制参数
     *  'repayment_plan' => [], // 还款计划
     *  'repayment_no' => '',   // 还款计划编号
     *  'sms_param' => [],      // 短信参数
     * ]
     * </pre>
     *
     * 风控参数
     * <pre>
     * [
     *  [
     *      'user_info_full_name' => '',    // 用户注册姓名
     *      'user_info_id_type' => '',      // 用户注册证件类型  0: 身份证或企业经营证件
     *      'user_info_id_no' => '',        // 用户注册证件号码
     *      'user_info_identify_state' => '',   // 是否实名认证 1：是，0：无认证
     *      'user_info_identify_type' => '',    // 认证方式,实名认证时必填 1:银行卡认证，2：现场认证，3：身份证远程认证，4：其它认证
     *  ]
     * ]
     * </pre>
     *
     * 短信参数
     * <pre>
     * [
     *  'contract_type' => '',  // 合同类型
     *  'contact_way' => ''     // 联系方式
     * ]
     * </pre>
     *
     *
     * @return array
     * <pre>
     * [
     *  'order' => '',   //
     * ]
     * </pre>
     *
     * 成功后将异步post返回到 url_return 地址中
     *
     * 返回结构
     * <pre>
     * [
     *  'oid_partner' => '',    // 商户编号
     *  'dt_order' => '',       // 订单时间
     *  'no_order' => '',       // 商户唯一编号
     *  'oid_paybill' => '',    // 连连订单号
     *  'result_pay' => '',     // 支付结果 SUCCESS 成功，PROCESSING 处理中
     *  'money_order' => '',    // 交易金额，单位为元
     *  'settle_date' => '',    // 清算日期 YYYYMMDD
     * ]
     * </pre>
     *
     */
    public function wapSignedAuthorizationPay($parameter)
    {
        $data = [
            'version' => '1.0',
            'oid_partner' => $this->llpay_config->oid_partner,
            'user_id' => $parameter['user_id'],
            'app_request' => '3',
            'sign_type' => $this->llpay_config->sign_type,
            'busi_partner' => $parameter['busi_partner'],
            'no_order' => $parameter['no_order'],
            'dt_order' => $parameter['dt_order'],
            'name_goods' => $parameter['name_goods'],
            'money_order' => $parameter['money_order'],
            'notify_url' => $parameter['notify_url'],
            'id_type' => '0',
            'id_no' => $parameter['id_no'],
            'acct_name' => $parameter['acct_name'],
            'risk_item' => json_encode($parameter['risk_item']),
            'pay_type' => 'D',
            'repayment_plan' => json_encode($parameter['repayment_plan']),
            'repayment_no' => $parameter['repayment_no'],
            'sms_param' => json_encode($parameter['sms_param'])
        ];
        $order = $this->buildRequestPara($data);
        return [
            'order' => $order,
            'url' => self::WAP_SIGNED_AUTHORIZATION_PAY_URL
        ];
    }

    /**
     * WAP签约授权接口
     *
     * 相当于无首付模式
     *
     * @param array $parameter
     * 签约所需参数
     * <pre>
     * [
     *  'user_id' => '',    // 用户唯一标识
     *  'id_no' => '',      // 证件号码
     *  'acct_name' => '',  // 证件姓名
     *  'card_no' => '',    // 银行卡号
     *  'risk_item' => [],  // 风控数据
     *  'url_return' => '', // 签约结束回调地址
     *  'repayment_plan' => [], // 还款计划
     *  'repayment_no' => '',   // 还款计划编号
     *  'sms_param' => [],  // 短信参数
     * ]
     * </pre>
     *
     * 风控参数
     * <pre>
     * [
     *  [
     *      'user_info_full_name' => '',    // 用户注册姓名
     *      'user_info_id_type' => '',      // 用户注册证件类型  0: 身份证或企业经营证件
     *      'user_info_id_no' => '',        // 用户注册证件号码
     *      'user_info_identify_state' => '',   // 是否实名认证 1：是，0：无认证
     *      'user_info_identify_type' => '',    // 认证方式,实名认证时必填 1:银行卡认证，2：现场认证，3：身份证远程认证，4：其它认证
     *  ]
     * ]
     * </pre>
     *
     * 短信参数
     * <pre>
     * [
     *  'contract_type' => '',  // 合同类型
     *  'contact_way' => ''     // 联系方式
     * ]
     * </pre>
     *
     * @return array
     * <pre>
     * [
     *  'order' => '',
     *  'url' => '',
     * ]
     * </pre>
     *
     * 成功后将异步post返回到 url_return 地址中
     *
     * 返回结构
     * <pre>
     * [
     *  'status' => '',     // 交易结果代码 0000 为成功
     *  'result' => [
     *      'sign_type' => '',
     *      'sign' => '',
     *      'oid_partner' => '',    // 商户编号
     *      'user_id' => '',        // 用户id
     *      'agree_no' => ''        // 签约协议号
     *  ]
     * ]
     * </pre>
     *
     */
    public function wapSignedAuthorization($parameter)
    {
        $data = [
            'version' => '1.0',
            'oid_partner' => $this->llpay_config->oid_partner,
            'user_id' => $parameter['user_id'],
            'app_request' => '3',
            'sign_type' => $this->llpay_config->sign_type,
            'id_type' => '0',
            'id_no' => $parameter['id_no'],
            'acct_name' => $parameter['acct_name'],
            'card_no' => $parameter['card_no'],
            'pay_type' => 'I',
            'risk_item' => json_encode($parameter['risk_item']),
            'url_return' => $parameter['url_return'],
            'repayment_plan' => json_encode($parameter['repayment_plan']),
            'repayment_no' => $parameter['repayment_no'],
            'sms_param' => json_encode($parameter['sms_param'])
        ];
        $order = $this->buildRequestPara($data);
        return [
            'order' => $order,
            'url' => self::WAP_SIGNED_AUTHORIZATION_URL
        ];
    }

    /**
     * wap授权申请接口
     *
     * 无首付模式下，用户若已经签约过，则直接调用这个授权接口
     *
     * @param array $parameter
     * <pre>
     * [
     *  'user_id' => '',
     *  'repayment_plan' => [],
     *  'sms_param' => [],
     *  'repayment_no' => '',
     *  'no_agree' => '',
     * ]
     * </pre>
     *
     * 风控参数
     * <pre>
     * [
     *  [
     *      'user_info_full_name' => '',    // 用户注册姓名
     *      'user_info_id_type' => '',      // 用户注册证件类型  0: 身份证或企业经营证件
     *      'user_info_id_no' => '',        // 用户注册证件号码
     *      'user_info_identify_state' => '',   // 是否实名认证 1：是，0：无认证
     *      'user_info_identify_type' => '',    // 认证方式,实名认证时必填 1:银行卡认证，2：现场认证，3：身份证远程认证，4：其它认证
     *  ]
     * ]
     * </pre>
     *
     * 短信参数
     * <pre>
     * [
     *  'contract_type' => '',  // 合同类型
     *  'contact_way' => ''     // 联系方式
     * ]
     * </pre>
     *
     * @return array
     * <pre>
     * [
     *  'ret_code' => '',       // 结果代码
     *  'ret_msg' => '',        // 结果描述
     *  'token' => '',          // 授权码
     *  'sign_type' => '',
     *  'sign' => '',
     *  'oid_partner' => ''     // 商户id
     * ]
     * </pre>
     */
    public function wapAuthorization($parameter)
    {
        $data = [
            'user_id' => $parameter['user_id'],
            'oid_partner' => $this->llpay_config->oid_partner,
            'sign_type' => $this->llpay_config->sign_type,
//            'api_version' => '1.0',
            'repayment_plan' => json_encode($parameter['repayment_plan']),
            'repayment_no' => $parameter['repayment_no'],
            'sms_param' => json_encode($parameter['sms_param']),
            'pay_type' => 'D',
            'no_agree' => $parameter['no_agree'],
        ];

        $sortPara = self::buildRequestPara($data);

        // 返回数据
        $retJson = $this->buildRequestJSON($sortPara, self::WAP_AUTHORIZATION_URL);
        return json_decode($retJson, true);
    }

    /**
     * 还款计划变更接口
     * @param array $parameter
     * <pre>
     * [
     *  'user_id' => '',            // 用户id
     *  'repayment_plan' => [],     // 还款计划
     *  'repayment_no' => '',       // 还款编号
     *  'repayment_state' => '',    // 填 terminal 终止还款，或不填
     *  'sms_param' => [],          // 短信参数
     * ]
     * </pre>
     *
     * 风控参数
     * <pre>
     * [
     *  [
     *      'user_info_full_name' => '',    // 用户注册姓名
     *      'user_info_id_type' => '',      // 用户注册证件类型  0: 身份证或企业经营证件
     *      'user_info_id_no' => '',        // 用户注册证件号码
     *      'user_info_identify_state' => '',   // 是否实名认证 1：是，0：无认证
     *      'user_info_identify_type' => '',    // 认证方式,实名认证时必填 1:银行卡认证，2：现场认证，3：身份证远程认证，4：其它认证
     *  ]
     * ]
     * </pre>
     *
     * 短信参数
     * <pre>
     * [
     *  'contract_type' => '',  // 合同类型
     *  'contact_way' => ''     // 联系方式
     * ]
     * </pre>
     *
     * @return array
     * <pre>
     * [
     *  'ret_code' => '',       // 返回码 0000为成功
     *  'ret_msg' => '',
     *  'sign_type' => '',
     *  'sign' => ''
     * ]
     * </pre>
     */
    public function changeRepaymentPlan($parameter)
    {
        $data = [
            'oid_partner' => $this->llpay_config->oid_partner,
            'sign_type' => $this->llpay_config->sign_type,
            'user_id' => $parameter['user_id'],
            'repayment_plan' => json_encode($parameter['repayment_plan']),
            'repayment_no' => $parameter['repayment_no'],
            'repayment_state' => empty($parameter['repayment_state'])?'':$parameter['repayment_state'],
            'sms_param' => json_encode($parameter['sms_param']),
        ];

        $sortPara = self::buildRequestPara($data);

        // 返回数据
        $retJson = $this->buildRequestJSON($sortPara, self::CHANGE_REPAYMENT_PLAN_URL);
        return json_decode($retJson, true);
    }

    /**
     * 银行卡还款扣款接口
     *
     * 会有异步通知
     *
     * @param array $parameter
     * <pre>
     * [
     *  'user_id' => '',        // 用户在平台唯一标识
     *  'busi_partner' => '',   // 商户业务类型,虚拟：101001，实物：109001
     *  'no_order' => '',       // 商户唯一订单号
     *  'dt_order' => '',       // 商户订单时间 'YYYYMMDDH24MISS', 14位数字
     *  'name_goods' => '',     // 商品名称
     *  'money_order' => '',    // 订单总金额
     *  'notify_url' => '',     // 异步通知地址
     *  'risk_item' => [],      // 风险控制参数
     *  'schedule_repayment_date' => '',    // 还款日期
     *  'repayment_no' => '',   // 还款计划编号
     *  'no_agree' => '',       // 签约协议号
     * ]
     * </pre>
     *
     * 风控参数
     * <pre>
     * [
     *  [
     *      'user_info_full_name' => '',    // 用户注册姓名
     *      'user_info_id_type' => '',      // 用户注册证件类型  0: 身份证或企业经营证件
     *      'user_info_id_no' => '',        // 用户注册证件号码
     *      'user_info_identify_state' => '',   // 是否实名认证 1：是，0：无认证
     *      'user_info_identify_type' => '',    // 认证方式,实名认证时必填 1:银行卡认证，2：现场认证，3：身份证远程认证，4：其它认证
     *  ]
     * ]
     * </pre>
     *
     * 短信参数
     * <pre>
     * [
     *  'contract_type' => '',  // 合同类型
     *  'contact_way' => ''     // 联系方式
     * ]
     * </pre>
     *
     * @return mixed
     * <pre>
     * [
     *  'ret_code' => '',   // 0000 为成功
     *  'ret_msg' => '',    // 描述
     *  'sign_type' => '',  // 签名类型
     *  'sign' => '',       // 签名
     *  'oid_partner' => '',    // 商户签名
     *  'no_order' => '',   // 商户订单号
     *  'dt_order' => '',   // 订单时间
     *  'money_order' => '',    // 交易金额
     *  'oid_paybill' => '',    // 连连支付单号
     *  'info_order' => '',     // 订单描述
     * ]
     * </pre>
     *
     *
     * 回调数据：
     *
     * 成功才通知，失败和异常不通知，商户通过订单结果来查询订单状态 一共通知30此，频率为2分钟1次。
     *
     * 所以有异步回调的数据格式为：
     * <pre>
     * [
     *  'oid_partner' => '',    // 商户id
     *  'sign_type' => '',      // 签名方式
     *  'sign' => '',           // 签名
     *  'dt_order' => '',       // 订单时间
     *  'no_order' => '',       // 商户订单号
     *  'oid_paybill' => '',    // 连连支付订单号
     *  'money_order' => '',    // 交易金额，单位为元
     *  'result_pay' => '',     // 支付结果 SUCCESS 为成功
     *  'settle_date' => '',    // 清算日期
     *  'info_order' => '',     // 订单描述
     *  'pay_type' => '',       // 支付方式 D:认证支付（借记卡）
     *  'bank_code' => '',      // 银行编号
     *  'no_agree' => '',       // 协议编号
     *  'id_type' => '',        // 身份类型 0：身份证
     *  'id_no' => '',          // 身份证号
     *  'acct_name' => '',      // 银行账户名称
     *  'card_no' => '',        // 银行卡号，显示 显示前 6 后 4 隐藏其它, 例: 622208*********0000
     * ]
     * </pre>
     *
     * 接到通知后，需要返回如下格式
     * <pre>
     * [
     *  'ret_code' => '0000',
     *  'ret_msg' => '交易成功'
     * ]
     * </pre>
     */
    public function bankCardRepaymentDebit($parameter)
    {
        $data = [
            'user_id' => $parameter['user_id'],
            'oid_partner' => $this->llpay_config->oid_partner,
            'sign_type' => $this->llpay_config->sign_type,
            'busi_partner' => $parameter['busi_partner'],
            'api_version' => '1.0',
            'no_order' => $parameter['no_order'],
            'dt_order' => $parameter['dt_order'],
            'name_goods' => $parameter['name_goods'],
            'money_order' => $parameter['money_order'],
            'notify_url' => $parameter['notify_url'],
            'risk_item' => json_encode($parameter['risk_item']),
            'schedule_repayment_date' => $parameter['schedule_repayment_date'],
            'repayment_no' => $parameter['repayment_no'],
            'pay_type' => 'D',
            'no_agree' => $parameter['no_agree'],
        ];
        $sortPara = self::buildRequestPara($data);

        // 返回数据
        $retJson = $this->buildRequestJSON($sortPara, self::BANK_CARD_REPAYMENT_DEBIT_URL);
        return json_decode($retJson, true);
    }

    /**
     * 银行卡BIN查询接口
     */
    public function bankCardBINQuery()
    {
        // todo
    }

    /**
     * 用户签约信息查询接口
     * @param array $parameter
     * <pre>
     * [
     *  'user_id' => '',    // 用户在平台的唯一标识，必填
     *  'offset' => '0',    // 查询偏移量，从零开始，选填
     *  'card_no' => '',    // 签约银行卡号，选填
     *  'no_agree' => '',   // 签约协议号，选填
     * ]
     * </pre>
     * @return array    用户签约信息
     * <pre>
     * [
     *  'ret_code' => '',   // 0000 代表成功
     *  'ret_msg' => '',    // 交易结果
     *  'user_id' => '',    // 用户id，唯一标识
     *  'count' => '',      // 返回总记录数,
     *  'agreement_list' => [], // 签约结果集
     *  'sign_type' => '',      // 签名类型
     *  'sign' => '',           // 签名
     * ]
     * </pre>
     *
     * 'agreement_list'结构
     * <pre>
     * [
     *  'no_agree' => '',   // 银行卡签约唯一编号
     *  'card_no' => '',    // 银行卡后4位
     *  'bank_name' => '',  // 银行卡名称
     *  'bank_code' => '',  // 银行编号
     *  'card_type' => '',  // 银行卡类型
     *  'bind_mobile' => '',    // 手机号码
     * ]
     * </pre>
     *
     */
    public function userSignedInfoQuery($parameter)
    {
        $data = [
            'oid_partner' => $this->llpay_config->oid_partner,
            'user_id' => $parameter['user_id'],
            'pay_type' => 'D',
            'sign_type' => $this->llpay_config->sign_type,
            'offset' => empty($parameter['offset'])?'0':$parameter['offset'],
            'card_no' => empty($parameter['card_no'])?'':$parameter['card_no'],
            'no_agree' => empty($parameter['no_agree'])?'':$parameter['no_agree'],
        ];
        $sortPara = self::buildRequestPara($data);

        // 返回数据
        $retJson = $this->buildRequestJSON($sortPara, self::USER_SIGNED_INFO_QUERY_URL);
        return json_decode($retJson, true);
    }

    /**
     * 商户支付结果查询接口
     * @param array $parameter
     * <pre>
     * [
     *  'no_order' => '',   // 商户订单号,必填
     *  'dt_order' => '',   // 商户订单时间，必填
     *  'oid_paybill' => '',    // 连连订单号，选填
     * ]
     * </pre>
     * @return array
     * <pre>
     * [
     *  'ret_code' => '',
     *  'ret_msg' => '',
     *  'sign_type' => '',
     *  'sign' => '',
     *  'result_pay' => '',     // SUCCESS 成功, WAITING 等待支付, PROCESSING 银行支付处理中, REFUND 退款, FAILURE 失败
     *  'oid_partner' => '',    // 商户id
     *  'dt_order' => '',       // 商户订单时间
     *  'no_order' => '',       // 商户订单号
     *  'oid_paybill' => '',    // 连连订单号
     *  'money_order' => '',    // 交易金额
     *  'memo' => '',       // 支付备注
     *  'card_no' => '',    // 银行卡号，显示前6后4，例: 622208*********0000
     *  'settle_date' => '',    // 清算日期 YYYYMMDD
     * ]
     * </pre>
     */
    public function merchantPayResultQuery($parameter)
    {
        $data = [
            'oid_partner' => $this->llpay_config->oid_partner,
            'sign_type' => $this->llpay_config->sign_type,
            'no_order' => $parameter['no_order'],
            'dt_order' => $parameter['dt_order'],
            'oid_paybill' => empty($parameter['oid_paybill'])?'':$parameter['oid_paybill'],
            'query_version' => '1.1',
        ];
        $sortPara = self::buildRequestPara($data);

        // 返回数据
        $retJson = $this->buildRequestJSON($sortPara, self::MERCHANT_PAY_RESULT_QUERY_URL);
        return json_decode($retJson, true);
    }
}