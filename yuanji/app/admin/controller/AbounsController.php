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
class AbounsController extends AdminBaseController
{
    public function index()
    {
    	
        $data=Db::name('abonus')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }
   public function add()
   {
   		
		if(input('sub'))
		{
			$arid=input('id/a');
			$i=0;
			foreach ($arid as $id) {
				$i++;
				$result=Db::name('abonus')->where('id',$id)
				->update([ 
	  					  'multiple'=>input('multiple/a')[$i],
				          'abonus'=>input('abonus/a')[$i] 
			 	         ]);
			}
			if($result)
				{
					$this->redirect('Abouns/index');
				}
				else
				{	
					 $this->redirect('Abouns/index');
				}
			
		}
	}
}