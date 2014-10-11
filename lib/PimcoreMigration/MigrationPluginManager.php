<?php

/**
 * Description of MigratioPluginManager
 *
 * @author Achim Kramer <achim@zibra.de>
 */
class PimcoreMigration_MigrationPluginManager {
    
    /**
     * Reference to PimcoreMigration object
     * 
     * @var PimcoreMigration_Migration 
     */
    private $migration;
    
    /**
     * Constructor function of the Migration Plugin Manager
     * 
     * @param PimcoreMigration_Migration $migration
     */
    public function __construct($migration) {
        
        $this->migration = $migration;
    }
    
    /**
     * @todo build dynamically
     * 
     * @var array 
     */
    private $plugins = array("document-page" => "PimcoreMigration_MigrationPlugin_DocumentPageMigration");
    
    
    /**
     * This function returns a plugin object for the specified type. 
     * 
     * @param string $type
     */
    public function getPlugin($type) {
        
        // Check if Plugin is known
        if(!isset($this->plugins[$type])) {
            
            throw new \Exception("Plugin for type '". $type ."' is unknown.");
        }
        
        // Check if plugin already instantiated
        if(is_string($this->plugins[$type])) {
            
            
            // retrieve plugin classname
            $pluginClassName = $this->plugins[$type];

            // Initialize PluginClass
            $this->plugins[$type] = new $pluginClassName();
            
            // Inject dependencies to this new plugin object
            $this->plugins[$type]->setMigration($this->migration);
        }
        
        // Return configured plugin object
        return $this->plugins[$type];
    }
    
    /**
     * @todo Description
     */
    public function registerPlugin() {}
}
