﻿
            ╭───────────────────────╮
    ────┤           连连支付WEB接口代码示例结构说明             ├────
            ╰───────────────────────╯ 
　                                                                  
　       接口名称：连连支付实时付款接口 demo参考连连支付开发平台的支付接口demo改造
　 　    代码版本：1.0
         开发语言：PHP
         版    权：连连银通电子支付有限公司
　       制 作 者：kristain

         备注:实时付款API php的demo要参考支付web接口，实时付款没有具体php版的demo,具体需要细看文档，因调用实时付款API加密算法php没对应的算法，需要调用
              java class（lianlianpay-security-1.0.0.jar）

(商户测试期间需要用正式的数据测试，测试时默认单笔单日单月额度50，等测试OK，和连连技术核对过业务对接逻辑后，申请走上线流程打开额度）
 * 对于返回码4002和4004的疑似订单（文档中有说明），不能系统直接调用确认接口，要人工审核后才能调用
 * 实时付款对接：
 * // 出现异常时要调用订单查询接口，明确订单状态，不能私自设置订单为失败状态，以免造成这笔订单在连连付款成功了，而商户设置为失败
 * 步骤： 1.先对参数设置
 *        2.对参数加签   商户私钥加签
 *        3.对参数加密ll_encrypt（查询接口不需要加密） 银通公钥加密
 *        4.对响应内容sign不为空时验签   验签用的是银通公钥
 *        5.根据返回code码处理逻辑（详细看文档说明，特别关注红色字和异常机制）
    ─────────────────────────────────

───────
 代码文件结构
───────

llpay
  │
  ├lib┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈类文件夹
  │  │llpay_security.function.php -------- 连连加密文件
  │  ├llpay_core.function.php ┈┈┈┈┈┈连连支付接口公用函数文件
  │  │
  │  ├llpay_notify.class.php┈┈┈┈┈┈┈连连支付通知处理类文件
  │  │
  │  ├llpay_submit.class.php┈┈┈┈┈┈┈连连支付各接口请求提交类文件
  │  │
  │  └llpay_md5.function.php┈┈┈┈┈┈┈连连支付接口MD5函数文件
  │  │
  │  └llpay_cls_json.function.php┈ 连连支付接口JSON函数文件
  │
  ├log.txt┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈日志文件
  │
  ├llpay.config.php┈┈┈┈┈┈┈┈┈┈┈┈基础配置类文件
  │
  ├llpayapi.php┈┈┈┈┈┈┈┈┈┈┈┈┈┈连连支付接口入口文件
  │
  ├notify_url.php ┈┈┈┈┈┈┈┈┈┈┈┈┈服务器异步通知页面文件
  │
  ├return_url.php ┈┈┈┈┈┈┈┈┈┈┈┈┈页面跳转同步通知文件
  │
  └readme.txt ┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈使用说明文本

※注意※

1、必须开启curl服务
（1）使用Crul需要修改服务器中php.ini文件的设置，找到php_curl.dll去掉前面的";"即可

2、需要配置的文件是：
llpay.config.php
llpayapi.php

●本代码示例（DEMO）采用fsockopen()的方法远程HTTP获取数据、采用DOMDocument()的方法解析XML数据。

请根据商户网站自身情况来决定是否使用代码示例中的方式——
如果不使用fsockopen，那么建议用curl来代替；
如果环境不是PHP5版本或其以上，那么请用其他方法代替DOMDocument()。

curl、XML解析方法需您自行编写代码。


─────────
 类文件函数结构
─────────

llpay_core.function.php

function createLinkstring($para)
功能：把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
输入：Array  $para 需要拼接的数组
输出：String 拼接完成以后的字符串

function createLinkstringUrlencode($para)
功能：把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对参数值urlencode
输入：Array  $para 需要拼接的数组
输出：String 拼接完成以后的字符串

function paraFilter($para)
功能：除去数组中的空值和签名参数
输入：Array  $para 签名参数组
输出：Array  去掉空值与签名参数后的新签名参数组

function argSort($para)
功能：对数组排序
输入：Array  $para 排序前的数组
输出：Array  排序后的数组

function logResult($word='')
功能：写日志，方便测试（看网站需求，也可以改成存入数据库）
输入：String $word 要写入日志里的文本内容 默认值：空值

function getHttpResponsePOST($url, $cacert_url, $para, $input_charset = '')
功能：远程获取数据，POST模式
输入：String $url 指定URL完整路径地址
      String $cacert_url 指定当前工作目录绝对路径
      Array  $para 请求的数据
      String $input_charset 编码格式。默认值：空值
输出：String 远程输出的数据

function getHttpResponseGET($url, $cacert_url)
功能：远程获取数据，GET模式
输入：String $url 指定URL完整路径地址
      String $cacert_url 指定当前工作目录绝对路径
输出：String 远程输出的数据

function charsetEncode($input,$_output_charset ,$_input_charset)
功能：实现多种字符编码方式
输入：String $input 需要编码的字符串
      String $_output_charset 输出的编码格式
      String $_input_charset 输入的编码格式
输出：String 编码后的字符串

function charsetDecode($input,$_input_charset ,$_output_charset) 
功能：实现多种字符解码方式
输入：String $input 需要解码的字符串
      String $_output_charset 输出的解码格式
      String $_input_charset 输入的解码格式
输出：String 解码后的字符串

┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉

function md5Sign($prestr, $key)
功能：MD5签名
输入：String $prestr 待签名数据
      String $key 私钥
输出：String 签名结果

function md5Verify($prestr, $sign, $key)
功能：MD5验签
输入：String $data 待签名数据
      String $sign 签名结果
      String $key 私钥
输出：bool 验证结果
┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉

llpay_notify.class.php

function verifyNotify()
功能：对notify_url的认证
输出：Bool  验证结果：true/false

function verifyReturn()
功能：对return_url的认证
输出：Bool  验证结果：true/false

function getSignVeryfy($para_temp, $sign)
功能：获取返回时的签名验证结果
输入：Array $para_temp 通知返回来的参数数组
      String $sign 连连支付返回的签名结果
输出：Bool 获得签名验证结果

function getResponse($notify_id)
功能：获取远程服务器ATN结果,验证返回URL
输入：String $notify_id 通知校验ID
输出：String 服务器ATN结果

┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉

llpay_submit.class.php

function buildRequestMysign($para_sort)
功能：生成要请求给连连支付的参数数组
输入：Array $para_sort 已排序要签名的数组
输出：String 签名结果

function buildRequestPara($para_temp)
功能：根据反馈回来的信息，生成签名结果
输入：Array $para_temp 请求前的参数数组
输出：String 要请求的参数数组

function buildRequestParaToString($para_temp)
功能：根据反馈回来的信息，生成签名结果
输入：Array $para_temp 请求前的参数数组
输出：String 要请求的参数数组字符串

function buildRequestForm($para_temp, $method, $button_name)
功能：建立请求，以表单HTML形式构造（默认）
输入：Array $para_temp 请求前的参数数组
      String $method 提交方式。两个值可选：post、get
      String $button_name 确认按钮显示文字
输出：String 提交表单HTML文本

function buildRequestHttp($para_temp)
功能：建立请求，以模拟远程HTTP的POST请求方式构造并获取连连支付的处理结果
输入：Array $para_temp 请求前的参数数组
输出：String 连连支付处理结果

function buildRequestHttpInFile($para_temp, $file_para_name, $file_name)
功能：建立请求，以模拟远程HTTP的POST请求方式构造并获取连连支付的处理结果，带文件上传功能
输入：Array $para_temp 请求参数数组
      String $file_para_name 文件类型的参数名
      String $file_name 文件完整绝对路径
输出：String 连连支付返回处理结果

function query_timestamp() 
功能：用于防钓鱼，调用接口query_timestamp来获取时间戳的处理函数
输出：String 时间戳字符串

┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉┉





