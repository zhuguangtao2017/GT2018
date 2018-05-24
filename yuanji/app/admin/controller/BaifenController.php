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
class BaifenController extends AdminBaseController
{
    public function index()
    {
        $data=Db::name('baifen')->find();
        $this->assign('data',$data);
        return $this->fetch();
    }
    public function add()
   {
	if(input('sub'))
	{
		$result=Db::name('baifen')->where('id',1)
		->update([ 
  			   'one'=>input('one'),
			   'two'=>input('two'),
			   'three'=> input('three'),
			   'four'=> input('four'),
			   'five'=> input('five'),
			   'six'=> input('six'),
			   'seven'=> input('seven'),
			   'eight'=>input('eight'),
			   'nine'=>input('nine')
	          		]);
		if($result)
		{
			$this->redirect('Baifen/index');
		}
		else
		{	
			 $this->redirect('Baifen/index');
		}
	}
	
}
  

}