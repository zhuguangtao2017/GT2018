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
use cmf\controller\AdminBaseController;
use app\portal\model\PortalPostModel;
use app\portal\service\PostService;
use app\portal\model\PortalCategoryModel;
use think\Db;
use app\admin\model\ThemeModel;

class AdminFqController extends HomeBaseController
{
    /**
     * 每日分红
     
     * )
     */
	 
    public function index()
    {
		
             //查出所有订单
               $arr1=Db::name('pay')->where('type',1)->select();
             //查出分红等级和倍数
               $data=Db::name('abonus')->select();	

			   date_default_timezone_set('Asia/Shanghai');		
 
			  $startday=strtotime(date('Y-m-d',time()));//当前天的开始
			 
			 
			   $endday=strtotime(date('Y-m-d',time()))+3600*24;////当前天的结束

			   $jibaifen=Db::name('baifen')->find();//加钱方式
				$arr=array();
				$j=-1;
				foreach($jibaifen as $va)
				{
					$arr[$j]=$va;
					$j++;
				}

               foreach ($arr1 as $key => $val) 
               {
                   $uid= $val['uid'];
				   $uuid=$uid;
                   $money=$val['money']; 
                   $fktime=$val['time'];
                   $dqtime=time();
					//判断商品
					//$bili=$this->bili($money);
					
                   foreach($data as $v){
					   
				       if($money==$v['price'])
					   {   //产品价格的倍数和分红
							$beishu=$v['multiple'];
							$fh=$v['abonus']/100;
							//$fenhong=$money*$beishu*$fh;
							$fenhong=$money*$fh;
					//九级
					
					for($i=0;$i<8;$i++){
						$fid=Db::name('user')->where(['id'=>$uid])->find();
									
						if(!empty($fid)&&$fid['fid']!='0')
						{
							
							$rsr=Db::name('user')->where(['id'=>$fid['fid']])->find();
							$uid=$fid['fid'];
							$chaoe=$this->selmoney($uid);//判断是否超额
							if($rsr['user_type']=='5'||$arr[$i]=='0'||$chaoe==false){
								continue;
							}
							
							//$arr[$i]=$rs;
							$moneys=$fenhong*($arr[$i]/100);//收益金额
							//insert into mingxi ('贡献人,当前人,收入金额') values ($uid,$fid,$moneys);

							$chaxun=Db::name("user_money_detail")->where("uid",$fid['fid'])->where("from",$uuid)->where('time','between',[$startday,$endday])->find();
							
							if(empty($chaxun)){
								$user = ['uid'=>$fid['fid'],'money'=>$moneys,'from'=>$uuid,'time'=>time(),'type'=>'1'];
								Db::name('user_money_detail')->insert($user);
							}
						}
						else
						{
							break;
						}
						
				} 
							  //查询用户下单时间到当前时间的收益和分红
							$summoney=Db::name('user_money_detail')			  ->where('uid',$uuid)
								      ->where('type','in','1,5')
									  ->where('time','between',[$fktime,$dqtime])					->sum('money');
						   $sy= $summoney;
						   $mm= $money*$beishu;
						   if($sy<$mm)
						   {
							 
							$num=Db::name('user_money_detail')
								 ->where('uid',$uuid)
								 ->where('time','between',[$startday,$endday])
								 ->where('type','5')->count();
							 if($num>0){
							    //die();							 
							 }else{
							 //分红插入明细表
							  $tim=strtotime(date('Y-m-d',$fktime));
								  if(time()-$tim>24*3600){
                                   // $fenhong
								  
								   $ticheng=Db::name('user_money_detail')->insert([
										'uid'=>$uuid,
										'money'=>$fenhong,
										'from'=>0, 
										'time'=>time(),
										'type'=>5,
									 ]); 
								 
								 }
							 }
						   } 
					   }				   				   
				   } 
               }
         //执行结束
         echo "suc";
         // return $this->fetch();
    }

	//判断是否超出收益
	public function selmoney($id)
	{
		$pay=Db::name("pay")->where("uid",$id)->where("type","1")->find();
		
		
		if(!empty($pay))
		{
			 $shouyi=Db::table("cmf_user_money_detail")->where("uid",$id)->where("type",['=',1],['=',5],'or')->where("time",">=",$pay['time'])->sum("money");
			 $abs=Db::name("abonus")->where("price",$pay['money'])->find();
			 //print_r($abs);
			  $moneyy=$abs['multiple']*$pay['money'];
		    if($shouyi>=$moneyy)
			{
				Db::name('pay')->where(['id'=>$pay['id']])->update(['type'=>2]);
				return false;
			}
			else
			{
				return true;
			}
		}else
		{
			return true;
		}

	}
  }