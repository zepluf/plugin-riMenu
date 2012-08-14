<?php
namespace plugins\riMenu;

use plugins\riCore\PluginCore;
use plugins\riPlugin\Plugin;
use plugins\riCore\Event;

class riMenu extends PluginCore{

    public function init(){
        global $autoLoadConfig;

        if(!IS_ADMIN_FLAG){
            $autoLoadConfig[200][] = array('autoType' => 'require', 'loadFile' => __DIR__ . '/lib/init_includes.php');
        }
    }

    public function injectMenu(Event $event)
    {
        Plugin::get('templating.holder')->add($event->getSlot(), Plugin::get('view')->render(Plugin::get('settings')->get('theme.menus.'.$event->getSlot().'.template'), Plugin::get('settings')->get('theme.menus.'.$event->getSlot().'.parameters')));
        // extend here the functionality of the core
        // ...
    }

    public function install(){
        return Plugin::get('riCore.DatabasePatch')->executeSqlFile(file(__DIR__ . '/sql/install.sql'));
    }
    
    public function uninstall(){
        return Plugin::get('riCore.DatabasePatch')->executeSqlFile(file(__DIR__ . '/sql/uninstall.sql'));
    }
}