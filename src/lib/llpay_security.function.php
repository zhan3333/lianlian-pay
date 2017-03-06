<?php

function aesEncrypt($data,$key,$nonce){
	return base64_encode( openssl_encrypt ($data, "AES-256-CTR", $key, true, $nonce . "\0\0\0\0\0\0\0\1"));
}
function aesDecrypt($data,$key,$nonce){
	return openssl_decrypt (base64_decode($data), "AES-256-CTR", $key, true, $nonce . "\0\0\0\0\0\0\0\1");
}
function genLetterDigitRandom($size) {
	$allLetterDigit = array("0","1","2","3","4","5","6","7","8","9","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
	$randomSb = "";
	$digitSize = count($allLetterDigit)-1;
	for($i = 0; $i < $size; $i ++){
		$randomSb .= $allLetterDigit[rand(0,$digitSize)];
	}
	return $randomSb;
}
function rsaEncrypt($data,$public_key){
	openssl_public_encrypt ( $data, $encrypted, $public_key,OPENSSL_PKCS1_OAEP_PADDING ); // 公钥加密
	return base64_encode ( $encrypted );
}
function rsaDecrypt($data,$private_key){
	openssl_private_decrypt(base64_decode ($data), $decrypted, $private_key,OPENSSL_PKCS1_OAEP_PADDING ); // 私钥解密
	return $decrypted;
}
function lianlianpayEncrypt($req, $public_key, $hmack_key, $version, $aes_key, $nonce) {
	$B64hmack_key = rsaEncrypt ( $hmack_key, $public_key );
	$B64aes_key = rsaEncrypt ( $aes_key, $public_key );
	$B64nonce = base64_encode($nonce);
	$encry = aesEncrypt ( utf8_decode($req), $aes_key, $nonce);
	$message = $B64nonce . "$" .$encry;
	$sign = hex2bin(hash_hmac("sha256",$message,$hmack_key));
	$B64sign = base64_encode($sign);
	return $version . '$' . $B64hmack_key . '$' . $B64aes_key . '$' . $B64nonce . '$' . $encry . '$' . $B64sign;
}
function ll_encrypt($plaintext, $public_key) {
	$pu_key = openssl_pkey_get_public ( $public_key );
	$hmack_key = genLetterDigitRandom(32);
	$version = "lianpay1_0_1";
	$aes_key = genLetterDigitRandom(32);
	$nonce = genLetterDigitRandom(8);
	return lianlianpayEncrypt($plaintext, $pu_key, $hmack_key, $version, $aes_key, $nonce);
}
?>