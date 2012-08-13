<?php
namespace plugins\riMenu;

use Symfony\Component\HttpFoundation\RedirectResponse;

use Symfony\Component\HttpFoundation\Request;
use plugins\riSimplex\Controller;
use plugins\riPlugin\Plugin;
use Symfony\Component\HttpFoundation\Response;

class AdminMenuController extends Controller{

    public function indexAction(Request $request){
        $menus_tree = Plugin::get('riMenu.Tree')->getTree();
        $this->view->get('holder')->add('main', $this->view->render('riMenu::backend/_menu_list.php', array('menus' => $menus_tree[0]['sub_menus'], 'menus_tree' => $menus_tree)));
        return $this->render('riMenu::admin_layout.php');
    }
    
    public function ajaxShowSubMenu(Request $request){
        $check = 1;
        $menuId = $request->get('menuId');
        $menus_tree = Plugin::get('riMenu.Tree')->getTree();
        if(count($menus_tree[$menuId]['sub_menus']) > 0){
            foreach ($menus_tree[$menuId]['sub_menus'] as $submenuId){
                $submenu[] = $menus_tree[$submenuId]['menus_id'];
            }
        }else{
            $check = 0;
        }
        return new Response(json_encode(array(
           'html'=>$this->view->render('riMenu::pupup_submenu.php',array('submenu'=>$submenu)),
           'check'=>$check
        )));
    }

    public function editAction(Request $request){
        global $db,$messageStack;

        $menus_id = (int)$request->get('menus_id');

        $types = Plugin::get('settings')->get('riMenu.types');
        $types_dropdown = array(array('text' => ri('Please choose'), 'id' => -1));

        // build the type drop down
        foreach ($types as $key => $value)
            $types_dropdown[] = array('text' => $key, 'id' => $value);

        if(is_array($menu = $request->get('menu'))){

            foreach($menu as $key => $val){

                $data = array(
                    'parent_id' => $menu[$val['array_parent_id']]['menus_id'],
                    'menus_type' => $val['menus_type'],
                    'menus_main_page' => $val['menus_main_page'],
                    'menus_parameters' => $val['menus_parameters'],
                    'menus_attributes' => $val['menus_attributes'],
                    'sort_order' => $val['sort_order']
                );

                $data_desc = array(
                    'menus_id' => $val['menus_id'],
                    'menus_name' => $val['menus_name']
                );
                // if the id is 0, we need to insert
                if($val['menus_id'] == 0){

                    zen_db_perform(TABLE_MENUS, $data);
                    $menu[$key]['menus_id'] = $db->insert_ID();
                    // insert description
                    $data_desc['menus_id'] = $menu[$key]['menus_id'];
                    zen_db_perform(TABLE_MENUS_DESCRIPTION, $data_desc);
                }
                else{
                    zen_db_perform(TABLE_MENUS, $data, 'update', 'menus_id = ' . (int)$val['menus_id']);
                    zen_db_perform(TABLE_MENUS_DESCRIPTION, $data_desc, 'update', 'menus_id = ' . (int)$val['menus_id']);
                }
            }

            // remove the deleted menus
            $delete = $request->get('delete');
            if(is_array($delete) && count($delete) > 0){
                $to_delete = array();
                foreach ($delete as $delete_id) {
                    Plugin::get('riMenu.Tree')->getAllChildren($delete_id, $to_delete);
                    $to_delete[] = $delete_id;
                }
                $this->delete($to_delete);
            }

            // if the menus_id is 0, lets do a redirect
            if($menus_id == 0){
                return new RedirectResponse($this->generateUrl('admin_menu_edit', array('menus_id' => $menu[0]['menus_id'])));
            }
        }

        $this->view->get('holder')->add('main', $this->view->render('riMenu::backend/_menu_edit.php', array(
            'menus_id' => $menus_id,
            'menus' => $menus_id > 0 ? Plugin::get('riMenu.Tree')->getVerticalMenu($menus_id) : array(),
            'parent' => Plugin::get('riMenu.Tree')->getMenu($menus_id),
            'types' => $types,
            'types_dropdown' => $types_dropdown)));

        return $this->render('riMenu::admin_layout.php');
    }

    public function deleteAction(Request $request){
        $this->delete($request->get('menus_id'));

        return new RedirectResponse($this->generateUrl('admin_menu'));
    }

    /*public function indexAction(Request $request){
      return $this->render('riMenu::admin_layout.php', array('menus' => Plugin::get('riMenu.Tree')->getVerticalMenu(191)));
      }  */


    public function searchAction(Request $request){
        $data = array();
        switch($request->get('type')){
            case 'product':
                $products = Plugin::get('riProduct.Products')->findByName($request->get('term'));
                foreach($products as $product){
                    $data[] = array('id' => $product->productsId, 'label' => utf8_encode($product->getDescription()->productsName), 'main_page' => $this->getInfoPage($product->productsId), 'parameters' => 'products_id='.$product->productsId);
                }
                break;
            case 'category':
                if($categories = Plugin::get('riCategory.Categories')->findByName($request->get('term'))){

                    foreach($categories as $category){
                        $data[] = array('id' => $category->categoriesId, 'label' => utf8_encode($category->getDescription()->categoriesName), 'main_page' => 'index', 'parameters' => 'cPath='.$category->categoriesId);
                    }
                }
                break;
            case 'manufacturer':
                if($manufacturers = Plugin::get('riManufacturer.Manufacturers')->findByName($request->get('term'))){

                    foreach($manufacturers as $manufacturer){
                        $data[] = array('id' => $manufacturer->manufacturersId, 'label' => utf8_encode($manufacturer->manufacturersName), 'main_page' => 'index', 'parameters' => 'manufacturers_id='.$manufacturer->manufacturersId);
                    }
                }
                break;
            case 'page':
                $dirs = array_filter(glob(DIR_FS_CATALOG . 'includes/modules/pages/'.$request->get('term').'*'), 'is_dir');

                foreach($dirs as $key => $dir){
                    $dir = basename($dir);
                    $data[] = array('id' => $key, 'label' => $dir, 'main_page' => $dir, 'parameters' => '');
                }

                break;
            case 'ezpage':
                if($ezpages = Plugin::get('riEzPage.EzPages')->findByName($request->get('term'))){

                    foreach($ezpages as $ezpage){
                        $data[] = array('id' => $ezpage->pagesId, 'label' => utf8_encode($ezpage->pagesTitle), 'main_page' => 'index', 'parameters' => 'id='.$ezpage->pagesId);
                    }
                }
                break;
        }
        return new Response(
            json_encode($data)
        );

    }

    private function delete($menus_ids){
        global $db;
        if(is_array($menus_ids)) $menus_ids = implode(',', $menus_ids);

        $db->Execute("DELETE FROM " . TABLE_MENUS . " WHERE menus_id IN(" . $menus_ids . ")");
        $db->Execute("DELETE FROM " . TABLE_MENUS_DESCRIPTION . " WHERE menus_id IN(" . $menus_ids . ")");
    }

    private function getInfoPage($zf_product_id) {
        global $db;
        $sql = "select products_type from " . TABLE_PRODUCTS . " where products_id = '" . (int)$zf_product_id . "'";
        $zp_type = $db->Execute($sql);
        if ($zp_type->RecordCount() == 0) {
            return 'product_info';
        } else {
            $zp_product_type = $zp_type->fields['products_type'];
            $sql = "select type_handler from " . TABLE_PRODUCT_TYPES . " where type_id = '" . (int)$zp_product_type . "'";
            $zp_handler = $db->Execute($sql);
            return $zp_handler->fields['type_handler'] . '_info';
        }
    }

}