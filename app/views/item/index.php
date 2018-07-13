<form action="" method="get">
    <input type="text" value="" name="keyword">
    <input type="submit" value="搜索">
</form>

<p><a href="/item/add">新建</a></p>


<table>
    <tr>
        <th>ID</th>
        <th>内容</th>
        <th>操作</th>
    </tr>
        <?php foreach ($items as $key =>$rs){?>
        <tr>
            <td><?php echo $rs['id'];?></td>
            <td><?php echo $rs['item_name'];?></td>
            <td>
                <a href="/item/detail/<?php echo $rs['id'];?>">查看</a>
                <a href="/item/edit/<?php echo $rs['id'];?>">编辑</a>
                <a href="/item/delete/<?php echo $rs['id'];?>">删除</a>
            </td>
        </tr>
    <?php }?>
</table>
<?php echo $new_pagenavi;?>
