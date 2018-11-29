<?php

include_once "wxBizMsgCrypt.php";




// 第三方发送消息给公众平台
$encodingAesKey = "epRl2Ic2nPcsq7PvMxEXLbt77GuAUwsPUhfdnONlbd1";
$token = "201705181525";
$timeStamp = "1409304348";
$nonce = "xxxxxx";
$appId = "wx915aee4509bc5cc8";
$from_xml = "<xml>
    <ToUserName><![CDATA[gh_6854d77f8ea5]]></ToUserName>
    <Encrypt><![CDATA[n2nuLzyUQcDPTgvHDBKml997vzG2WUG2qNZYrD6838cCfZkJDhUu2+F4Ketf1DK4NlMd8Dl4MEC6Oj68p9iHP/rYRinGmG8TWqt+eUlUGg9Fbl6Px+woqqYV/zmmn2eft6P5lMg47SsAitxRZ04yey8ShzjEZfukrhadxtcjsvz+QAfmX33fn/NU0g9j3GaGamfnNM7ZAkFyvMevEp2roWZeifuu6XU4Rrb7iE8O/Vr0oQ25ExyOn25cvfS8w/NCwgBFAZJAtiwV/wre5e83+u5a7rV8SRTDn1SvqjVEOoO07uNcDwFdWbU88hQBsIJ+RQ+UOXE7sDqAg4SmBnnlXULMtVWaum//mOATPYIQjv5k/Y5jcBI75JLVkZEz9wvjYf3uD0f+qMO3tB0GcOwfqrTA/F31qLxUz7+jyZpv7yE=]]></Encrypt>
</xml>";


$pc = new WXBizMsgCrypt($token, $encodingAesKey, $appId);
// $encryptMsg = '';
// $errCode = $pc->encryptMsg($text, $timeStamp, $nonce, $encryptMsg);
// if ($errCode == 0) {
// 	print("加密后: " . $encryptMsg . "\n");
// } else {
// 	print($errCode . "\n");
// }

// $xml_tree = new DOMDocument();
// $xml_tree->loadXML($encryptMsg);
// $array_e = $xml_tree->getElementsByTagName('Encrypt');
// $array_s = $xml_tree->getElementsByTagName('MsgSignature');
// $encrypt = $array_e->item(0)->nodeValue;
// $msg_sign = $array_s->item(0)->nodeValue;

// $format = "<xml><ToUserName><![CDATA[toUser]]></ToUserName><Encrypt><![CDATA[%s]]></Encrypt></xml>";
// $from_xml = sprintf($format, $encrypt);

// 第三方收到公众号平台发送的消息
$msg = '';
$msg_sign = 'bb7f4b9cf0950d4d00f30a17ca326e7e27789644';
$timeStamp  = '1540351272';
$nonce = '394858147';
$errCode = $pc->decryptMsg($msg_sign, $timeStamp, $nonce, $from_xml, $msg);
if ($errCode == 0) {
	print("解密后: " . $msg . "\n");
} else {
	print($errCode . "\n");
}
