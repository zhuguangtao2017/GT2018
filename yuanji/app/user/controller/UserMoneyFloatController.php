<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/10/11
 * Time: 14:40
 */
 /** 
* 
*----------Dragon be here!----------/ 
* 　　 ┏┓　 ┏┓ 
* 　　┏┛┻━━━┛┻┓
* 　　┃　　　 ┃ 
* 　　┃ ━  ━  ┃ 
* 　　┃┳┛　┗┳ ┃ 
* 　　┃　　　 ┃ 
* 　　┃  ┻    ┃ 
* 　　┃　　   ┃ 
* 　　┗━┓　　┏┛ 
* 　　　┃　　┃神兽保佑 
* 　　　┃　　┃代码无BUG！ 
* 　　　┃　　┗━━━┓ 
* 　　　┃　　　　┣┓ 
* 　　　┃　　　 ┏┛ 
* 　　　┗┓┓┏━┳┓┏┛ 
* 　　　 ┃┫┫ ┃┫┫ 
* 　　　 ┗┻┛ ┗┻┛ 
* ━━━━━━神兽出没━━━━━━by:ZJH
*/  
namespace app\user\controller;
use think\Db;
use think\Request;
use app\user\model\UserMoneyDetailModel;
use cmf\controller\AdminBaseController;
use \PHPExcel; //包含 excel
use \PHPExcel_IOFactory; //包含 excel
use \PHPExcel_Style_Fill; //包含 excel

use Aliyun\Core\Config;
use Aliyun\Core\Profile\DefaultProfile;
use Aliyun\Core\DefaultAcsClient;
use Aliyun\Api\Sms\Request\V20170525\SendSmsRequest;
use Aliyun\Api\Sms\Request\V20170525\QuerySendDetailsRequest;

// 加载区域结点配置
Config::load();


class UserMoneyFloatController extends AdminBaseController{
	public function index(){
		$where   = [];
        $request = input('param.');
        $px = 'desc';
        if(input('px'))
        {
        	$px = input('px');
        }
		//dump($request);
        if (!empty($request['uid'])) {
            $where['d.uid'] = intval($request['uid']);
        }
		
        $keywordComplex = [];
        if (!empty($request['keyword'])) {
            $keyword = $request['keyword'];
            $keywordComplex['u.mobile|u.user_nickname|u.user_email']    = ['like', "%$keyword%"];
        }
		if(!empty($request['pid'])){
			$keywordComplex['u.pid'] = ['=',$request['pid']];
		}
		$time = date('Y-m-d',time());
		$date = explode('-',$time);
		$now_year = $date[0].'-01-01';
		$last_year = ($date[0]+1).'-01-01';
		$where['d.time']=[
			['>=',strtotime($now_year)],
			['<=',strtotime($last_year)],
			'and'
		];
		if(!empty($request['stime'])){
			$now_year='';$last_year='';
			$where['d.time']=['>',strtotime($request['stime'])];
		}
		if(!empty($request['etime'])){
			$now_year='';$last_year='';
			$where['d.time']=['<=',strtotime($request['etime'])+3600*24];
		}
		if(!empty($request['stime'])&&!empty($request['etime'])){
			$now_year='';$last_year='';
			$where['d.time']=[
				['>',strtotime($request['stime'])],
				['<=',strtotime($request['etime'])+3600*24],
				'and'
			];
		}
		
		$dWhere=[];
		if(!empty($request['pay_type'])){
			$dWhere['d.fspan']=['=',$request['pay_type']];
		}
		if(!empty($request['ids'])){
			//存在下级id
			if (!empty($request['uid'])) {
				//存在下级id,并且索引用户id时候
				if($request['uid']!=$request['this_id']){	
					if(in_array($request['uid'],explode(',',$request['ids']))){
						//echo '存在下级id,并且索引用户id时候,用户id不是this_id,此时索引id在ids,查消费';
						$where['d.uid'] = ['=',$request['uid']];
						$where['d.type']=['=',4];
					}else{
						//echo '存在下级id,并且索引用户id时候,用户id不是this_id,,并且索引id不在ids,不查';
						echo "<h4 style='color:#f00'>下级中无此用户</h4>";
						$arr = explode(',',$request['ids']);
						$a = $arr;
						array_push($arr,$request['this_id']);
						$where['d.uid'] = ['in',$arr];
						$where['d.type']=['in',[1,4]];
						$keywordComplex['u.mobile|u.user_nickname|u.user_email']='';
					}
				}else{
					//echo '存在下级id,并且索引用户id时候,用户id是this_id,只查收益';
					$where['d.uid'] = ['=',intval($request['uid'])];
					$where['d.type']=['=',1];
				}
			}
			else{
				//存在下级id,没有索引用户id
				//echo '哈哈哈';
				$arr = explode(',',$request['ids']);
				$a = $arr;
				array_push($arr,$request['this_id']);
				$where['d.uid'] = ['in',$arr];
				$where['d.type']=['in',[1,4]];
			}
		}
		if(!empty($request['type'])){
			$where['d.type']=['=',$request['type']];
		}
		$rb=false;
		if(!empty($request['export'])){		
			$detail = Db::name('user_money_detail')
			->alias('d')
			->field('d.*,u.id as user_id,u.user_nickname,u.mobile,u.user_email,u.user_type')
			->join('cmf_user u','u.id=d.uid')
			->whereOr($keywordComplex)
			->where($where)
			->where($dWhere)
			->select();
			//echo '<pre>';
			//print_r($detail);
			//echo '</pre>';
			$rb=true;
		}else{
			$detail = Db::name('user_money_detail')
			->alias('d')
			->field('d.*,u.id as user_id,u.user_nickname,u.mobile,u.user_email,u.user_type')
			->join('cmf_user u','u.id=d.uid')
			->whereOr($keywordComplex)
            ->where($where)
			->where($dWhere)
            ->order("time $px")
			->paginate(30,false,['query'=>request()->param()]);
			//echo Db::getLastSql();
			$page = $detail->render();
		}
		$details = [];
		if(!empty($request['ids'])){
			foreach($detail as $k=>$v){
				if( in_array($v['uid'],explode(',',$request['ids']))&&$v['type']==4 || $v['uid']==$request['this_id']&&$v['type']==1){
					$details[$k]=$v;
					$info = Db::name('user')
						->where('id',$v['from'])
						->field('user_nickname,user_email,mobile')
						->find();
					$details[$k]['froms']=$info;
				}
			}
		}else{
			foreach($detail as $k=>$v){
				$info = Db::name('user')
					->where('id',$v['from'])
					->field('user_nickname,user_email,mobile')
					->find();
				$details[$k]=$v;
				$details[$k]['froms']=$info;
			}
		}
		$type_arr=[];
		foreach($details as $v){
			//1收益  2提现  3充值，4 消费 5 分红
			//echo '<pre>';
			
			switch($v['type']){
				case '1':
					@$type_arr['sy'][]+=$v['money'];
					break;
				case '2':
					@$type_arr['tx'][]+=$v['money'];
					break;
				case '3':
					@$type_arr['cz'][]+=$v['money'];
					break;
				case '4':
					if($v['pan']==0){	//余额支付
						@$type_arr['xf_nei'][]+=$v['money'];
					}else{				//外部资金支付
						@$type_arr['xf_wai'][]+=$v['money'];
					}
					break;
				case '5':
					@$type_arr['fh'][]+=$v['money'];
					break;
			}
		}
		///*
		if($rb){
			//导出
			$objPHPExcel = new PHPExcel();
			$objPHPExcel->getProperties()->setTitle("资金流水");
			//添加数据
			$objPHPExcel->setActiveSheetIndex(0);//设置Sheet
			$objPHPExcel->getActiveSheet()->setCellValue('A1', '用户名');//可以指定位置
			$objPHPExcel->getActiveSheet()->setCellValue('B1', '级别');
			$objPHPExcel->getActiveSheet()->setCellValue('C1', '金额');
			$objPHPExcel->getActiveSheet()->setCellValue('D1', '资金类型');
			$objPHPExcel->getActiveSheet()->setCellValue('E1', '支付类型');
			$objPHPExcel->getActiveSheet()->setCellValue('F1', '时间');
			$objPHPExcel->getActiveSheet()->setCellValue('G1', '提现审核');
			//设置单元格宽度
			$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(20);
			$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
			$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(10);
			$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(310);
			$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(30);
			$k=2;
			//循环
			$user_type = [2=>'省代理商','地方代理','会员','注册会员'];
			$type = [1=>'收益','提现','充值','消费','每日分红','积分返现','手续费'];
			$allow = ['未审核','通过','未通过'];
			foreach($details as $key => $b) {
				$objPHPExcel->getActiveSheet()->setCellValue('A' . $k, $b['user_nickname']);
				$objPHPExcel->getActiveSheet()->setCellValue('B' . $k, $user_type[$b['user_type']]);
				$objPHPExcel->getActiveSheet()->setCellValue('C' . $k, $b['money'].'￥');
				$objPHPExcel->getActiveSheet()->setCellValue('D' . $k, $type[$b['type']]);
				$b['fs'] = isset($b['fs'])?$b['fs']:'余额';
				$objPHPExcel->getActiveSheet()->setCellValue('E' . $k, $b['fs']);
				$b['time'] = date('Y-m-d H:i:s',$b['time']);
				$objPHPExcel->getActiveSheet()->setCellValue('F' . $k, $b['time']);
				$objPHPExcel->getActiveSheet()->setCellValue('G' . $k, $allow[$b['allow']]);
				

				$k++;
			}
			//$objPHPExcel->getActiveSheet()->mergeCells('A'.$k.':G'.$k);
			//$objPHPExcel->getActiveSheet()->setCellValue('A' . $k, '注：灰色区域为自动生成内容，不要改变');
			$name = '用统计';
			$filename = './upload/file/excel.xls';
			$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
			if(is_file($filename)){
				unlink($filename);//删除原来的
			}
			$objWriter->save($filename);
			header('Content-type: application/vnd.ms-excel');
			header('Content-Disposition: attachment; filename='.$filename);
			header('Cache-Control: max-age=0');
			header('Content-Length: '.filesize($filename));
			readfile($filename);
			exit();
		}
		//*/
		//print_r($type_arr);
		//die;
		//echo '<pre>';
		//print_r($details);
		//echo '</pre>';
		//die;
		$aaid=cmf_get_current_admin_id();
		$ars=Db::name("role_user")->field("role_id")->where("user_id",$aaid)->find();
		$this->assign('aqx',$ars['role_id']);
		//if(!$rb){
			return $this->fetch('',
			[
				'detail'=>$details,'page'=>$page,'now'=>$now_year,'last'=>$last_year,
				'type_arr'=>$type_arr,
			]);
		//}
	}
/*
ajax提现通过
*/
public function allow(){
	$id = input('param.id');
	$user = new UserMoneyDetailModel();
	$bool = $user->save(['allow'=>1],['id'=>$id]);
	if($bool){
		$this->success('成功');
	}else{
		$this->error('失败');
	}
}
/*
ajax提现不通过
*/
public function noallow(){
	$id = input('param.id');
	$user = new UserMoneyDetailModel();
	$bool = $user->save(['allow'=>2],['id'=>$id]);
	if($bool){
		$this->success('成功');
	}else{
		$this->error('失败');
	}
}

/*
设置ajax
*/
	public function allows(){
		$allow = $_POST['allow'];
		$allow = array_filter($allow);
		if(!empty($allow)){
			$allow = $_POST['allow'];
			$id = $_POST['allow_id'];
			$mark = $_POST['mark'];
			//echo '<pre>';
			//print_r($_POST);  
			//echo '</pre>';
			$i=0;
			foreach($allow as $v){ 
				$data=Db::name('user_money_detail')->where("id",$id[$i])->find();
				if($v==1 && $data['allow']!=1){			
					$money=substr($data['money'],1);
					$money1=$money*0.2;
					$money2=$money*0.05;
					$money3=$money-$money1-$money2;
					$user1 =				['uid'=>$data["uid"],'money'=>$money1,'from'=>'','time'=>time(),'type'=>'6','fs'=>"购物积分",'fspan'=>$data["fspan"]];
					Db::name('user_money_detail')->insert($user1);

					$user2 = ['uid'=>$data["uid"],'money'=>'-'.$money2,'from'=>'','time'=>time(),'type'=>'7','fs'=>'提现手续费','fspan'=>$data["fspan"]];
					Db::name('user_money_detail')->insert($user2);

					Db::name('user_money_detail')->where('id',$id[$i])->update(['money'=>"-".$money3]);

					//正则匹配发送短信
					if($data['allow']!=1){		//1为已通过
						//echo '<pre>';
						//print_r($data);  
						//echo '</pre>';
						if(!empty($data['fs'])){
							$regex = '/(.*)\((.+)?/';
							$matches = array(); 
							if(preg_match($regex, $data['fs'], $matches)){
								//echo '<pre>';
								//print_r($matches);
								//echo '</pre>';
								$card = substr($matches[2],0,-1);
								$arr = explode('-',$card);
								if(!empty($arr[6])){
									$name = Db::name('user')->field('user_nickname')->where(['id'=>$data['uid']])->find();
									$result=$this->sendsms($arr[6],$name['user_nickname']);
									//print_r($result);
								}
							}
						}
					}	
				}
				Db::name('user_money_detail')->where(['type'=>2,'id'=>$id[$i]])->update(['allow'=>$v,'mark'=>$mark[$i]]); 
				//echo Db::getlastsql();
				//echo "<br />";
				$i++;
			}
			$this->success('修改成功');
			/*
			for($i=0;$i<count($id);$i++){	
				Db::name('user_money_detail')->where(['type'=>2,'id'=>$id[$i]])->update(['allow'=>$allow[$i],'mark'=>$mark[$i]]);
				//echo Db::getlastsql();
			}
			$this->success('修改成功');
			*/
		}else{
			$this->error('未做修改');
		}
	}
	/*
	代理收益
	*/
	public function getSons(){
		echo '我是求代理收益';
	}
	public function sendsms($tel=null,$name=null){
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
		$request->setTemplateCode("SMS_115950131");
		//选填-假如模板中存在变量需要替换则为必填(JSON格式),友情提示:如果JSON中需要带换行符,请参照标准的JSON协议对换行符的要求,比如短信内容中包含\r\n的情况在JSON中需要表示成\\r\\n,否则会导致JSON在服务端解析失败
		$request->setTemplateParam("{\"name\":\"$name\",\"product\":\"云通信服务\"}");
		//选填-发送短信流水号
		//$request->setOutId("1234522");
		//发起访问请求
	  return $acsResponse = $acsClient->getAcsResponse($request);	
	}
}