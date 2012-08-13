<?php
namespace plugins\riMenu;

use plugins\riCore\PluginCore;
use plugins\riPlugin\Plugin;

class riMenu extends PluginCore{
    
    public function install(){
        return Plugin::get('riCore.DatabasePatch')->executeSqlFile(file(__DIR__ . '/sql/install.sql'));
<<<<<<< HEAD
=======
        return true;
>>>>>>> 70e9db6f62263854623e4d90341d692f9a874bff
    }
    
    public function uninstall(){
        return Plugin::get('riCore.DatabasePatch')->executeSqlFile(file(__DIR__ . '/sql/uninstall.sql'));
<<<<<<< HEAD
=======
        return true;
>>>>>>> 70e9db6f62263854623e4d90341d692f9a874bff
    }
}