<?php

/*
 * This is a CLI script to perform PimcoreMigration
 */

// Use Pimcore CLI startup routine
include_once(dirname(__FILE__) . "/../../../pimcore/cli/startup.php");

/*
 * Pimcore Migration Setup 
 */
// Define PimcoreMigration Config
$pimcoreMigrationConfig = new PimcoreMigration_Configuration(array("migrations-path" => "website/migrations"));

// Initialize Migration object
$pimcoreMigration = PimcoreMigration_Migration::getInstance($pimcoreMigrationConfig);


/*
 * Run migration
 */
// RuN!
$pimcoreMigration->migrate();


