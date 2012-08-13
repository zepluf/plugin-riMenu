<form action="<?php echo $router->generate('admin_menu', riGetAllGetParams());?>" method="POST">
	<input type="hidden" name="action" value="update_nav" />
	<ul class='tree-root'>
			<li class='box-title'><?php rie('Name')?><a href="javascript:void(0);" id='add-root'><?php echo zen_image_button('add.png', IMAGE_NEW_PRODUCT); ?></a></li>
	</ul>
	<div class='toolbox_content'>
	<?php echo zen_image_submit('', 'Save Change','class="button_save"') ?>
	<a href='#'><?php rie('Cancel')?></a>
	</div>
</form>