# Pimcore Migration Extention

## Description

This package is a Pimcore Extension designed to enable sophisticated data migration.
There is also the great migration library from Doctrine called Doctrine Migrations.
Pimcore Migrations is very inspired by this package, but is designed to integrate 
more deeply into the Pimcore and connect with the Pimcore internals.

## Architecture

Pimcore Migration integrates with pimcore's internal Webservice functionalities and 
provides an additional conceptual channel "migration files". This is why PimcoreMigration
depends strictly to certain Pimcore versions.

## Functionalities

It is possible to migrate ...

[] 1. Documents
[]    - from array definition
[]    - from serialized 

[] 2. Objects
[]    - from array definition
[]    - from serialized object

[] 3. Assets


Changes are written into changelog.json file. 


## Usage

Do not forget to enable the PimcoreMigration Extension in Pimcore backend! This 
is necessary for correct Autoloading


## TODOs/Features

- Up/Down migration for Document_Pages 
- Migration locking with semaphore
- Migration Plugins
- Integrate with Events
