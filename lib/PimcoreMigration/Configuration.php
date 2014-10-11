<?php

/**
 * Description of PimcoreMigration_Migration_Configuration
 *
 * @author Achim Kramer <achim@zibra.de>
 */
class PimcoreMigration_Configuration {
    
    /**
     * Member variable holds initial config settings
     * @var array 
     */
    protected $settings = array("migrations-path" => "website/migrations",
                                "migration-history-filename" => "migration-history.json");
    
    /**
     * Array holds Version objects indexted by their version
     * 
     * @var mixed 
     */
    protected $versionObjects;
    
    
    /**
     * This function constructs this configuration from 
     * 
     * @param array $settings
     */
    public function __construct($settings) {
        
        // Merge with default settings
        $this->settings = array_merge($this->settings, $settings); 
        
        // Register versions from configured path
        $this->registerVersionObjectsFromDirectory();
    }
    
    /**
     * Return registered version objects
     * 
     * @return array
     */
    public function getVersionObjects() {
        
        return $this->versionObjects;
    }
    
    /**
     * This function returns an array with all available version ids
     * 
     * @return PimcoreMigration_Migration_Version[]
     */
    public function getVersionIds() {
        
        return array_keys($this->versionObjects);
    }
        
    /**
     * This function scans the configured migration-path for Version classes
     * and creates objects of those classes
     * 
     * NOTICE: Array index is alphabetically sorted
     * 
     * @return array Array with Version objects, indexed by their Version ID
     */
    protected function registerVersionObjectsFromDirectory() {
        
        /*
         * Load Version classes from directory
         */
        
        // 
        $path = realpath(PIMCORE_DOCUMENT_ROOT . "/". trim($this->settings["migrations-path"], '/'));
        $files = glob($path . '/Version*.php');
        
        $this->versionObjects = array();
        if ($files) {
            foreach ($files as $file) {
                require_once($file);
                $info = pathinfo($file);
                $version = substr($info['filename'], 7);
                $class = $info['filename'];
                
                //
                $this->versionObjects[$version] = new $class(); // $this->registerMigration($version, $class);
            }
        }
        
        // sort array key alphabetically ascending
        // @NOTICE: must be alphabetically key sorted!
        ksort($this->versionObjects);
        
        // 
        return $this->versionObjects; 
    }
        
    /**
     * This function returns the settings encapsulated by this Configuration object
     * 
     * @param string $key
     * @return mixed
     */
    public function getSetting($key) {
        
        // return setting specified by the given key
        return $this->settings[$key];
    }
}
