<?php

/**
 * Description of Migration
 *
 * @author Achim Kramer <achim@zibra.de>
 */
class PimcoreMigration_Migration {
    
    /**
     * Reference to singleton instance
     * 
     * @var PimcoreMigration_Migration 
     */
    private static $instance;    
    
    /*
     * Reference to pimcore's internal Zend EventmMnager
     * @var Zend_EventManager_EventManager
     */
    private $eventManager;
            
    /**
     * Member variable holds PimcoreMigration configuration settings
     * 
     * @var PimcoreMigration_Configuration 
     */
    private $migrationConfig;
    
    /**
     * Reference to Migration PluginManager
     * 
     * @var PimcoreMigration_MigrationPluginManager
     */
    private $migrationPluginManager;
    
    /**
     * Reference to MigrationHistory
     * 
     * @var PimcoreMigration_MigrationHistory 
     */
    private $migrationHistory;
    
    /**
     * Reference to the currently migrating version object
     * 
     * @var PimcoreMigration_Version 
     */
    private $currentMigratingVersion;
    
    
    /**
     * This is the constructor function for the leading Migration class
     * 
     * @FEATURE: could be moved into separate Factory
     * 
     * @param PimcoreMigration_Configuration|null $migrationConfig
     */
    private function __construct($migrationConfig = null) {
        
        $this->migrationConfig = $migrationConfig;
    }
   
    /**
     * This function does a migration. Optionally there is as certain version identifier 
     * given, to which pimcore is migrated to.
     * 
     * @param string|null $to If specified, it is migrated to this version, else this function migrates to the latest definition
     */
    public function migrate($to = null) {
        
        // @TEST
//        $to = "21040710192356";
//        $to = "21040210190110";
//        $to = "0";
        // @TEST
        
        
        
        
        // Check if Migration object is configures yet
        if(!$this->migrationConfig) {
            throw new \Exception("Migration object is not configured yet!");
        }
        
        
        // Activate MigrationHistory
        $this->migrationHistory->activate();
        
        
        // Retrieve Available-Versions
        $availableVersions = $this->migrationConfig->getVersionObjects();
                
        // Check migration history validity
        // @TODO
        
        // Get available version Ids from Configuration
        $availableVersionIds = $this->migrationConfig->getVersionIds();
        
        // ..and already migrated version Ids from MigrationHistory
        $migratedVersionIds = $this->migrationHistory->getMigratedVersionIds();
        
        
        // Get current version Id
        $from = (!empty($migratedVersionIds)) ? (string) end($migratedVersionIds) : "0";
        
        // Retrieve target version Id
        $to = (is_string($to)) ? $to : end($availableVersionIds);
                
        // Get direction of this migration
        $direction = ($from > $to) ? "down" : "up";
        
        
        // Get Migrations to execute
        $todoVersionIds = $this->determineMigrationsToExecute($direction, $availableVersionIds, $migratedVersionIds, $to);
        
        
        // Process todo-Versions is their correct order
        foreach($todoVersionIds as $todoVersionId) {
            
            // Check if version id is a known
            if(!isset($availableVersions[$todoVersionId])) {
                
                // 
                throw new \Exception("Uups! Version-Object for versionId '". $todoVersionId ."' is missing!");
            }
            
            // Check if version id also has a processable Version-object
            if(!($availableVersions[$todoVersionId] instanceof PimcoreMigration_Version)) {
                
                throw new \Exception("Uups! Version-Object for versionId '". $todoVersionId ."' has wrong type!");
            }
            
            
            // Use Verion object
            $version = $availableVersions[$todoVersionId];
            
            // Inject dependencies
            $version->setMigrationPluginManager($this->migrationPluginManager);
            $version->setMigrationHistory($this->migrationHistory);
            
            
            // Remember this migrating version as current
//            $this->currentMigratingVersion = $version;
            
            // Switch between up- an downgrade
            if($direction == "up") {

                // do upgrade
                $version->doUpgrade();
                
            } else {
                
                // do downgrade
                $version->doDowngrade();                
            }
        }
        
        // Feedback
        echo "Migration finished";
    }
    
    /**
     * This function calculates an array with versions expected to be migrated
     * 
     * @param string $direction Specifies the direction of the migration
     * @param string[] $availableVersionIds
     * @param string[] $migratedVersionIds
     * @param string|null $to
     * @return string[]
     */
    public function determineMigrationsToExecute($direction, $availableVersionIds, $migratedVersionIds, $to = null) {
        
        // Retrieve current version id
        $from = (!empty($migratedVersionIds)) ? end($migratedVersionIds) : "0"; 
        
        
        // Initialize todoMigrations array
        $todoMigrationIds = array();
        
        // Calculate todo migrations
        if($direction == "up") {
        
            // 
            $availableTodoMigrationIds = array_diff($availableVersionIds, $migratedVersionIds);
        
            // 
            foreach($availableTodoMigrationIds as $availableTodoMigrationId) {
                
                if($from < $availableTodoMigrationId && $availableTodoMigrationId <= $to) {
                    
                    $todoMigrationIds[] = $availableTodoMigrationId;
                }
            }
            
            // @NOTICE: should be sorted
            
        } else {
            
            // sort migrated VersionIds reverse
            rsort($migratedVersionIds);
            
            // 
            foreach($migratedVersionIds as $migratedVersionId) {
                
                if($migratedVersionId <= $from && $migratedVersionId > $to) {
                    
                    $todoMigrationIds[] = $migratedVersionId;
                }
            }
        }
        
        // return calculated Migration IDs
        return $todoMigrationIds;        
    }
    
     
    /**
     * This function creates the PimcoreMigration functionality with the given 
     * configuration
     * 
     * @param PimcoreMigration_Configuration $config
     */
    public function setConfiguration($config) {
        
        $this->migrationConfig = $config;
    }
    
    /**
     * Setter function for Zend Eventmanager
     * 
     * @param Zend_EventManager_EventManager $eventManager
     */
    public function setEventManager($eventManager) {
        
        $this->eventManager = $eventManager;
    }
        
    /**
     * Setter function for migration history
     * 
     * @param PimcoreMigration_MigrationHistory $migrationHistory
     */
    public function setMigrationHistory($migrationHistory) {
        
        $this->migrationHistory = $migrationHistory;
    }
    
    /**
     * Setter function for migration plugin manager
     * 
     * @param PimcoreMigration_MigrationPluginManager $migrationPluginManager
     */
    public function setMigrationPluginManager($migrationPluginManager) {
        
        $this->migrationPluginManager = $migrationPluginManager;
    }
       
    /**
     * This function returns the current migrating version object
     * 
     * @return PimcoreMigration_Version
     */
    public function getCurrentMigratingVersion() {
        
        return $this->currentMigratingVersion;
    }
    
    /**
     * This function returns singleton object of this Migration class
     * 
     * @param PimcoreMigration_Configuration $migrationConfig
     */
    public static function getInstance($migrationConfig = null) {
        
        // Check if singleton exists
        if(!self::$instance) {
            
            // build singleton object
            self::$instance = new self($migrationConfig);
            
            
            // Resolve dependency to Pimcore EventManager
            self::$instance->setEventManager(Pimcore::getEventManager());
            
            // Initialize special PluginManager
            self::$instance->setMigrationPluginManager(new PimcoreMigration_MigrationPluginManager($this));

            // Initialize special MigrationHistory
            self::$instance->setMigrationHistory(new PimcoreMigration_MigrationHistory($migrationConfig));
        }
        
        // return singleton object
        return self::$instance;
    }
}
