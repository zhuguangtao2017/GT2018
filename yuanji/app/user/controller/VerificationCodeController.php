<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Dean <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace app\user\controller;

use cmf\controller\HomeBaseController;
use think\Validate;

use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;

// 加载区域结点配置
Config::load();

class VerificationCodeController extends HomeBaseController
{
    public function send()
    {
        $validate = new Validate([
            'username' => 'require',
        ]);

        $validate->message([
            'username.require' => '请输入手机号或邮箱!',
        ]);

        $data = $this->request->param();
        if (!$validate->check($data)) {
            $this->error($validate->getError());
        }

        $accountType = '';

        if (Validate::is($data['username'], 'email')) {
            $accountType = 'email';
        } else if (preg_match('/(^(13\d|15[^4\D]|17[013678]|18\d)\d{8})$/', $data['username'])) {
            $accountType = 'mobile';
        } else {
            $this->error("请输入正确的手机或者邮箱格式!");
        }

        //TODO 限制 每个ip 的发送次数

        $code = cmf_get_verification_code($data['username']);
        if (empty($code)) {
            $this->error("验证码发送过多,请明天再试!");
        }

        if ($accountType == 'email') {

            $emailTemplate = cmf_get_option('email_template_verification_code');

            $user     = cmf_get_current_user();
            $username = empty($user['user_nickname']) ? $user['user_login'] : $user['user_nickname'];

            $message = htmlspecialchars_decode($emailTemplate['template']);
            $message = $this->display($message, ['code' => $code, 'username' => $username]);
            $subject = empty($emailTemplate['subject']) ? 'ThinkCMF验证码' : $emailTemplate['subject'];
            $result  = cmf_send_email($data['username'], $subject, $message);

            if (empty($result['error'])) {
                cmf_verification_code_log($data['username'], $code);
                $this->success("验证码已经发送成功!");
            } else {
                $this->error("邮箱验证码发送失败:" . $result['message']);
            }

        } else if ($accountType == 'mobile') {

          // $param  = ['mobile' => $data['username'], 'code' => $code];
         //  $result = hook_one("send_mobile_verification_code", $param);
		    $result=$this->sendsms($data['username'],$code);  
			
            cmf_verification_code_log($data['username'], $code);  
			
            if ($result->Message=='OK') {
                $this->success('验证码已经发送成功!');
            } else {
                $this->success('验证码发送失败!');
            }

        }


    }

	public function sendsms($tel,$code){
		 //此处需要替换成自己的AK信息

		$accessKeyId = "LTAIFTDZ5KhleEz7";//参考本文档步骤2
		$accessKeySecret = "5oaoH4apSI8fFSGZc4323cp00WzDd6";//参考本文档步骤2
		//短信API产品名（短信产品名固定，无需修改）
		$product = "Dysmsapi";
		//短信API产品域名（接口地址固定，无需修改）
		$domain = "dysmsapi.aliyuncs.com";
		//暂时不支持多Region（目前仅支持cn-hangzhou请勿修改）
		$region = "cn-hangzhou";
		//初始化访问的acsCleint
		$profile = DefaultProfile::getProfile($region, $accessKeyId, $accessKeySecret);
		DefaultProfile::addEndpoint("cn-hangzhou", "cn-hangzhou", $product, $domain);
		$acsClient= new DefaultAcsClient($profile);
		$request =  new SendSmsRequest();
		header('Content-Type: text/plain; charset=utf-8');
		//必填-短信接收号码。支持以逗号分隔的形式进行批量调用，批量上限为1000个手机号码,批量调用相对于单条调用及时性稍有延迟,验证码类型的短信推荐使用单条调用的方式
		$request->setPhoneNumbers($tel);
		//必填-短信签名
		$request->setSignName("元基");
		//必填-短信模板Code
		$request->setTemplateCode("SMS_105720070");
		//选填-假如模板中存在变量需要替换则为必填(JSON格式),友情提示:如果JSON中需要带换行符,请参照标准的JSON协议对换行符的要求,比如短信内容中包含\r\n的情况在JSON中需要表示成\\r\\n,否则会导致JSON在服务端解析失败
		$request->setTemplateParam("{\"code\":\"$code\",\"product\":\"云通信服务\"}");
		//选填-发送短信流水号
		//$request->setOutId("1234522");
		//发起访问请求
	  return $acsResponse = $acsClient->getAcsResponse($request);
	 
 
	
	
	}

	 

}
