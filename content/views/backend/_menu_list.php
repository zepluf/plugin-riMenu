<div class='title'><h3><?php //rie('Shop Navigation-Listing')?></h3></div>
<form action="<?php echo $router->generate('admin_menu_edit');?>">
	<div class="content_pagination">
		<table style="width: 100%; border:0px">
			<tr>
				<td>ID</td>
				<td>Name</td>
				<td><a href="<?php echo $router->generate('admin_menu_edit', array('menus_id' => 0))?>"><?php rie('New')?></a></td>
			</tr>
			<tr></tr>
			<?php foreach($menus as $menu):?>
			<tr style="background:#F3F3F3;">
				<td><?php echo $menu;?></td>
				<td><?php echo $menus_tree[$menu]['menus_name']?></td>
				<td><a href="<?php echo $router->generate('admin_menu_edit', array('menus_id' => $menu))?>"><?php rie('Edit')?></a> - <a href="<?php echo $router->generate('admin_menu_delete', array('menus_id' => $menu))?>"><?php rie('Delete')?></a></td>
			</tr>
			<?php endforeach;?>
		</table>
	</div>
</form> 