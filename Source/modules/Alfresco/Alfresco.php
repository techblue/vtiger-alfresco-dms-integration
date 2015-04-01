<?php

class Alfresco {

    public function vtlib_handler($moduleName, $eventType) {
        if ($eventType == 'module.postinstall') {
            Vtiger_Utils::CreateTable('vtiger_alfrescousers', '(id INTEGER NOT NULL AUTO_INCREMENT, userid INTEGER, alfresco_username VARCHAR(255), alfresco_password VARCHAR(50),alfresco_folder tinytext,url tinytext, alfresco_subfolder tinytext, PRIMARY KEY (id),CONSTRAINT vtiger_alfrescousers FOREIGN KEY (userid) REFERENCES vtiger_users (id) ON DELETE CASCADE)');
            Vtiger_Utils::CreateTable('vtiger_alfrescodocuments', '(id INTEGER NOT NULL AUTO_INCREMENT,crmid INTEGER DEFAULT NULL,document_objectid varchar(1000) DEFAULT NULL,document_title varchar(1000) DEFAULT NULL,PRIMARY KEY (id),CONSTRAINT vtiger_alfrescodocuments FOREIGN KEY (crmid) REFERENCES vtiger_crmentity (crmid) ON DELETE CASCADE ON UPDATE CASCADE)');
            Vtiger_Utils::AlterTable('vtiger_notes', ' CHANGE filename filename VARCHAR(1000)');
            Vtiger_Utils::AlterTable('vtiger_notes', ' CHANGE title title VARCHAR(500)');
            $this->_registerLinks($moduleName);
        } else if ($eventType == 'module.enabled') {
            $this->_registerLinks($moduleName);
           // Vtiger_Utils::CreateTable('vtiger_alfrescodocuments', '(id INTEGER NOT NULL AUTO_INCREMENT,crmid INTEGER DEFAULT NULL,document_objectid varchar(1000) DEFAULT NULL,document_title varchar(1000) DEFAULT NULL,PRIMARY KEY (id),CONSTRAINT vtiger_alfrescodocuments FOREIGN KEY (crmid) REFERENCES vtiger_crmentity (crmid) ON DELETE CASCADE ON UPDATE CASCADE)');
           // Vtiger_Utils::AlterTable('vtiger_notes', ' CHANGE filename filename VARCHAR(1000)');
           // Vtiger_Utils::AlterTable('vtiger_notes', ' CHANGE title title VARCHAR(500)');
          //  Vtiger_Utils::CreateTable('vtiger_alfrescousers', '(id INTEGER NOT NULL AUTO_INCREMENT, userid INTEGER, alfresco_username VARCHAR(255), alfresco_password VARCHAR(50),alfresco_folder tinytext,url tinytext, alfresco_subfolder tinytext, PRIMARY KEY (id),CONSTRAINT vtiger_alfrescousers FOREIGN KEY (userid) REFERENCES vtiger_users (id) ON DELETE CASCADE)');
        } else if ($eventType == 'module.disabled') {
            $this->_deregisterLinks($moduleName);
        } else {
            $this->_registerLinks($moduleName);
        }
    }

    protected function _registerLinks($moduleName) {
        $thisModuleInstance = Vtiger_Module::getInstance($moduleName);
        if ($thisModuleInstance) {
            //$thisModuleInstance->addLink("HEADERSCRIPT", "MailMerge", "modules/MailMerge/js/Merge.js");
            //$thisModuleInstance->addLink("HEADERSCRIPT", "MailMerge", "layouts/vlayout/modules/MailMerge/resources/MyRss.js");
            // $thisModuleInstance->addLink("HEADERSCRIPT", "MailMerge", "layouts/vlayout/modules/MailMerge/resources/jquery.rss.min.js");

            $leadsModuleInstance = Vtiger_Module::getInstance('Documents');
            //$leadsModuleInstance->addLink("LISTVIEWBASIC", "Authorise Alfresco", 'index.php?module=Alfresco&view=alfrescologin','Dropbox.png',0);
            $leadsModuleInstance->addLink("LISTVIEWSIDEBARWIDGET", "Alfresco", 'module=Alfresco&view=widget', 'modules/Alfresco/Alfresco/Common/Images/AlfrescoLogo32.png');
            $leadsModuleInstance->addLink("DETAILVIEWSIDEBARWIDGET", "Alfresco", 'module=Alfresco&view=widget', 'modules/Alfresco/Alfresco/Common/Images/AlfrescoLogo32.png');
            // $leadsModuleInstance->addLink("LISTVIEWBASIC", "Select from alfresco", 'index.php?module=Alfresco&view=selectfromalfresco', 'Dropbox.png', 4);
            $leadsModuleInstance1 = Vtiger_Module::getInstance('Alfresco');
            //$leadsModuleInstance->addLink("LISTVIEWBASIC", "Authorise Alfresco", 'index.php?module=Alfresco&view=alfrescologin','Dropbox.png',0);
            $leadsModuleInstance1->addLink("SIDEBARWIDGET", "Alfresco", 'module=Alfresco&view=widget', 'modules/Alfresco/Alfresco/Common/Images/AlfrescoLogo32.png',1);
            
        }
    }

    protected function _deregisterLinks($moduleName) {
        $thisModuleInstance = Vtiger_Module::getInstance($moduleName);
        if ($thisModuleInstance) {
            //$thisModuleInstance->deleteLink("HEADERSCRIPT", "MailMerge", "modules/MailMerge/js/Merge.js");

            $leadsModuleInstance1 = Vtiger_Module::getInstance('Alfresco');
            $leadsModuleInstance1->deleteLink("SIDEBARWIDGET", "Alfresco");

            $leadsModuleInstance = Vtiger_Module::getInstance('Documents');
            $leadsModuleInstance->deleteLink("LISTVIEWSIDEBARWIDGET", "Alfresco");
            $leadsModuleInstance->deleteLink("DETAILVIEWSIDEBARWIDGET", "Alfresco");
            $leadsModuleInstance->deleteLink("LISTVIEWBASIC", "Upload to alfresco");
            $leadsModuleInstance->deleteLink("LISTVIEWBASIC", "Select from alfresco");
        }
    }

}
