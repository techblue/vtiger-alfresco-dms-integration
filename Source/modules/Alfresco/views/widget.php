<?php
error_reporting(E_ALL);
ini_set('display_errors',0);
require_once "modules/Alfresco/Alfresco/config.php";
require_once "modules/Alfresco/Alfresco/Alfresco_CMIS_API.php";
//session_start();

class Alfresco_widget_View extends Vtiger_Index_View {

    function process(Vtiger_Request $request) {

        global $current_user;
        $userid = $current_user->id;
        if ($userid == null)
            header("location:index.php?module=Documents&view=List");

        $user = Alfresco_Record_Model::find($userid);

        if ($user != null) {


            echo'<br>';
            echo'&nbsp;&nbsp;&nbsp;<a href="index.php?module=Alfresco&view=alfrescologin&req=1"  class="btn btn-primary" style="background-color:#ffffff;font-weight:bold;"> <font color="#333333"><i class="icon-plus"></i> Upload To Alfresco</font> <img src="modules/Alfresco/Alfresco/Common/Images/AlfrescoLogo32.png" width=15 height=30></a>';
            echo'<br>';
            echo'<br>';
            echo'&nbsp;&nbsp;&nbsp;<a href="index.php?module=Alfresco&view=selectfromalfresco&req=2" class="btn btn-primary" style="background-color:#ffffff;font-weight:bold;"> <font color="#333333"><i class="icon-plus"></i> Select From Folder </font><img src="modules/Alfresco/Alfresco/Common/Images/AlfrescoLogo32.png" width=15 height=30></a>';
            echo'<br>';
            echo'<br>';
            echo'&nbsp;&nbsp;&nbsp;<a href="index.php?module=Alfresco&view=searchAlfresco&req=3" class="btn btn-primary" style="background-color:#ffffff;font-weight:bold;"> <font color="#333333"><i class="icon-plus"></i> Search From Alfresco </font><img src="modules/Alfresco/Alfresco/Common/Images/AlfrescoLogo32.png" width=15 height=30></a>';
        echo'<br>';echo'<br>';
            
        } else {
            echo'<br>';
            echo'<a href="index.php?module=Alfresco&view=alfrescologin"  class="btn btn-primary" style="background-color:#ffffff;font-weight:bold;border:1px solid #ccc;"> <font color="#333333">Authorise From Alfresco</font> <img src="modules/Alfresco/Alfresco/Common/Images/AlfrescoLogo32.png" width=15 height=30></a>';
        }
    }

}
