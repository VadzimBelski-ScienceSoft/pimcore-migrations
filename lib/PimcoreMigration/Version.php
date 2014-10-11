<?php

/**
 * Description of Version
 *
 * @author Achim Kramer <achim@zibra.de>
 */
abstract class PimcoreMigration_Version {
    
    /**
     * Reference to depending PluginManager which provides Migration plugins
     * 
     * @var PimcoreMigration_MigrationPluginManager
     */
    private $migrationPluginManager;
    
    /**
     * Reference to the MigrationHistory object
     * 
     * @var PimcoreMigration_MigrationHistory 
     */
    private $migrationHistory;
    
    /**
     * This variable holds an task counter, which is increased every time an
     * migration task is done
     * 
     * @var integer 
     */
    private $taskNumber = 0;
    
    /**
     * Variable holds current migration context
     * 
     * @var string Either "up" or "down" 
     */
    private $mode;
    
    /**
     * Variable holds detail information from upgrade
     * @NOTICE: this is only filled in downgrade mode!
     *  
     * @var array|null 
     */
    protected $upgradeDetails = null;

    
    /**
     * This function is used to process the version's upgrade definition.
     * It also triggers a MigrationHistory flushing.
     */
    public function doUpgrade() {
        
        // Switch to upgrade mode
        $this->mode = "up";
        
        
        // @TODO: preUp();
        
        // 
        $this->up();
        
        // @TODO: postUp();
        
        
        // Flush MigrationHistory
        $this->migrationHistory->flush();
    }
    
    /**
     * This function is used to process the version's downgrade
     */
    public function doDowngrade() {
        
        // Switch to upgrade mode
        $this->mode = "down";
        
        // Load version task details and make accessible 
        $this->upgradeDetails = $this->getMigrationHistory()->getUpgradeTaskDetailsIndexed($this->getVersionId());
        
        
        // @TODO: preDown();
        
        // Downgrade
        $this->down();
        
        // @TODO: postDown();
        
        
        // Remove downgraded version from MigrationHistory
        $this->migrationHistory->removeVersionHistory($this->getVersionId());
        
        // Flush MigrationHistory
        $this->migrationHistory->flush();
    }
    
    /**
     * This function defines the upgrade of the specific version
     */
    abstract protected function up();
    
    /**
     * This function defines the downgrade of the specific version
     */
    abstract protected function down();
    
    /**
     * This function delegates this "create" call to a corresponding Migration 
     * Plugin, if such exists
     * 
     * @param string $type
     * @param array $options
     */
    public function create($type, $options) {
                
        // Load plugin specified by type
        $plugin = $this->migrationPluginManager->getPlugin($type);
                
        
        // Use the plugin's create function
        $historyData = $plugin->create($options);
                        
        
        // Only create version history in upgrade mode
        // @FIXME: also enable in downgrade mode?
        if($this->mode === "up") {

            // Enhance VersionHistory entry
            $historyData["type"] = $type;
            $historyData["number"] = $this->getNextTaskNumber();

            // Store history data as new task
            $this->migrationHistory->addTaskHistory($this->getVersionId(), $historyData);
        }
    }
        
    /**
     * This function delegates this "update" call to a corresponding Migration 
     * Plugin, if such exists
     * 
     * @param string $type
     * @param integer $identifier
     * @param array $options
     */
    public function update($type, $identifier, $options) {
        
        // 
        $plugin = $this->migrationPluginManager->getPlugin($type);
        

        // 
        $historyData = $plugin->update($identifier, $options);
        
        
        
        // Only create version history in upgrade mode
        // @FIXME: also enable in downgrade mode?
        if($this->mode === "up") {

            // Enhance VersionHistory entry
            $historyData["type"] = $type;
            $historyData["number"] = $this->getNextTaskNumber();

            // Store history data as new task
            $this->migrationHistory->addTaskHistory($this->getVersionId(), $historyData);
        }
    }
    
    /**
     * This function delegates this "delete" call to a corresponding Migration 
     * Plugin, if such exists
     * 
     * @param string $type
     * @param integer $identifier
     */
    public function delete($type, $identifier) {
        
        // 
        $plugin = $this->migrationPluginManager->getPlugin($type);
        
        
        // 
        $return = $plugin->delete($identifier);
        
        // @TODO: create aciton history in upgrade mode
        // ..but what data to remember?!
        
        
        // 
        return $return;
    }

    /**
     * This function returns the Version ID of this Version object
     * 
     * @return string
     */
    public function getVersionId() {
        
        return substr(get_class($this), 7);
    }
    
    /**
     * This function returns the next task number of this current version
     * 
     * @return integer
     */
    public function getNextTaskNumber() {
        
        // return next task number
        return ++$this->taskNumber;
    }
    
    /**
     * This function delegates to MigrationHistory object with current VersionId
     * 
     * @param integer $taskNumber
     * @return stdClass
     */
    protected function upgradeDetails($taskNumber) {
        
        // Get task details from MigrationHistory 
        return $this->getMigrationHistory()->getUpgradeTaskDetails($this->getVersionId(), $taskNumber);
    }
    
    /**
     * Setter function for PluginManager dependency
     * 
     * @param PimcoreMigration_MigrationPluginManager $pluginManager
     */
    public function setMigrationPluginManager($pluginManager) {
        
        $this->migrationPluginManager = $pluginManager;
    }
    
    /**
     * Setter function for MigrationHistory dependency
     * 
     * @param PimcoreMigration_MigrationHistory $migrationHistory
     */
    public function setMigrationHistory($migrationHistory) {
        
        $this->migrationHistory = $migrationHistory;
    }
    
    /**
     * This function returns reference to MigrationHistory object
     * 
     * @return PimcoreMigration_MigrationHistory
     */
    protected function getMigrationHistory() {
        
       return $this->migrationHistory; 
    }
}
