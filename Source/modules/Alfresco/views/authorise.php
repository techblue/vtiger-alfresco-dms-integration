<?php

error_reporting(E_ALL);
ini_set('display_errors',0);
ob_start();
class Alfresco_authorise_View extends Vtiger_Index_View {

  public function process(Vtiger_Request $request) {
//global $path;
global $current_user;
$userid = $current_user->id;


    require_once ('modules/Alfresco/Alfresco/cmis_repository_wrapper.php');
    require_once ('modules/Alfresco/Alfresco/cmis_service.php');
    require_once "modules/Alfresco/Alfresco/config.php";
    require_once "modules/Alfresco/Alfresco/Alfresco_CMIS_API.php";


require_once "modules/Alfresco/Alfresco/config.php";


if (isset($_SESSION) == false) {
    // Start the session
    session_start();
}

if (isset($_REQUEST['login'])) {
    $userName = $_REQUEST['alfresco_username'];
    $password = $_REQUEST['alfresco_password'];
    $user = Alfresco_Record_Model::find($userid);

     if ($user != null) {
         $passupdate='true';
     }
   
    

    // Create the session
    $repo = new CMISalfRepo($repositoryUrl, $userName, $password);
    $repo->connect($repositoryUrl, $userName, $password);
    $create=true;
     if ($repo->connected == true) {
     $client = new CMISService($repositoryUrl, $userName, $password);
   // print "Connected\n";
    $myfolder = $client->getObjectByPath('/');
    $per = $client->getAllowableActions($myfolder->id);

   
    if ($per['canCreateFolder']){
       // echo 'can create';
        $create=true;
   }
    else{
       // header("location:index.php?module=Alfresco&view=alfrescologin&error=5");
        $create=false;
        //echo 'you do not have permission to create folder in root, please select different user';
    }
    }
    if($create==true){
    if ($repo->connected != true) {
        header("location:index.php?module=Alfresco&view=alfrescologin&error=1");
    } elseif ($passupdate == 'true') {
        $user = Alfresco_Record_Model::update($userName, $password);

        header("location:index.php?module=Documents&view=List");
    } else {
        $user = Alfresco_Record_Model::save($userName, $password);
       
        header("location:index.php?module=Documents&view=List");
    }
    }else{
        header("location:index.php?module=Alfresco&view=alfrescologin&error=5");
    }
    
} else {
    //$userid = $current_user->id;
    //$user = Alfresco_Record_Model::find($userid);
//print_r($user);
//header("location:index.php?module=Documents&view=List");
//if ($user == null) {
    header("location:index.php?module=Alfresco&view=alfrescoupload");
//}
    
}

}
}