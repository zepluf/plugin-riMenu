<?php
use plugins\riPlugin\Plugin;

foreach(Plugin::get('settings')->get('theme.menus') as $holder => $holder_parameters){
    Plugin::get('dispatcher')->addListener(\plugins\riCore\HolderHelperEvents::onHolderStart . '.' . $holder, array(Plugin::get('riMenu'), 'injectMenu'));
}

