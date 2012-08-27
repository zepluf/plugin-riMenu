<?php
namespace plugins\riMenu;

use plugins\riCore\PluginCore;
use plugins\riPlugin\Plugin;
use plugins\riCore\Event;

class riMenu extends PluginCore{

    public function install(){
        return Plugin::get('riCore.DatabasePatch')->executeSqlFile(file(__DIR__ . '/sql/install.sql'));
    }
    
    public function uninstall(){
        return Plugin::get('riCore.DatabasePatch')->executeSqlFile(file(__DIR__ . '/sql/uninstall.sql'));
    }
}