<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8">
    <title>小程序管理后台</title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.4 -->
    <link href="/Public/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- FontAwesome 4.3.0 -->
 	<link href="/Public/bootstrap/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <!-- Ionicons 2.0.0 --
    <link href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css" rel="stylesheet" type="text/css" />
    <!-- Theme style -->
    <link href="/Public/dist/css/AdminLTE.min.css" rel="stylesheet" type="text/css" />
    <!-- AdminLTE Skins. Choose a skin from the css/skins 
    	folder instead of downloading all of them to reduce the load. -->
    <link href="/Public/dist/css/skins/_all-skins.min.css" rel="stylesheet" type="text/css" />
    <!-- iCheck -->
    <link href="/Public/plugins/iCheck/flat/blue.css" rel="stylesheet" type="text/css" />   
    <!-- jQuery 2.1.4 -->
    <script src="/Public/plugins/jQuery/jQuery-2.1.4.min.js"></script>
	<script src="/Public/js/global.js"></script>
    <script src="/Public/js/myFormValidate.js"></script>    
    <script src="/Public/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="/Public/js/layer/layer-min.js"></script><!-- 弹窗js 参考文档 http://layer.layui.com/-->
    <script src="/Public/js/myAjax.js"></script>
    <script type="text/javascript">
    function delfunc(obj){
    	layer.confirm('确认删除？', {
    		  btn: ['确定','取消'] //按钮
    		}, function(){
    		    // 确定
   				$.ajax({
   					type : 'post',
   					url : $(obj).attr('data-url'),
   					data : {act:'del',del_id:$(obj).attr('data-id')},
   					dataType : 'json',
   					success : function(data){
   						if(data==1){
   							layer.msg('操作成功', {icon: 1});
   							$(obj).parent().parent().remove();
   						}else{
   							layer.msg(data, {icon: 2,time: 2000});
   						}
//   						layer.closeAll();
   					}
   				})
    		}, function(index){
    			layer.close(index);
    			return false;// 取消
    		}
    	);
    }
    
    function selectAll(name,obj){
    	$('input[name*='+name+']').prop('checked', $(obj).checked);
    }   
    
    function get_help(obj){
        layer.open({
            type: 2,
            title: '帮助手册',
            shadeClose: true,
            shade: 0.3,
            area: ['70%', '80%'],
            content: $(obj).attr('data-url'), 
        });
    }
    </script>        
  </head>
  <body style="background-color:#ecf0f5;">
 

<div class="wrapper">
    <div class="breadcrumbs" id="breadcrumbs">
	<ol class="breadcrumb">
	<?php if(is_array($navigate_admin)): foreach($navigate_admin as $k=>$v): if($k == '后台首页'): ?><li><a href="<?php echo ($v); ?>"><i class="fa fa-home"></i>&nbsp;&nbsp;<?php echo ($k); ?></a></li>
	    <?php else: ?>    
	        <li><a href="<?php echo ($v); ?>"><?php echo ($k); ?></a></li><?php endif; endforeach; endif; ?>          
	</ol>
</div>

	<section class="content">
    <form action="<?php echo U('Goods/addEditCategorydel');?>" method="post">
       <div class="row">
       		<div class="col-xs-12">
	       		<div class="box">
	             <div class="box-header">
	               	<nav class="navbar navbar-default">	     
				        <div class="collapse navbar-collapse">
						   <div class="navbar-form row">
				            	<div class="col-md-1">

					            </div>
					            <div class="col-md-9">
					            	<span class="warning">温馨提示：顶级分类（一级大类）设为推荐时才会在首页楼层中显示</span>
					            </div>
					            <div class="col-md-2">
					            <a href="<?php echo U('Goods/addEditCategory');?>" class="btn btn-primary pull-right"><i class="fa fa-plus"></i>新增分类</a>
                      <button class="btn btn-danger pull-right">批量删除</button>
				            	</div>
				            </div>
				      	</div>
	    			</nav> 	               
	             </div><!-- /.box-header -->
	           <div class="box-body">
	           <div class="row">
	            <div class="col-sm-12">
	              <table id="list-table" class="table table-bordered table-striped dataTable" role="grid" aria-describedby="example1_info">
	                 <thead>
	                   <tr role="row">
                      <th>全选 <input type="checkbox" onclick="$('input[name=\'cat_id\[\]\']').prop('checked', this.checked);"></th>
	                   	   <th valign="middle">分类ID</th>
		                   <th valign="middle" style="text-align:center;">分类名称</th>
                           <!--<th valign="middle" style="text-align:center;">类型</th>-->
                           <th valign="middle" style="text-align:center;">是否推荐</th>
		                   <th valign="middle" style="text-align:center;">是否显示</th>
                           <!-- <th valign="middle">佣金比例</th>
                           <th valign="middle">分组</th>
		                   <th valign="middle">排序</th> -->
		                   <th valign="middle" style="text-align:center;" >操作</th>
	                   </tr>
	                 </thead>
			<tbody>
			<?php if(is_array($cat_list)): foreach($cat_list as $k=>$vo): ?><tr role="row" align="center" class="<?php echo ($vo["level"]); ?>" id="<?php echo ($vo["level"]); ?>_<?php echo ($vo["id"]); ?>" <?php if($vo[level] > 1): ?>style="display:none"<?php endif; ?>>
			  			 <td><input type="checkbox" name="cat_id[]" class="cat_id" value="<?php echo ($vo["id"]); ?>"></td>
               <td><?php echo ($vo["id"]); ?></td>
							<td><?php echo ($vo["mobile_name"]); ?></td>
                         <td>
                             <img width="20" height="20" src="/Public/images/<?php if($vo[is_hot] == 1): ?>yes.png<?php else: ?>cancel.png<?php endif; ?>" onclick="changeTableVal('goods_category','id','<?php echo ($vo["id"]); ?>','is_hot',this)"/>
                         </td>
	                     <td>
                             <img width="20" height="20" src="/Public/images/<?php if($vo[is_show] == 1): ?>yes.png<?php else: ?>cancel.png<?php endif; ?>" onclick="changeTableVal('goods_category','id','<?php echo ($vo["id"]); ?>','is_show',this)"/>                             
                         </td>
	                     <td>
	                      <a class="btn btn-primary" href="<?php echo U('Goods/addEditCategory',array('id'=>$vo['id']));?>"><i class="fa fa-pencil"></i></a>
	                      <a class="btn btn-danger" href="javascript:del_fun('<?php echo U('Goods/delGoodsCategory',array('id'=>$vo['id']));?>');"><i class="fa fa-trash-o"></i></a>
							 <a><button class="btn bg-navy" type="button" data-value="<?php echo ($vo['id']); ?>" onclick="tree_open(this);"><i class="fa fa-angle-double-up"></i>展开</button></a>
			     		</td>
						</tr><?php endforeach; endif; ?>
	                   </tbody>
	               </table></div></div>
		               <div class="row">
			               <div class="col-sm-5">
			               <div class="dataTables_info" id="example1_info" role="status" aria-live="polite"></div></div>
		               </div>
	             </div><!-- /.box-body -->
	           </div><!-- /.box -->
       		</div>
       </div>
       </form>
     </section>
</div>
<script type="text/javascript">

// 展开收缩
function  tree_open(obj)
{
    var id = $(obj).attr('data-value');
    var Iid = 'id_'+id;
    console.log(id);
    var cla = $(obj).find("i").attr('class')
	var cz = '';
	if(cla=='fa fa-angle-double-up'){
        $.ajax({
			url:'/index.php/admin/goods/ajaxcate',
			type:'post',
            data : {'id':id},
            dataType : 'json',
			success:function (data) {
                data.data.forEach(function(value, index, array) {
                    if(value['level']<3)  cz = '<a><button class="btn bg-navy" type="button" data-value='+value['id']+' onclick="tree_open(this);"><i class="fa fa-angle-double-up"></i>展开</button></a>';
                    else cz = '';
                    if(value['is_hot']==1){ img1 = 'yes.png';}
                    else{ img1 = 'cancel.png';}
                    if(value['is_show']==1){ img2 = 'yes.png';}
                    else{ img2 = 'cancel.png';}
                    var html = '<tr align="center" id='+Iid+' class="'+Iid+' levl2"><td><input type="checkbox" name="cat_id[]" class="cat_id" value='+value['id']+'></td><td>'+value['level2']+value['id']+'</td><td>'+value['mobile_name']+'</td><td><img width="20" height="20" src="/Public/images/'+img1+'" onclick="changeTableVal(\'goods_category\',\'id\',\''+value['id']+'\',\'is_hot\',this)"/></td><td><img width="20" height="20" src="/Public/images/'+img2+'" onclick="changeTableVal(\'goods_category\',\'id\',\''+value['id']+'\',\'is_show\',this)"/></td><td><a class="btn btn-primary" href="./addEditCategory?id='+value['id']+'"><i class="fa fa-pencil"></i></a> <a class="btn btn-danger" href="javascript:del_fun(\'./delGoodsCategory?id='+value['id']+'\');"><i class="fa fa-trash-o"></i></a> '+cz+'</td></tr>';
                    $(obj).parents("tr").after(html);
                });
            }
		});
        $(obj).html('<i class="fa fa-angle-double-down"></i>收起');
	}else{
        $(obj).html('<i class="fa fa-angle-double-up"></i>展开');
        var a = '.'+Iid;
        $(a).hide();
	}
}
    
// 以下是 bootstrap 自带的  js
function rowClicked(obj)
{
  span = obj;

  obj = obj.parentNode.parentNode;

  var tbl = document.getElementById("list-table");

  var lvl = parseInt(obj.className);

  var fnd = false;
  
  var sub_display = $(span).hasClass('glyphicon-minus') ? 'none' : '' ? 'block' : 'table-row' ;
  //console.log(sub_display);
  if(sub_display == 'none'){
	  $(span).removeClass('glyphicon-minus btn-info');
	  $(span).addClass('glyphicon-plus btn-warning');
  }else{
	  $(span).removeClass('glyphicon-plus btn-info');
	  $(span).addClass('glyphicon-minus btn-warning');
  }

  for (i = 0; i < tbl.rows.length; i++)
  {
      var row = tbl.rows[i];
      
      if (row == obj)
      {
          fnd = true;         
      }
      else
      {
          if (fnd == true)
          {
              var cur = parseInt(row.className);
              var icon = 'icon_' + row.id;
              if (cur > lvl)
              {
                  row.style.display = sub_display;
                  if (sub_display != 'none')
                  {
                      var iconimg = document.getElementById(icon);
                      $(iconimg).removeClass('glyphicon-plus btn-info');
                      $(iconimg).addClass('glyphicon-minus btn-warning');
                  }else{               	    
                      $(iconimg).removeClass('glyphicon-minus btn-info');
                      $(iconimg).addClass('glyphicon-plus btn-warning');
                  }
              }
              else
              {
                  fnd = false;
                  break;
              }
          }
      }
  }

  for (i = 0; i < obj.cells[0].childNodes.length; i++)
  {
      var imgObj = obj.cells[0].childNodes[i];
      if (imgObj.tagName == "IMG")
      {
          if($(imgObj).hasClass('glyphicon-plus btn-info')){
        	  $(imgObj).removeClass('glyphicon-plus btn-info');
        	  $(imgObj).addClass('glyphicon-minus btn-warning');
          }else{
        	  $(imgObj).removeClass('glyphicon-minus btn-warning');
        	  $(imgObj).addClass('glyphicon-plus btn-info');
          }
      }
  }

}
</script>
</body>
</html>