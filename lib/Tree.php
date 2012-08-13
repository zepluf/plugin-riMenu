<?php
/**
 * Simple Menu Tree
 * @Version: Beta 2
 * @Authour: yellow1912
 * @license http://www.zen-cart.com/license/2_0.txt GNU Public License V2.0
 */ 

namespace plugins\riMenu;

class Tree {
	var $menu_tree = array();
	var $is_deepest_cats_built = false;
	var $parent_html = '';
	var $child_html = '';
	var $current_id = -1;
	var $exceptional_list = array();
	var $new_id;
	var $is_attached = false;
	
	function __construct(){
		if(count($this->menu_tree) == 0){
			global $languages_id, $db;
			$menus_query = "select *
	                      from " . TABLE_MENUS . " c, " . TABLE_MENUS_DESCRIPTION . " cd
	                      where c.menus_id = cd.menus_id
	                      and c.menus_status=1
						  and cd.language_id = '" . (int)$_SESSION['languages_id'] . "'
	                      order by c.parent_id, c.sort_order, cd.menus_name";
			$menus = $db->Execute($menus_query);

			// reset the tree first
			$this->menu_tree = array(); 
			$this->is_deepest_cats_built = false;
			while (!$menus->EOF) {
				$this->menu_tree[$menus->fields['menus_id']] = $menus->fields;								
				$this->menu_tree[$menus->fields['menus_id']]['path'][] = $menus->fields['menus_id'];
				$this->menu_tree[$menus->fields['parent_id']]['sub_menus'][] = $menus->fields['menus_id'];
				$menus->MoveNext();
			}
			
			// walk through the array and build sub/cPath and other addtional info needed
			foreach($this->menu_tree as $key => $value){
				// add sub 'class' for print-out purpose
				$this->menu_tree[$key]['has_children'] = isset($this->menu_tree[$key]['sub_menus']);
				
				// only merge if parent cat is not 0
				if(isset($this->menu_tree[$key]['parent_id']) && $this->menu_tree[$key]['parent_id'] > 0){
					if(is_array($this->menu_tree[$this->menu_tree[$key]['parent_id']]['path']) && count($this->menu_tree[$this->menu_tree[$key]['parent_id']]['path'])> 0)
						$this->menu_tree[$key]['path'] = array_merge($this->menu_tree[$this->menu_tree[$key]['parent_id']]['path'],$this->menu_tree[$key]['path']);
				}
				$this->menu_tree[$key]['nPath'] = $this->menu_tree[$key]['cPath'] = isset($this->menu_tree[$key]['path']) ? implode('_',$this->menu_tree[$key]['path']) : $key;
			}
			// for debugging using super global mod
			// $_POST['menu_tree'] = $this->menu_tree;
		}
		// This special portion of code was added to catch the current menu selected
		$this->current_id = $this->getCurrentNavId();
		$this->exceptional_list = array();
		
		if($this->current_id != -1){
			$cPath = $this->getCpath($this->current_id);
			if(!empty($cPath)){
				$this->exceptional_list = explode('_', $cPath);
			}
		}
	}
	
	function getCurrentNavId(){
		$cPath = $_GET['cPath'];
		if(isset($_GET['nPath']))
			$cPath = $_GET['nPath'];
		if(empty($cPath))
			return -1;
		return $this->_getMenusId($cPath);
	}
	
	function getCpath($menus_id){
		$menus_id = $this->_getMenusId($menus_id);
		return (isset($this->menu_tree[$menus_id]['cPath']) ? $this->menu_tree[$menus_id]['cPath'] : '');
	}
	
	public function getMenu($menus_id){
		return $this->menu_tree[$menus_id];	
	}
	
	function getTree(){
		return $this->menu_tree;
	}	

	/**
	 * 
	 * Enter description here ...
	 */
	public function getVerticalMenu($menus_id){
		$tree = array();
		$this->_getVerticalMenu($menus_id, $tree, 0);
		return $tree;
	}
	
	public function _getVerticalMenu($menus_id, &$tree, $level){
		foreach($this->menu_tree[$menus_id]['sub_menus'] as $sub_id){
			$menu = $this->menu_tree[$sub_id];
			$menu['level'] = $level;			
			$tree[] = $menu;
			if($this->menu_tree[$sub_id]['has_children']){
				$this->_getVerticalMenu($sub_id, $tree, $level+1);
			}
		}
	}
	
	function startAttach(){
		if(SCT_REBUILD_TREE == 'true' || !$this->is_attached)
			return true;
		return false;
	}
	
	function endAttach(){
		$this->is_attached = true;
	}
	
	function retrieveDeepestLevelChildren($menus_id){
		$menus_id = $this->_getMenusId($menus_id);
		return (isset($this->menu_tree[$menus_id]['deepest_cats']) ? $this->menu_tree[$menus_id]['deepest_cats'] : array());
	}
	
	function buildDeepestLevelChildren(){
		if(!$this->is_deepest_cats_built){
			$this->_buildDeepestLevelChildren(0);
			$this->is_deepest_cats_built = true;
		}
		// for debugging using super global mod
		// $_POST['menu_tree'] = $this->menu_tree;
	}
	
	function _buildDeepestLevelChildren($menus_id){
		$parent_id = isset($this->menu_tree[$menus_id]['parent_id']) ? $this->menu_tree[$menus_id]['parent_id'] : -1;
		if(isset($this->menu_tree[$menus_id]['sub_menus'])){
			foreach($this->menu_tree[$menus_id]['sub_menus'] as $sub_cat){
					// we now need to loop thru these cats, and find if they have sub_menus
					$this->_buildDeepestLevelChildren($sub_cat);
			}
		}
		elseif($parent_id > 0){
			$this->menu_tree[$parent_id]['deepest_cats'][] = $menus_id;
		}
		
		if($parent_id >= 0 && isset($this->menu_tree[$menus_id]['deepest_cats'])){
			if(isset($this->menu_tree[$parent_id]['deepest_cats']))
				$this->menu_tree[$parent_id]['deepest_cats'] = array_merge($this->menu_tree[$parent_id]['deepest_cats'],$this->menu_tree[$menus_id]['deepest_cats']);
			else
				$this->menu_tree[$parent_id]['deepest_cats'] = $this->menu_tree[$menus_id]['deepest_cats'];
		}
	}
			
	
	function countSubMenus($menus_id){
		$menus_id = $this->_getMenusId($menus_id);
		return isset($this->menu_tree[$menus_id]['sub_menus']) ? 
				count($this->menu_tree[$menus_id]['sub_menus']) : 0;
	}	

	public function getAllChildren($menus_id){
		$children = array();
		$this->_getAllChildren($menus_id, $children);
		return $children;		
	}
	
	public function _getAllChildren($menus_id, &$children){		
		foreach($this->menu_tree[$menus_id]['sub_cats'] as $sub_id){			
			$children[] = $sub_id;
			if($this->menu_tree[$sub_id]['sub']){
				$this->_getAllChildren($sub_id, $children);
			}
		}		
	}
		
	function _getMenusId($menus_id){
		if(!is_int($menus_id)){
			$temp = explode('_',$menus_id);
			$menus_id = end($temp);
		}
		return $menus_id;
	}
	
	/*
	function attachToMenuTree($new_node, $parent_id = 0){
		// we first need to find and assign a "fake" menu id
		if(!isset($new_node['id']) || isset($this->menu_tree[$new_node['id']])){
			if(!isset($this->new_id) && isset($this->menu_tree[$parent_id]['sub_menus']) && count($this->menu_tree[$parent_id]['sub_menus']) > 0)
				$this->new_id = end($this->menu_tree[$parent_id]['sub_menus']);
			
			$current_id = ++$this->new_id;
		}
		else 
			$current_id = $new_node['id'];
			
		if(!is_numeric($this->menu_tree[$parent_id]['nPath']) || $this->menu_tree[$parent_id]['nPath'] != 0)
			$nPath = "{$this->menu_tree[$parent_id]['nPath']}_{$current_id}";
		else 
			$nPath = $current_id;
			
		// we will then update its parent sub_cats. Since theese new add-on menus are "fake" and don't have
		// any product, we dont need to re-calculate the deepest_cats though.
		$this->menu_tree[$parent_id]['sub_cats'][] = $current_id;

		if(isset($new_node['children']))
			$new_node['sub'] = 'has_sub'; 
		else 
			$new_node['sub'] = 'no_sub'; 
			
		$node = array('name' => $new_node['name'], 'parent_id' => $parent_id, 'path' => explode('_',$nPath), 'sub' => $new_node['sub'], 'cPath' => $new_node['cPath'], 'nPath' => $nPath);	

		$this->menu_tree[$current_id] = $node;
		
		if(isset($new_node['children']))
			foreach($new_node['children'] as  $child)
				$this->attachToMenuTree($child, $current_id);
	}
	*/
}