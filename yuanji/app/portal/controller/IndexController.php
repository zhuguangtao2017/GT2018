<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <thinkcmf@126.com>
// +----------------------------------------------------------------------
namespace app\portal\controller;

use cmf\controller\HomeBaseController;
use think\Request;
 
use think\Db;
class IndexController extends HomeBaseController
{
    public function index()
    {   
		$about=Db::name('portal_post')->where(['id'=>7])->find();
		$contact=Db::name('portal_post')->where(['id'=>5])->find();
		$rs_wenhua=Db::name('portal_post')->where(['id'=>21])->find();
		//print_r($about);
	 
        return $this->fetch(':index',['about'=>$about,'contact'=>$contact,'rs_wenhua'=>$rs_wenhua]);
    }

	public function chong(){
		$MerNo="43578";
		//MD5私钥
		$MD5key = "yuanjitituan";
		//订单号
		$BillNo = $this->request->param('BillNo'); 
		//一麻袋支付订单号
		$OrderNo= $this->request->param('OrderNo'); 
		//金额
		$Amount = $this->request->param('Amount');  
		//支付状态
		$Succeed = $this->request->param('Succeed');   
		//支付结果
		$Result = $this->request->param('Result');   
		//取得的MD5校验信息
		$SignInfo = $this->request->param('SignInfo');  
		//备注
		$Remark = $this->request->param('Remark'); 
    	//校验源字符串
         $md5src = "MerNo=".$MerNo."&BillNo=".$BillNo."&OrderNo=".$OrderNo."&Amount=".$Amount."&Succeed=".$Succeed."&".$MD5key;
       //MD5检验结果
	     $md5sign = strtoupper(md5($md5src));

		  if ($SignInfo!=$md5sign){                
				 die();
		  }

		  if($Succeed=='88' && $Result=='SUCCESS'){
		  //  $uid=cmf_get_current_user_id();
	       // $data = $this->request->param;
			//$money=$data['money'];
			//$fangshi=input('interface');
			//$uid = $this->request->param('uid'); 
			$uid= $this->request->param('uid', 0, 'intval');

			$fangshi=4;
			if($fangshi==1)
				{
					 $zhanghaos=input('hao');//支付账号
					 $xixi='支付宝';
					 $fspan=1;
				}else if($fangshi==2)
				{
					$zhanghaos=input('hao');//支付账号
					 $xixi='微信';
					  $fspan=2;
				}
				else if($fangshi==4)
				{					
					$xixi='银行卡';
					$fspan=4;
				}
		$user = ['uid'=>$uid,'money'=>$Amount,'from'=>'','time'=>time(),'type'=>'3','fs'=>$xixi,'fspan'=>$fspan];
		$aa=Db::name('user_money_detail')->insert($user);
		 if($aa){echo 'ok';}else{echo 'fail'; }
		}
	
	}
	 
	 public function getCategorySelect($cityid=5,$select_id=0,$id = 0,$level = 0,$level_nbsp='',$str=''){	 
	 
		$category_arr=Db::name('user')->where(['pid'=>5])->where('fid',$id)->select();
	
		for($lev = 0; $lev < $level * 2 - 1; $lev ++) {
			$level_nbsp .= "&nbsp;";
		}
		if ($level++)
			$level_nbsp .= "┝";
		foreach ( $category_arr as $category ) {
		    $id = $category ['id'];
			$fid = $category ['fid'];
			$name = !empty($category ['user_nickname'])?$category ['user_nickname']:$category ['mobile'];
			$selected = $select_id==$id?'selected':'';
			echo  "<option value=\"".$id."\" ".$selected.">".$level_nbsp . " " . $name."</option>\n";
			$this->getCategorySelect ($cityid,$select_id, $id, $level,$level_nbsp,$str);
		}
		 
  }
}
