<?php
header("Content-type:text/html;charset=utf-8");
//date_default_timezone_set('PRC');

$transId='TEST00000000002';//"TX".date('Ymd',time()).substr(time(),'-6');
$accountNumber="43578";//一麻袋商户号
$cardNo="6222021604000969400";
$amount="0.01";


$private_key = '-----BEGIN RSA PRIVATE KEY-----
MIICdgIBADANBgkqhkiG9w0BAQEFAASCAmAwggJcAgEAAoGBAIce5u6eDfwpyaiFWLVs2BtOPDo0
LAtV6/KTzMP2kjueaDQzDdv7w4NRqZICABDX/o8mJ6s4tor1gIkuXO5ut28twBDbjJ3fWcm/Ga5L
ztCbu7OjJ93BlWkEJkCIzd2ibf/Tqq5qHVXF/sxFuRhIT3OkzZamyDPmEaNAZbZDdof/AgMBAAEC
gYAwgbh3ewgcOUgqlkxFPSDLlKdsYaRaIWtFtydwRgkzG+ferWFRUq1abCuKvesWIORCsXjWL9Lg
Scft91XnRpnU6y9X0B0A4yPnnKFd+7KB2J/+4XqWWh9brU3RUstBH+uuY8Bw50uL2s0tqnqOX+e0
0FhY37u7jZG2T97IED5KuQJBAL4XAVe3ks7prDY3e7PbwTL4ESbUVx7UKoXVkeKWVFl//KVf36Ip
KnW75AbzuKP0p9mAla9lz7OcsKkbViULO9MCQQC1+Kfuw06XQdoALGk6GDEHyx2LwCdoSzU8vXyO
jht4g9TEpjlHBMAZHZGq+zQ+PAnIhjxuRpN+eCtzCJxYdIOlAkAZ4ZH6OnFPoLskypsaGvKMGQBk
1AZkmSiM/k4Vlrg3U1i3v3z4XDh+vS1H0QkzsYzk7T/0GJ2V6+CVtbYd5xCnAkEAj7nlUfVa9qch
e2+YcTU4TLKGFKJhvcNhOidj4OinE+n0PJoZtVkwLOYo7sZYfitHguVbh7IgvwxFLSeI7WihrQJA
D1JTJeHfHDdeStoaFlVl8/8A0jAN2Y6j8fkJWrpxOOFh+xDFjmJr2sZ7/iHqLl029yQ9ULqWXQXM
cwCNDECaTg==
-----END RSA PRIVATE KEY-----';

  // $MD5key = "yuanjitituan";
$url="transId=".$transId."&accountNumber=".$accountNumber."&cardNo=".$cardNo."&amount=".$amount;

//$encrypted = "yuanjitituan";   
$key = openssl_pkey_get_private($private_key);
openssl_sign($url, $sign, $key, OPENSSL_ALGO_SHA1);

echo  $sign =  ($sign);

 
$MENU_URL="https://gwapi.yemadai.com/transfer/transferFixed";

echo $data=<<<EOF
 <?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<yemadai>
	<accountNumber>$accountNumber</accountNumber>
	<notifyURL>http://notify.merchant.com</notifyURL>
	<tt>0</tt>
	<signType>RSA</signType>
	<transferList>
		<transId>$transId</transId>
		<bankCode>工商银行</bankCode>
		<provice>山东省</provice>		 
		<city>泰安市</city>		 
		<branchName>泰安市</branchName>
		<accountName>华凯华</accountName>
		<cardNo>$cardNo</cardNo>
		<amount>$amount</amount>
		<remark>测试转账</remark>
		<secureCode>$sign</secureCode>
	</transferList>	 
</yemadai>
EOF;
$data=base64_encode($data);
$ch = curl_init();
/*使用cURL完成简单的请求主要分为以下四步：
1.初始化，创建一个新cURL资源  curl_init初始化一个cURL会话
2.设置URL和相应的选项    curl_setopt//设置curl参数
3.执行抓取URL并把它传递给浏览器
        curl_exec 执行一个cURL会话 
4.关闭cURL资源，并且释放系统资源
//     curl_close关闭curl会话
*/

curl_setopt($ch, CURLOPT_URL, $MENU_URL); 
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
curl_setopt($ch, CURLOPT_AUTOREFERER, 1); 
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
$info = curl_exec($ch);
if (curl_errno($ch)) {
    echo 'Errno'.curl_error($ch);
}
curl_close($ch);
var_dump(base64_decode($info));



?>
 