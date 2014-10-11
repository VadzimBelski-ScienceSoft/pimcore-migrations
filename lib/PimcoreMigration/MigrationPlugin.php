<?php

/**
 * Description of MigrationPlugin
 *
 * @author Achim Kramer <achim@zibra.de>
 */
abstract class PimcoreMigration_MigrationPlugin {
    
    /**
     * Reference to migration object 
     * 
     * @var PimcoreMigration_Migration 
     */
    private $migration;
    
    
    /**
     * Setter function for Migration object
     * 
     * @param PimcoreMigration_Migration $migration
     */
    public function setMigration($migration) {
        
        $this->migration = $migration;
    }
}
