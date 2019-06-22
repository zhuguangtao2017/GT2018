<?php if (!defined('THINK_PATH')) exit();?>
                    <form method="post" enctype="multipart/form-data" target="_blank" id="form-order">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover">
                                <thead>
                                <tr>
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:sort_list('order_sn');">ID</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:sort_list('order_sn');">订单编号</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:sort_list('type');">类型</a>
                                    </td>
                                    <td class="text-center">
                                        <a href="javascript:sort_list('addtime');">申请日期</a>
                                    </td>
                                    <td class="text-center"><a href="javascript:void(0);">状态</a></td>
                                    <td class="text-center">操作</td>
                                </tr>
                                </thead>
                                <tbody>
                                <?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$items): $mod = ($i % 2 );++$i;?><tr>
                                        <td class="text-center"><?php echo ($items["id"]); ?></td>
                                        <td class="text-center"><a href="<?php echo U('order/detail',array('order_id'=>$items['order_id']));?>"><?php echo ($items["order_sn"]); ?></a></td>
                                        <td class="text-center">
                                        	<?php if($items[type] == 0): ?>退货<?php endif; ?>
                                        	<?php if($items[type] == 1): ?>换货<?php endif; ?>                                            
                                        </td>
                                        <td class="text-center"><?php echo (date('Y-m-d H:i',$items["addtime"])); ?></td>
                                        <td class="text-center">
                                        	<?php if($items[status] == 0): ?>未处理<?php endif; ?>
                                        	<?php if($items[status] == 1): ?>处理中<?php endif; ?>
                                        	<?php if($items[status] == 2): ?>已完成<?php endif; ?>                                        
                                        </td>
                                        <td class="text-center">
                                            <a href="<?php echo U('order/return_info',array('id'=>$items['id']));?>" data-toggle="tooltip" title="" class="btn btn-info" data-original-title="查看详情"><i class="fa fa-eye"></i></a>
                                            <a href="javascript:void(0);" onclick="if(confirm('确定要删除吗?')) location.href='<?php echo U('order/return_del',array('id'=>$items['id']));?>';" id="button-delete6" data-toggle="tooltip" title="" class="btn btn-danger" data-original-title="删除"><i class="fa fa-trash-o"></i></a> <?php if($items[status] == 1): ?><a href="javascript:void(0);" data-toggle="tooltip" title="" id="Refresh" class="btn btn-success" onclick="Refresh(this,<?php echo ($items['id']); ?>)" data-original-title="刷新"><i class="fa fa-circle-o-notch"></i><?php endif; ?></td>
                                    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                    <div class="row">
                        <div class="col-sm-6 text-left"></div>
                        <div class="col-sm-6 text-right"><?php echo ($page); ?></div>
                    </div>
<script>
    $(".pagination  a").click(function(){
        var page = $(this).data('p');
        ajax_get_table('search-form2',page);
    });
    function Refresh(obj,id) {
        //alert(id);
        $.ajax({
            type : "POST",
            url:"/index.php/Admin/user/returnStatus",
            data : {'id':id},
            success: function(data){
                var data = eval('('+data+')');
                if(data.status == 1){
                    alert(data.msg);
                    $(obj).parent().parent().remove();
                }else {
                    alert('订单状态不允许');
                }

            }
        });
    }
</script>