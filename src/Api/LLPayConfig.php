<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2017/3/7
 * Time: 11:47
 */

namespace LLPay\Api;


class LLPayConfig
{
    //商户编号是商户在连连钱包支付平台上开设的商户号码，为18位数字，如：201306081000001016
    public $oid_partner;

    //秘钥格式注意不能修改（左对齐，右边有回车符）  商户私钥，通过openssl工具生成,私钥需要商户自己生成替换，对应的公钥通过商户站上传
    public $RSA_PRIVATE_KEY;

    //连连银通公钥
    public $LIANLIAN_PUBLICK_KEY;

    //安全检验码，以数字和字母组成的字符
    public $key;

    //签名方式 不需修改
    public $sign_type = 'RSA';


    public function __construct($config)
    {
        foreach ($config as $key => $value) {
            if (property_exists($this, $key)) {
                $this->{$key} = $value;
            }
        }
    }

}