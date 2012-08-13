<?php $riview->get('loader')->load(array('riMenu::admin.css', 'jquery.lib', 'jquery.ui.lib', 'jquery.validation.lib', 'jsrender.lib', 'inline.js' => array('inline' => "
<script type='text/javascript'>
jQuery(document).ready(function(){

	jQuery('#add-new-root').bind('click', function(){
		jQuery(jQuery('#menu-template').render([{
			level: 0, 
			sort_order: index_menu > 1 ? parseInt(jQuery('#root_menu tr.level_0:last').find('input.sort_order').val())+1 : 0,
			array_id: index_menu++, 
			array_parent_id: 0			
		}])).insertAfter(jQuery('#root_menu tr:last'));																		
	});

	// bind to click event of the add new menu icon
	jQuery('.add').live('click', function(){
		var last_element = current = jQuery(this).closest('tr');
		var current_level = current.data('level');
		// sort order is pre-set at 0 for the first element of a branch
		var sort_order = 0;
		current.nextAll('tr').each(function(){
			var level = jQuery(this).data('level');
			
			if(level == current_level+1)
				sort_order = parseInt(jQuery(this).find('input.sort_order').val())+1;
			
			if(level > current_level){
				last_element = jQuery(this);				
			}
			else
				return;
		});
		
		jQuery(jQuery('#menu-template').render([{
			level: current.data('level') + 1, 
			array_id: index_menu++, 
			array_parent_id: current.data('array-id'), 
			sort_order: sort_order
		}])).insertAfter(last_element);
	});
	
	jQuery('.remove').live('click', function(){
		var this_row = jQuery(this).closest('tr');
		var current_level = parseInt(this_row.data('level'));
		var current_id = parseInt(this_row.data('id'));
		
		// remove the children				
		this_row.nextAll('tr').each(function(){
			// we stop if the level > the parent level
			if(parseInt(jQuery(this).data('level')) <= current_level){
				return;
			}
			// insert new delete input
			var id = parseInt(jQuery(this).data('id'))
			if(id > 0)
				jQuery('<input type=\"hidden\" name=\"delete[]\" value=\"' + jQuery(this).data('id') + '\" />').insertAfter(jQuery('#box_content'));
			
			jQuery(this).remove();
		});
		// insert new delete input		
		if(current_id > 0)
			jQuery('<input type=\"hidden\" name=\"delete[]\" value=\"' + current_id + '\" />').insertAfter(jQuery('#root_menu'));
		// remove the current menu
		this_row.remove();	
	});
	
	jQuery('select.menus_type').live('change', function(){
		switch(jQuery(this).val()){
			case '".$types['Product']."':
				jQuery(this).closest('tr').data('type', 'product').find('input.menus_main_page:first').attr('readonly', true).val('product_info');
				break;
			case '".$types['Category']."':
				jQuery(this).closest('tr').data('type', 'category').find('input.menus_main_page:first').attr('readonly', true).val('index');
				break;
			case '".$types['EzPage']."':
				jQuery(this).closest('tr').data('type', 'ezpage').find('input.menus_main_page:first').attr('readonly', true).val('page');
				break;
			case '".$types['Manufacturer']."':
				jQuery(this).closest('tr').data('type', 'manufacturer').find('input.menus_main_page:first').attr('readonly', true).val('index');
				break;
			case '".$types['Page']."':
				jQuery(this).closest('tr').data('type', 'page').find('input.menus_main_page:first').attr('readonly', true);
				break;
			case '".$types['Custom']."':
				jQuery(this).closest('tr').data('type', 'page').find('input.menus_main_page:first').attr('readonly', false);
				break;
		}		
	})
	
	// autocomplete	
	jQuery(function() {	
		var cache = {}, lastXhr;
		jQuery('input.menus_suggest:not(.ui-autocomplete-input)').live('focus', function (event) {					
	    	$(this).autocomplete({
				minLength: 2,
				source: function(request, response) {
				
					request.type = jQuery(this.element).closest('tr').data('type');
					
					if(!(request.type in cache)) cache[request.type] = {};
					
					var term = request.term;
					if ( term in cache[request.type] ) {
						response( cache[request.type][ term ] );
						return;
					}										
					
					$('#please-wait').css('visibility', 'visible');
					lastXhr = $.getJSON( 'ri.php/admin_menu_search/', request, function( data, status, xhr ) {
						cache[request.type][ term ] = data;
						if ( xhr === lastXhr ) {
							response( data );
						}
						$('#please-wait').css('visibility', 'hidden');						
					});
				},
				select: function( event, ui ) {
					jQuery(this).val( ui.item.label );
					var parent = jQuery(this).closest('tr');	
					parent.find('input.menus_parameters').val(ui.item.parameters);
					parent.find('input.menus_main_page').val(ui.item.main_page);
					//jQuery(this).nextAll('.merchants_ids').val(ui.item.value);			
					return false;
				}
			});
		});
	});
	
	jQuery(function() {	
		$('#menu_form').validate();
	});
});
</script>")));
?>
<script id="menu-template" type="text/x-jsrender">
	<tr data-id="-1" data-array-id="{{:array_id}}" data-level="{{:level}}" class="level_{{:level}}">
		<td><div style="margin-left: {{:level * 10}}px;"><input type="text"
				class="required menus_name" value="" name="menu[{{:array_id}}][menus_name]"><span
				class="menu-icon add"><img border="0" alt=""
					src="includes/languages/english/images/buttons/add.png"> </span><span
				class="menu-icon remove"><img border="0" alt=""
					src="includes/languages/english/images/buttons/remove.png"> </span>
		</div>
		</td>
		<td><input type="hidden" class="menus_id" value="0"
			name="menu[{{:array_id}}][menus_id]"> <input type="hidden"
			class="array_parent_id" value="{{:array_parent_id}}" name="menu[{{:array_id}}][array_parent_id]"> 
			<?php echo zen_draw_pull_down_menu('menu[{{:array_id}}][menus_type]', $types_dropdown, '', 'class="menus_type"');?>
		<input type="text" class="menus_suggest"
			value="" name="menu[{{:array_id}}][menus_suggest]">
		</td>
		<td></td>
		<td><input type="text" class="menus_main_page"
			name="menu[{{:array_id}}][menus_main_page]"></td>
		<td><input type="text" class="menus_parameters"
			name="menu[{{:array_id}}][menus_parameters]"></td>
		<td><input type="text" class="menus_attributes"
			name="menu[{{:array_id}}][menus_attributes]"></td>
		<td><input type="text" class="sort_order" value="{{:sort_order}}"
			name="menu[{{:array_id}}][sort_order]"></td>
	</tr>
</script>
<div class='title'>
	<h3>
	<?php rie('Shop Navigation')?>
	</h3>
	
</div>
<div class="content_pagination">
	<form action="<?php echo $router->generate('admin_menu_edit', array('menus_id' => $menus_id));?>"
		method="post" id="menu_form">
		<div id='menu-title'>
			<label style="float:left;line-height: 28px;margin-left: 10px;"><?php rie('Name')?></label>
			<?php echo zen_draw_input_field('menu[0][menus_name]', $menus_id > 0 ? $parent['menus_name'] : '', 'class="menus_name"');?>
		
			</a>
			<?php echo zen_draw_hidden_field('menu[0][menus_id]', $menus_id, 'class="menus_id"');?>
			<?php echo zen_draw_hidden_field('menu[0][array_parent_id]', 0, 'class="array_parent_id"');?>
			<?php echo zen_draw_hidden_field('menu[0][menus_type]', 0, 'class="menus_type"');?>
		</div>

		<div id="please-wait"></div>
		<a href="javascript:void(0);" id="add-new-root" class='button_content'><?php rie('Create New Navigation')?></a>
		
		<br class="clr" />
		<div id="box_content">
			<div class="content_content">
				<table id='root_menu'>
					<tr>
						<td>Name</td>
						<td>Link to</td>
						<td></td>
						<td>Main page</td>
						<td>Parameters</td>
						<td>Attributes</td>
						<td>Sort Order</td>
					</tr>

					<?php
					$array_parent = array($menus_id => 0);
					$counter = 0;
					foreach ($menus as $key => $menu){
						$counter = $key + 1;
						$array_parent[$menu['menus_id']] = $counter;
						?>
					<tr class="level_<?php echo $menu['level']?>"
						data-level="<?php echo $menu['level']?>"
						data-array-id="<?php echo $counter?>"
						data-id="<?php echo $menu['menus_id']?>">
						<td>
							<div style="margin-left: <?php echo $menu['level'] * 10?>px;">
								<?php echo zen_draw_input_field('menu['.$counter.'][menus_name]',$menu['menus_name'],'class="menus_name"');?>
								<span class="menu-icon add"></span>
								<span class="menu-icon remove"></span>
							</div>
						</td>
						<td><?php echo zen_draw_hidden_field('menu['.$counter.'][menus_id]', $menu['menus_id'], 'class="menus_id"');?>
							<input type="hidden"
							name="menu[<?php echo $counter?>][array_parent_id]"
							value="<?php echo $array_parent[$menu['parent_id']]?>"
							class="array_parent_id" /> <?php echo zen_draw_pull_down_menu('menu['.$counter.'][menus_type]', $types_dropdown, $menu['menus_type'], 'class="menus_type"');?>
							<?php echo zen_draw_input_field('menu['.$counter.'][menus_suggest]',$menu['menus_suggest'],'class="menus_suggest"');?>
						</td>
						<td></td>
						<td><?php echo zen_draw_input_field('menu['.$counter.'][menus_main_page]',$menu['menus_main_page'], 'class="menus_main_page" ' . ($menu['menus_type'] != $types['Custom'] ? 'readonly' : ''));?>
						</td>
						<td><?php echo zen_draw_input_field('menu['.$counter.'][menus_parameters]',$menu['menus_parameters'], 'class="menus_parameters"');?>
						</td>
						<td><?php echo zen_draw_input_field('menu['.$counter.'][menus_attributes]',$menu['menus_attributes'], 'class="menus_attributes"');?>
						</td>
						<td><?php echo zen_draw_input_field('menu['.$counter.'][sort_order]',$menu['sort_order'], 'class="sort_order"');?>
						</td>
					</tr>

					<?php
					}
					?>


				</table>
				<script type="text/javascript">
				var index_menu = <?php echo $counter+1?>;
			</script>
			</div>
		</div>
		<button class="button_content"><?php rie('Save Changes');?></button>
		<a href="<?php echo $router->generate('admin_menu');?>"><?php rie('Cancel')?></a>
	</form>
	<br class='clr' />
	<?php if($menus_id > 0):?>
		<?php rie('Use this code to render the menu:')?><input type="text" value="<?php echo "<?php plugins\\riPlugin\Plugin::get('riMenu.Tree')->render()?>";?>" />
	<?php endif;?>
	<br class='clr' />
</div>
