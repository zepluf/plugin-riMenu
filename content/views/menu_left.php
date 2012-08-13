<?php
use plugins\riPlugin\Plugin;
$menus_tree = Plugin::get('riMenu.Tree')->getTree();

?>

<div id="items-slideshow-left">
    <ul>
        <?php 
        $i= 0;
        foreach($menus_tree[1]['sub_menus'] as $menu):   
        ?>
            <li <?php 
            if(substr($menus_tree[$menu]['menus_parameters'],6) == $cat[0]['categories_id']){ ?>
                    class="current-menu-left" 
            <?php } ?>>
                <a href="<?php echo zen_href_link($menus_tree[$menu]['menus_main_page'], $menus_tree[$menu]['menus_parameters']); ?>"><?php echo $menus_tree[$menu]['menus_name']; ?></a>
                <hr class="hr-class">
            </li>		
        <?php $i++;
        endforeach; ?>
    </ul>
</div>