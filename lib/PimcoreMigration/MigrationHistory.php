<?php

/**
 * Description of VersionHistory
 *
 * @author Achim Kramer <achim@zibra.de>
 */
class PimcoreMigration_MigrationHistory {

    /**
     * Variable holds array with settings for this MigrationHistory
     * 
     * @var PimcoreMigration_Configuration 
     */
    private $migrationConfig;
        
    /**
     * Variable holds changes of the migration history
     * 
     * @var stdClass 
     */
    private $migrationHistory;
    
    
    /**
     * Reference to PimcoreMigration Configuration object
     * 
     * @param PimcoreMigration_Configuration $migrationConfig
     */
    public function __construct($migrationConfig) {
        
        // Extract settings from given configuration object
        $this->migrationConfig = $migrationConfig;
    }
    
    /**
     * This function defines the destruction of this object
     */
    public function __destruct() {
        
        // Flush changes
        $this->flush();
    }
    
    /**
     * This function adds the given history data into the migration history file
     * 
     * @param string $versionId
     * @param array $historyData
     */
    public function addTaskHistory($versionId, $historyData) {
        
        // Define checkvariable
        $inserted = false;
        
        // Find specified version id
        foreach($this->migrationHistory->migrations as $idx => $migrationEntry) {
            
            // Compare version ids
            if($migrationEntry->version == $versionId) {
                
                // insert task history data
                $this->migrationHistory->migrations[$idx]->tasks[] = (object)$historyData;
                
                // remember this insertion
                $inserted = true;
                
                // finish this loop
                break;
            }
        }
        
        // Version not found
        if(!$inserted) {
            
            // Create a new version entry
            // @NOTICE: must be of type stdClass!
            $this->migrationHistory->migrations[] = (object) array("version" => $versionId,
                                                                   "tasks" => array((object)$historyData));
        }
    }
    
    
    /**
     * This function removes specified version from migration history
     * 
     * @param string $versionId
     */
    public function removeVersionHistory($versionId) {
        
        // Find specified version id
        foreach($this->migrationHistory->migrations as $idx => $migrationEntry) {
            
            // compare versions
            if($migrationEntry->version === $versionId) {
                
                // 
                unset($this->migrationHistory->migrations[$idx]);
                
                //
                break;
            }
        }
    }
    
    /**
     * This function must be called to initialize MigrationHistory fully
     */
    public function activate() {
        
        // Read current migration history file data
        $this->migrationHistory = $this->readMigrationHistory();
    }
    
    /**
     * This function flushes the migration history changes to the file
     */
    public function flush() {
        
        // First check if migrationHistory was read previously
        if($this->migrationHistory) {
            
            // Update last update timestamp
            $this->migrationHistory->{"last-migration"} = time(); 
            
            // write migration history to file
            $this->writeMigrationHistory($this->migrationHistory);
        }
    }
    
    
    /**
     * This function returns an array with the already migrated version IDs
     * 
     * @return array Array with already migrated Version-IDs. Empty if no version
     * is migrated yet
     */
    public function getMigratedVersionIds() {
        
        // Initialize result set
        $migratedVersionIds = array();
        
        // Loop MigrationHistory
        foreach($this->migrationHistory->migrations as $migrationEntry) {
            
            // Put version into result array
            $migratedVersionIds[] = $migrationEntry->version;
        }
        
        // sort keys alphabetically
        ksort($migratedVersionIds);
        
        //
        return $migratedVersionIds;
    }
    
    /**
     * This function extracts the current version id from the migration history
     * file
     * 
     * @return string Returns string if there is a version, else "0"
     */
    public function getCurrentVersionId() {
        
        // Check if migrations already exist
        if(empty($this->migrationHistory->migrations)) {
            
            return "0";
        }
        
        // Get Version id of the last migration in the migrations array
        return end($this->migrationHistory->migrations)->version;
    }
    
    /**
     * This function extracts
     * 
     * @param type $versionId
     * @return stdClass
     * @throws \Exception Throws exception, if there is no data for the specified version id
     */
    public function getUpgradeVersionDetails($versionId) {
        
        // Search for migration with specified versionid
        foreach($this->migrationHistory->migrations as $migrationEntry) {
            
            // Extract migration entry if we found the specified one 
            if($migrationEntry->version === $versionId) {
                
                return $migrationEntry;
            }
        }
        
        // Throw exception
        throw new \Exception("There is no migration data for the specified version '". $versionId ."'.");
    }
    
    /**
     * This function extracts action Details from specified version and task number
     * 
     * @param string $versionId
     * @param integer|null $taskNumber
     * @return stdClass
     */
    public function getUpgradeTaskDetails($versionId, $taskNumber = null) {
        
        // Get version details 
        $versionDetails = $this->getUpgradeVersionDetails($versionId);
        
        // Return version details with each containing action, if actioNnumber is not specified
        if(!$taskNumber) {
            
            return $versionDetails;
        }
        
        
        // Extract specified task details
        foreach($versionDetails->tasks as $taskEntry) {
            
            // Compare action numbers
            if($taskEntry->number == $taskNumber) {
                
                // Return this action Entry
                return $taskEntry;
            }
        }
        
        // 
        throw new \Exception("Specified version and task-number combination does not have details.");
    }
    
    /**
     * This function returns an indexed array of the specified task details
     * 
     * @param string $versionId
     * @return array
     */
    public function getUpgradeTaskDetailsIndexed($versionId) {
        
        // Initialize result array
        $indexedArray = array("tasks" => array());
        
        // Get version details 
        $versionDetails = $this->getUpgradeVersionDetails($versionId);
        
        // Iterate task entries and create an index from the task number
        foreach($versionDetails->tasks as $taskEntry) {
            
            $indexedArray[$taskEntry->number] = $taskEntry;
        }
        
        // 
        return $indexedArray;
    }
    
    /**
     * This function creates an initial migration-history file
     */
    protected function createInitialMigrationHistory() {
        
        // Define Initial Migration history
        $initialMigrationHistory = array("type" => "migration-history",
                                         "last-migration" => null,
                                         "migrations" => array());
        
        // Write initial data into file
        $this->writeMigrationHistory($initialMigrationHistory);
        
        // Read new initial Migration History
        $this->migrationHistory = $this->readMigrationHistory();
    }
    
    /**
     * This function reads the persisted migration history file
     * 
     * @return mixed
     */
    protected function readMigrationHistory() {
        
        // Check first if migration history exists
        if(!file_exists($this->getMigrationHistoryFilename())) {
            
            // Create an initial migration history 
            $this->createInitialMigrationHistory();
        }
        
        // return data from migration history file
        return json_decode(file_get_contents($this->getMigrationHistoryFilename()));
    }
    
    /**
     * This function writes the given array data as json file to disk
     * 
     * @param array $migrationHistory
     */
    protected function writeMigrationHistory($migrationHistory) {
        
        // write give data to file
        file_put_contents($this->getMigrationHistoryFilename(), json_encode($migrationHistory));
    }
    
    /**
     * This function generates the MigrationHistory Filename partially specified 
     * by the Configuration object
     * 
     * @return string Generated filename
     */
    protected function getMigrationHistoryFilename() {
        
        // return generated MigrationHistory Filename
        return PIMCORE_DOCUMENT_ROOT ."/". trim($this->migrationConfig->getSetting("migrations-path"), "/") ."/" . $this->migrationConfig->getSetting("migration-history-filename");
    }
}
