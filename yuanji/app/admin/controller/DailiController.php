<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 老猫 <zxxjjforever@163.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;
use think\Db;
use cmf\controller\AdminBaseController;
class DailiController extends AdminBaseController
{
    public function index()
    {
        $data=Db::name('dali')->find();
        $this->assign('data',$data);
        return $this->fetch();
    }
    public function add()
   {
	if(input('sub'))
	{
		$result=Db::name('dali')->where('id',1)
		->update([ 
  			   'sdai'=>input('sdai'),
			   'daili'=>input('daili')  
	          		]);
		if($result)
		{
			$this->redirect('Daili/index');
		}
		else
		{	
			 $this->redirect('Daili/index');
		}
	}
	
}
}