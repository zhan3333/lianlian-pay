<?php
/**
 * Created by PhpStorm.
 * User: zhan
 * Date: 2017/3/7
 * Time: 8:52
 */

namespace LLPay;

class Util
{
    /**
     * 生成内部用订单号
     */
    public static function generateOrderNum()
    {
        $id = dk_get_next_id();
        return $id;
    }

    /**
     * 加载方法类
     */
    public static function loadFunction()
    {
        require_once 'llpay_core.function.php';
        require_once 'llpay_md5.function.php';
        require_once 'llpay_rsa.function.php';
        require_once 'llpay_security.function.php';
    }

    /**
     * 获取支付时间
     *
     * @return string
     */
    public static function getPayTime()
    {
        return date('YmdHis');
    }
}