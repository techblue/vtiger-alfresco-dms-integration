<?php 

                require_once "modules/Alfresco/Alfresco/config.php";
class Alfresco_Record_Model extends Vtiger_Base_Model {
    
        public function save($user,$pass) {
            global $current_user;
               $userid=$current_user->id;
                $db = PearDatabase::getInstance();
                $db->pquery('INSERT INTO vtiger_alfrescousers (userid, alfresco_username,alfresco_password) VALUES(?,?,?)', array(
                        $userid, $user,$pass
                ));
                return $db->getLastInsertID();
        }
        
        public function createFolder($user,$folder) {
              $db = PearDatabase::getInstance();
              //  $query='';
                $re=$db->pquery('UPDATE vtiger_alfrescousers  set alfresco_folder=? where userid=?', array(
                         $folder,$user
                ));
        }
        
         public function createSubFolder($user,$folder) {
              $db = PearDatabase::getInstance();
               $sql = "select alfresco_subfolder from vtiger_alfrescousers where userid=?";
        //$db = PearDatabase::getInstance();
                $rs = $db->pquery($sql, array($userid));
               // if ($db->num_rows($rs)) {
                        if ($row = $db->fetch_array($rs)) {
                                $subfolder=$row['alfresco_subfolder'];
                                $subfolder=$subfolder.','.$folder;
                                 $re=$db->pquery('UPDATE vtiger_alfrescousers  set alfresco_subfolder=? where userid=?', array(
                         $subfolder,$user
                ));
                        }
                else{
              //  $query='';
                $re=$db->pquery('UPDATE vtiger_alfrescousers  set alfresco_subfolder=? where userid=?', array(
                         $folder,$user
                ));
                }
        }
        
        public function update($user,$pass) {
            global $current_user;
               $userid=$current_user->id;
                $db = PearDatabase::getInstance();
              //  $query='';
                $re=$db->pquery('UPDATE vtiger_alfrescousers  set alfresco_username=?,alfresco_password=? where userid=?', array(
                         $user,$pass,$userid
                ));
                return $re;
        }
        
      
       
        static function find($userid) {
                $db = PearDatabase::getInstance();
                $instances = array();
                $sql = "select alfresco_username,alfresco_password,alfresco_folder from vtiger_alfrescousers where userid=?";
        //$db = PearDatabase::getInstance();
                $rs = $db->pquery($sql, array($userid));
                if ($db->num_rows($rs)) {
                        if ($data = $db->fetch_array($rs)) {
                                //$instances[] = new self($data);
                        }
                }
                return $data;
        }
}
