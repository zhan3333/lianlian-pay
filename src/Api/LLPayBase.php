<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2017/3/7
 * Time: 11:15
 */

namespace LLPay\Api;
use LLPay\Util;

/**
 * 连连支付基础类
 * Class LLpayBase
 * @package LLPay
 */
class LLPayBase
{
    /**
     * @var LLPayConfig
     */
    public $llpay_config;

    public function __construct($config)
    {
        Util::loadFunction();
        $this->llpay_config = $config;
    }

    /**
     * 生成签名结果
     * @param array $para_sort 已排序要签名的数组
     * return 签名结果字符串
     * @return string
     */
    public function buildRequestMysign($para_sort) {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = createLinkstring($para_sort);
        $mysign = "";
        switch (strtoupper(trim($this->llpay_config->sign_type))) {
            case "MD5" :
                $mysign = md5Sign($prestr, $this->llpay_config->key);
                break;
            case "RSA" :
                $mysign = RsaSign($prestr, $this->llpay_config->RSA_PRIVATE_KEY);
                break;
            default :
                $mysign = "";
        }
//        file_put_contents("log.txt","签名:".$mysign."\n", FILE_APPEND);
        return $mysign;
    }

    /**
     * 生成要请求给连连支付的参数数组
     * @param array $para_temp 请求前的参数数组
     * @return array   要请求的参数数组
     */
    public function buildRequestPara($para_temp) {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = paraFilter($para_temp);
        //对待签名参数数组排序
        $para_sort = argSort($para_filter);
        //生成签名结果
        $mysign = $this->buildRequestMysign($para_sort);
        //签名结果与签名方式加入请求提交参数组中
        $para_sort['sign'] = $mysign;
        $para_sort['sign_type'] = strtoupper(trim($this->llpay_config->sign_type));
        foreach ($para_sort as $key => $value) {
            $para_sort[$key] = $value;
        }
        return $para_sort;
        //return urldecode(json_encode($para_sort));
    }

    /**
     * 建立请求，以模拟远程HTTP的POST请求方式构造并获取连连支付的处理结果
     * @param array     $request_data   请求的参数数组
     * @param string    $llpay_payment_url    请求的链接地址
     * @return string   连连支付处理结果
     */
    public function buildRequestJSON($request_data,$llpay_payment_url) {
        $sResult = '';

        //待请求参数数组字符串
        //$request_data = $this->buildRequestPara($para_temp);

        //远程获取数据
        $sResult = getHttpResponseJSON($llpay_payment_url, $request_data);

        return $sResult;
    }
}