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
namespace app\portal\model;

use think\Model;
use think\Db;
class UserModel extends Model
{
     public function getCategorySelect($select_id=0,$id = 0,$level = 0,$level_nbsp='',$str=''){	 
	 
		$category_arr=Db::name('user')->where('pid',13)->where('fid',$id)->select()->toArray();

		return $category_arr[0]['id'];
	 
		
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
			$str.= "<option value=\"".$id."\" ".$selected.">".$level_nbsp . " " . $name."</option>\n";
			$this->getCategorySelect ($select_id, $id, $level,$level_nbsp,$str);
		}
		return $str;
  }


}