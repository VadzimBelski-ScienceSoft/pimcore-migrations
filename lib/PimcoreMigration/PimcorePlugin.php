<?php

/**
 * Description of PimcorePlugin
 *
 * @author Achim Kramer <achim@zibra.de>
 */
class PimcoreMigration_PimcorePlugin  extends Pimcore_API_Plugin_Abstract implements Pimcore_API_Plugin_Interface {
    
    /**
     * Plugin Initialization
     */
    public function init() {
        
        // Do some Event handling
    }
    
    /**
     * 
     */
    public function handleDocument($e) {
    }
       
    /**
     * 
     * @return boolean
     */
    public static function install(){
        return true;
    }
     
    /**
     * 
     * @return boolean
     */
    public static function uninstall(){
        return true;
    }
    
    /**
     * 
     * @return boolean
     */
    public static function isInstalled() {
        return true;
    }
}
