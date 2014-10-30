# Pimcore Migration Plugin

## Description
This package is a Pimcore plugin designed to enable sophisticated data migration.
There is also the great migration library from Doctrine called Doctrine Migrations
which was an inspiration for this plugin. But Pimcore Migration is more designed 
to integrate deeply into Pimcore and connect with the internal functionalities.

## Architecture
Basic and core functionalities are implemented within Migration-class. This class 
provides the public API to use PimcoreMigration. Necessary for each Migration is 
an instance of Configuration-class to specify folders and other settings. 

A Migration requires Version definitions defined within Version-classes. Each Version
class has an up- and down-method, which is invoked in the corresponding migration context.

The MigrationPluginManager enables special MigrationPlugin support. PimcoreMigration 
is designed to have several MigtrationPlugin for every migration purpose. For example
one MigrationPlugin concentrates on the migration of Document_Page data. 

Changes are written into migration-histroy.json file.

## Functionalities
It is possible to migrate ...
- DocuementPage (not fully supported yet)

## Official MigrationPlugins
- DocumentPageMigration

## Usage
Do not forget to enable the PimcoreMigration Extension in Pimcore backend! This 
is necessary for correct Autoloading.

Run PimcoreMigration from Command Line. Navigate to /plugins/PimcoreMigration/bin
and call 'php pimcore-migration.php'

## Wishlist/Feature Requests
- Up/Down migration for Document_Pages 
- Migration locking with semaphore
- Migration Plugins
- Integrate with Events


1. Documents
   - from array definition
   - from serialized 

2. Objects
   - from array definition
   - from serialized object

3. Assets