<?php if (!defined('THINK_PATH')) exit();?><form method="post" enctype="multipart/form-data" target="_blank" id="goods_list_form">
    <div class="table-responsive">
        <table class="table table-bordered table-hover">
            <thead>
            <tr>
                <td style="width: 1px;" class="text-center">                
                    
                </td>                
                <td class="text-right goodsid">
                    <a href="javascript:sort('goods_id');">ID</a>
                </td>
                <td class="text-left">
                    <a href="javascript:sort('goods_name');">商品名称</a>
                </td>
                <!-- <td class="text-left">
                    <a href="javascript:sort('goods_sn');">货号</a>
                </td>  -->                               
                <td class="text-left">
                    <a href="javascript:sort('cat_id');">分类</a>
                </td>                
                <td class="text-left">
                    <a href="javascript:sort('shop_price');">价格</a>
                </td>
                <td class="text-center">
                    <a href="javascript:sort('is_recommend');">推荐</a>
                </td>
                <td class="text-center">
                    <a href="javascript:sort('is_new');">新品</a>
                </td>   
                <!-- <td class="text-center">
                    <a href="javascript:sort('is_hot');">热卖</a>
                </td> -->                
                <td class="text-left">
                    <a href="javascript:void(0);">库存</a>
                </td>
                <td class="text-left">
                    <a href="javascript:sort('is_on_sale');">上/下架</a>
                </td>
                <td class="text-left">
                    <a href="javascript:sort('goods_state');">审核状态</a>
                </td>              
                <td class="text-right">操作</td>
            </tr>
            </thead>
            <tbody>
            <?php if(is_array($goodsList)): $i = 0; $__LIST__ = $goodsList;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$list): $mod = ($i % 2 );++$i;?><tr>
                    <td class="text-center">
                       <input type="checkbox" name="goods_id[]" value="<?php echo ($list["goods_id"]); ?>"/>
                    </td>
                    <td class="text-right"><?php echo ($list["goods_id"]); ?></td>
                    <td class="text-left"><?php echo (getSubstr($list["goods_name"],0,33)); ?></td>
                    <!-- <td class="text-left"><?php echo ($list["goods_sn"]); ?></td> -->
                    <td class="text-left"><?php echo ($catList[$list[cat_id1]][mobile_name]); ?></td>
                    <td class="text-left"><?php echo ($list["shop_price"]); ?></td>
                    <td class="text-center">
                        <img width="20" height="20" src="/Public/images/<?php if($list[is_recommend] == 1): ?>yes.png<?php else: ?>cancel.png<?php endif; ?>" onclick="changeTableVal('goods','goods_id','<?php echo ($list["goods_id"]); ?>','is_recommend',this)"/>
                    </td>                     
                    <td class="text-center">
                        <img width="20" height="20" src="/Public/images/<?php if($list[is_new] == 1): ?>yes.png<?php else: ?>cancel.png<?php endif; ?>" onclick="changeTableVal('goods','goods_id','<?php echo ($list["goods_id"]); ?>','is_new',this)"/>
                    </td>
                    <!-- <td class="text-center">
                        <img width="20" height="20" src="/Public/images/<?php if($list[is_hot] == 1): ?>yes.png<?php else: ?>cancel.png<?php endif; ?>" onclick="changeTableVal('goods','goods_id','<?php echo ($list["goods_id"]); ?>','is_hot',this)"/>
                    </td>  -->                                                          
                    <td class="text-left"><?php echo ($list["store_count"]); ?></td>
                    <td class="text-left">
                        <?php if($list[is_on_sale] == 0 ): ?>下架<?php endif; ?>
                        <?php if($list[is_on_sale] == 1): ?>上架<?php endif; ?>
                    </td>
                    <td class="text-left">
                    <?php if($list[goods_state] == 0): ?>待审核<?php endif; ?>
                    <?php if($list[goods_state] == 1): ?>审核通过<?php endif; ?>
                    <?php if($list[goods_state] == 2): ?>审核失败<?php endif; ?>
                    <?php if($list[goods_state] == 3): ?>违规下架<?php endif; ?>
                    </td>
                    <td class="text-right">
                        <a href="<?php echo U('Admin/Goods/addEditGoods',array('id'=>$list['goods_id']));?>">查看</a>&nbsp;
                        <!--<br/>-->
                        <!--<a id="delgoods" href="<?php echo U('Admin/Goods/delGoods',array('id'=>$list['goods_id']));?>">删除</a>&nbsp;-->
                    </td>

                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
            </tbody>
        </table>
    </div>
</form>
<div class="col-sm-9 text-right"><?php echo ($page); ?></div>
<script>
    // 点击分页触发的事件
    $(".pagination  a").click(function(){
        cur_page = $(this).data('p');
        ajax_get_table('search-form2',cur_page);
    });

</script>
<script>
</script>