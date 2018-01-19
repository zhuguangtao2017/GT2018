<?php
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
use think\Validate;
use app\user\model\UserModel;
use app\portal\model\PortalCategoryModel;
use app\user\model\ProvinceModel;
use cmf\controller\AdminBaseController;

class ProvinceDetailController extends AdminBaseController{
	public function index (){
		$model = new UserModel();
		$province = new ProvinceModel();
		$pro = $province->select()->toArray();
		//echo '<pre>';
		//print_r($pro);
		//echo '</pre>';
		return $this->fetch('',['pro'=>$pro]);
	}
	public function show (){
		$pid = input('param.pid',false,0,'\d');
		$model = new UserModel();
		$trees = new PortalCategoryModel();
		//$data = $model->where(['pid'=>$pid])->select()->toArray();
		$res = $trees->zjh($pid);
		//echo '<pre>';
		//var_dump($res);
		//echo '</pre>';
		$res = empty($trees->zjh($pid))?'暂无发展':$trees->zjh($pid);
		return $this->fetch('',['tree'=>$res]);
	}
}