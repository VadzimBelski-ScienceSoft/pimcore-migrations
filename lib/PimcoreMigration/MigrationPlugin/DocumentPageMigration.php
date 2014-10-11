<?php

/**
 * Description of DocumentPageMigration
 *
 * @author Achim Kramer <achim@zibra.de>
 */
class PimcoreMigration_MigrationPlugin_DocumentPageMigration extends PimcoreMigration_MigrationPlugin {
    
    /**
     * This function creates a new Document_Page with the specified data array
     * from the Migration version
     * 
     * @param array $data
     * @return array
     */
    public function create($data) {
        
        /*
         * Create a model object from $data
         */
        
        // 
        $documentPage = new Document_Page();
        
        // @FIXME: change into mapping?! parse array generically?
        if(isset($data["key"])) { $documentPage->setKey($data["key"]); }
        //
        if(isset($data["title"])) { $documentPage->setTitle($data["title"]); }
        if(isset($data["description"])) { $documentPage->setDescription($data["description"]); }
        //
        if(isset($data["parentId"])) { $documentPage->setParentId($data["parentId"]); }
        if(isset($data["published"])) { $documentPage->setPublished($data["published"]); }
        //
        // ..@TODO: finish extraction
        
        
        // Set data defaults
        $documentPage->setId(null);
        $documentPage->setCreationDate(time());
        
        
        // Save 
        $documentPage->save();
        
        // Return data for migration history
        return array("action" => "create",
                     "data" => array("id" => $documentPage->getId()));
    }
        
    /**
     * This function updates the specified Document_Page with the given data
     * 
     * @param integer $identifier
     * @param array $data
     * @return array
     */
    public function update($identifier, $data) {
        
        // Initialize return array
        $return = array("action" => "update",
                        "data" => array("id" => $identifier));
        
        
        // Load specified document page
        $documentPage = Document_Page::getById($identifier);
        
        // @FIXME: change into mapping?! parse array generically?
        if(isset($data["key"])) { 
            
            // Remember previous value
            $return["data"]["key"] = $documentPage->getKey();
            
            // Change
            $documentPage->setKey($data["key"]);         
        }
        //
        if(isset($data["title"])) { 
            
            // Remember previous value
            $return["data"]["title"] = $documentPage->getTitle();
            
            // Change
            $documentPage->setTitle($data["title"]); 
        }
        if(isset($data["description"])) { 
            
            // Remember previous value
            $return["data"]["description"] = $documentPage->getDescription();
            
            // Change
            $documentPage->setDescription($data["description"]); 
        }
        //
        if(isset($data["parentId"])) { 
            
            // Remember previous value
            $return["data"]["parentId"] = $documentPage->getParentId();
            
            // Change
            $documentPage->setParentId($data["parentId"]); 
        }
        if(isset($data["published"])) { 
            
            // Remember previous value
            $return["data"]["published"] = $documentPage->getPublished();
            
            //
            $documentPage->setPublished($data["published"]); }
        //
        // ..@TODO: finish extraction
            
        
        // Change modification params
        $documentPage->setUserModification(0);
        $documentPage->setModificationDate(time());
            
        // Save update changes
        $documentPage->save();
        
        //
        return $return;
    }
    
    /**
     * This function deletes a Document_Page with the specfied identifier
     * 
     * @param integer $identifier
     */
    public function delete($identifier) {
        
        // Load document page
        $documentPage = Document_Page::getById($identifier);
        
        // Check first if Document_Page does exist
        if($documentPage) {
            
            // Detele this document
            $documentPage->delete();
        }
        
        // Return data for migration history
        return array("action" => "deleted",
                     "data" => array("id" => $identifier));
    }    
}
