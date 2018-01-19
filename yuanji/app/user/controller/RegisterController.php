<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: Powerless < wzxaini9@gmail.com>
// +----------------------------------------------------------------------
namespace app\user\controller;

use cmf\controller\HomeBaseController;
use think\Validate;
use app\user\model\UserModel;
use think\Db;
class RegisterController extends HomeBaseController
{

    /**
     * 前台用户注册
     */
    public function index()
    {
        //邀请码fid
        $fid =input('id');
		$rs=Db::name('user')->where('id',$fid)->find();
		$pid = $rs['pid'];
		if($pid){
		  $sheng=Db::name('province')->where('id',$pid)->select();
		}else{
         $sheng=Db::name('province')->select();
		}
        $redirect = $this->request->post("redirect");
        if (empty($redirect)) {
            $redirect = $this->request->server('HTTP_REFERER');
        } else {
            $redirect = base64_decode($redirect);
        }
        session('login_http_referer', $redirect);

        if (cmf_is_user_login()) {
            return redirect($this->request->root() . '/');
        } else 
        {
            return $this->fetch(":register",['fid'=>$fid,'sheng'=>$sheng,'pid'=>$pid]);
        }
    }

    /**
     * 前台用户注册提交
     */
    public function doRegister()
    {
        if ($this->request->isPost()) {
            $rules = [
                'password' => 'require|min:6|max:20',
                'nc'       => 'require|min:2|max:16',
                'sex'      => 'require', 
                'captcha'  => 'require',
               // 'code'     => 'require',
               // 'ty'      => 'require', 
				'pid'      => 'require', 
               
                

            ];

            //$isOpenRegistration=cmf_is_open_registration();

           /* if ($isOpenRegistration) {
                unset($rules['code']);
            }
			*/

            $validate = new Validate($rules);
            $validate->message([
               // 'code.require'     => '短信验证码不能为空',
                'password.require' => '密码不能为空',
                'password.max'     => '密码不能超过20个字符',
                'password.min'     => '密码不能小于6个字符',
                'captcha.require'  => '验证码不能为空',
                'nc.require'  => '昵称不能为空',
				'password.max'     => '昵称不能超过16个字符',
				'password.min'     => '昵称最少2个字符',
                'sex.require'  => '性别不能为空',
                'pid.require'  => '请选择所属省份',
                //'ty'  => '请阅读并同意协议',

            ]);

            $data = $this->request->post();
           // dump($data);
            if (!$validate->check($data)) {
                $this->error($validate->getError());
            }
             

          /*  if(!$isOpenRegistration){
                $errMsg = cmf_check_verification_code($data['username'], $data['code']);
                if (!empty($errMsg)) {
                    $this->error($errMsg);
                }
            }
			*/

            $register          = new UserModel();
            $user['user_pass'] = $data['password'];
            $user['fid'] = $data['hid'];
            //$user['birthday'] = $data['birthday'];
            $user['user_nickname'] = $data['nc'];
            $user['sex'] = $data['sex'];
			$user['pid'] = $data['pid'];
            if (Validate::is($data['username'], 'email')) {
                $user['user_email'] = $data['username'];
                $user['fid'] = $data['fid'];
                $log                = $register->registerEmail($user);
            } else if (preg_match('/(^(13\d|15[^4\D]|17[013678]|18\d)\d{8})$/', $data['username'])) {
                $user['mobile'] = $data['username'];
                $user['fid'] = $data['fid'];
                $log            = $register->registerMobile($user);
            } else {
                $log = 2;
            }
            $sessionLoginHttpReferer = session('login_http_referer');
            $redirect                = empty($sessionLoginHttpReferer) ? cmf_get_root() . '/' : $sessionLoginHttpReferer;
            switch ($log) {
                case 0:
                    $this->success('注册成功');
                    break;
                case 1:
                    $this->error("您的账户已注册过");
                    break;
                case 2:
                    $this->error("您输入的账号格式错误");
                    break;
				case 3:
					$this->error("该省无代理");
					break;
                default :
                    $this->error('未受理的请求');
            }

        } 
        else
        {
            $this->error("请求错误");
        }

    }
}