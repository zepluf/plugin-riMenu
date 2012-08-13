<?php
$menu_down = array();
$menu_down[] = array('id' => 1, 'text' => 'Category');
$menu_down[] = array('id' => 2, 'text' => 'Product');
$menu_down[] = array('id' => 3, 'text' => 'Manufacture');
$menu_down[] = array('id' => 4, 'text' => 'Page');
$menu_down[] = array('id' => 5, 'text' => 'Custom');
$menu_down[] = array('id' => 6, 'text' => 'Extenal');

$_SESSION['parent'] ? $_SESSION['parent'] = $_SESSION['parent'] + 1 : $_SESSION['parent'] = 1;
?>

<div class="content_content">
	<div class="box_first"><?php echo zen_draw_input_field('menu[$parent_id][name]','','class="menu_url"');?>
        <span class="add"><?php echo plugins\riPlugin\Plugin::get('riImage.Image')->find('riCjLoader::add.png');?></span>
        <span class="remove"><?php echo plugins\riPlugin\Plugin::get('riImage.Image')->find('riCjLoader::remove.png');?></span>
    </div>
	<div class="box_second" style="float:right;">	
	<?php echo zen_draw_hidden_field('menu['.$parent_id.'][parent]', $parent_id, 'class="menu_parent"');?>
	
	<?php echo zen_draw_pull_down_menu('menu['.$parent_id.'][linkto]', $menu_down, '', 'class="menu_type"');?>
	
	<?php echo zen_draw_input_field('menu['.$parent_id.'][url]','','class="menu_url"');?>

	<?php echo zen_draw_input_field('menu['.$parent_id.'][sort_order]','', 'class="menu_sort_order"');?>
	</div>	
</div>
<table>
<?php echo zen_draw_hidden_field('menu['.$parent_id.'][parent]', $parent_id, 'class="menu_parent"');?>
<tr>
<td>Name</td>
<td>Link to</td>
<td>Extra fields</td>
</tr>
<tr>
<td><?php echo zen_draw_input_field('menu['.$parent_id.'][name]','','class="menu_url"');?><span class="add"><?php echo zen_image_button('add.png');?></span></td>
<td><?php echo zen_draw_pull_down_menu('menu['.$parent_id.'][linkto]', $menu_down, '', 'class="menu_type"');?><?php echo zen_draw_input_field('menu[$parent_id][url]','','class="menu_url"');?></td>
<td><?php echo zen_draw_input_field('menu['.$parent_id.'][sort_order]','', 'class="menu_sort_order"');?></td>
</tr>

</table>
