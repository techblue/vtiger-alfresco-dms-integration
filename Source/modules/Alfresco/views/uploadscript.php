<?php
error_reporting(E_ALL);
ini_set('display_errors',0);
ini_set('max_execution_time', 3600);

require_once "include/database/PearDatabase.php";
require_once "modules/Alfresco/Alfresco/config.php";
require_once "modules/Alfresco/Alfresco/Alfresco_CMIS_API.php";

require_once ('modules/Alfresco/Alfresco/cmis_repository_wrapper.php');
require_once ('modules/Alfresco/Alfresco/cmis_service.php');
//}
// Specify the connection details
//$repositoryUrl = "http://192.168.100.106:8080/alfresco/api";
global $current_user; //global $adb;
$userid = $current_user->id;
if ($userid == null)
    header("location:index.php?module=Documents&view=List");
//if (isset($_REQUEST["create"]) == true || $_POST['alfresco_folder'] != '') {
$user = Alfresco_Record_Model::find($userid);
if ($user != null) {
// print_r($user);
$username = $user['alfresco_username'];
$password = $user['alfresco_password'];
$alfresco_folder = $user['alfresco_folder'];

            //$f=$user['alfresco_upload'];
            // Authenticate the user and create a session
            $repo = new CMISalfRepo($repositoryUrl, $username, $password);
            $repo->connect($repositoryUrl, $username, $password);
            if ($repo->connected == true) {
            }else{
                header("location:index.php?module=Alfresco&view=alfrescologin");
            }
        }else{
             header("location:index.php?module=Alfresco&view=alfrescologin");
        }




if ($_REQUEST['alfresco_folder'] != '') {
    $fold = $_POST['alfresco_folder'];
    $folder = new CMISalfObject($repositoryUrl, $username, $password, null, null, $folderPath . $fold);
    if (!$folder->loaded) {
        //die("\nSORRY! cannot open folder!\nThe last HTTP request returned the following status: " . $folder->lastHttpStatus . "\n\n");
        $folder = new CMISalfObject($repositoryUrl, $username, $password, null, null, $folderPath);
        $folder->createFolder($fold);
        //Alfresco_Record_Model::createFolder($userid, $_REQUEST['alfresco_folder']);
        $dirname = 'alfresco_upload';
        if (!is_dir($dirname))
            mkdir($dirname);
       // header("location:index.php?module=Documents&view=List");
   


        // $userName = 'erf';
        //$password = 'erf'; 
        //$username1 = $alfresco_folder;
        //Load folder object
        $folderPath.=$_REQUEST['alfresco_folder'];
        $folder = new CMISalfObject($repositoryUrl, $username, $password, null, null, $folderPath);
        if (!$folder->loaded) {
            //unlink($fileName);
            die("\nSORRY! cannot open folder!\nThe last HTTP request returned the following status: " . $folder->lastHttpStatus . "\n\n");
        }

        //Load contained objects
       // $folder->listContent();


//Check if the supplied path is a valid folder object (should be a cmis:folder type)
       // if ($folder->properties['cmis:baseTypeId'] <> 'cmis:folder')
         //   die("Not a valid FOLDER\n\n");


//}
//echo 'hello';
        $db = PearDatabase::getInstance();
        //$instances = array();
        $sql = "select attachmentsid,name,path from vtiger_attachments ";
        //$db = PearDatabase::getInstance();
        $rs = $db->pquery($sql);
        // if ($db->num_rows($rs)) {
        $i = 0;
        echo 'Uploaded documents:<br>';
        while ($row = $db->fetch_array($rs)) {
            //$instances[] = new self($data);
           // if ($i < 1000) {
              //  $i++;

                $upload = FALSE;
                $dirname = $row['path'];
                $fname = $row['attachmentsid'] . '_' . html_entity_decode($row['name']);
                //$fname=  str_replace('*', '', $fname);
                if (!is_dir('vtiger_alfresco_upload'))
                    mkdir('vtiger_alfresco_upload');
                $target_file = $dirname . $fname; //echo $target_file;
                //copy($target_file, 'vtiger_alfresco_upload/' . $fname);
               // $rfile = 'vtiger_alfresco_upload/' . $fname;

                if (file_exists($target_file)) {

                    //  $path = $_FILES['fileToUpload']['name'];
                    $ext = pathinfo($target_file, PATHINFO_EXTENSION);
                    
                    //remove * from file name
                    $fname=  str_replace('*', '', $fname);

                    if ($ext != '') {
                        $bname = basename($fname, '.' . $ext);
                        $noteName = $bname . '_' . rand(111111, 999999);
                        $file = $noteName . '.' . $ext;
                    } else {
                        $file = $fname . '_' . rand(111111, 999999);
                        $noteName = $file;
                    }
                    
                    if(!file_exists($file)){
                        $flname=$file;
                       // $file='vtiger_alfresco_upload/'.$file;
                   // rename($rfile, $file);
                    $fileName = $target_file;
//unlink($fileName);


                    echo $file . '<br>';
//PAY ATTENTION! file MUST be in your script folder!
//check if file exists
                    if (!is_file($fileName))
                        die("File not found!!\n");

//upload
                    $newDocId = $folder->upload($fileName,$flname);

//the above returns FALSE if object cannot be loaded
                    if (!$newDocId) {
                        //($fileName);
                        echo "\n\nSORRY! cannot upload file!\nThe last HTTP request returned the following status: " . $folder->lastHttpStatus . "\n\n";
                    } else {
                        $upload = true; //echo "UPLOADED new doc with ID=$newDocId\n\n";
                    }
/*
//relist folder contained objects
                    $folder->listContent();
                    $name = $fileName;
//echo "\n====================================\n";
//echo "folder Contained objects AFTER uploading:\n\n";
                    foreach ($folder->containedObjects as $obj) {
                        //echo $obj->properties['cmis:name']."  (".$obj->properties['cmis:baseTypeId'].")\n";
                        if ($obj->properties['cmis:name'] == $fileName) {
                            $objid = $obj->properties['cmis:objectId'];

                            $folder1 = new CMISalfObject($repositoryUrl, $username, $password, $objid);
                            $folder1->setAspect('cm:title', 'vtiger_documents');
                            $folder1->setAspect('cm:description', 'documents uploaded from vtiger');
                            $uiUrl = $folder1->contentUrl;
                        }
                    }
*/

                    }else{
                        //unlink($rfile);
                    }
                } else {
                    echo 'file not found';
                }
            //}
        }
    } else {
        echo 'folder exists';
    }
}
if($upload==FALSE){
?>

<html>

    <head>
        <title>Alfresco </title>
    </head>
    <style>
        td,tr,p,div
        {
            color: #004488;
            font-family: Tahoma, Arial, Helvetica, sans-serif;
            font-size: 11px;
        }
    </style>
    <body>




        <form action="index.php"  method="post" enctype="multipart/form-data" id="alfresco_form">
            <input type=hidden name=create value="true">
            <input type=hidden name=module value="Alfresco">
            <input type=hidden name=view value="uploadscript">
            <table width="100%" height="98%" align="center">
                <tbody><tr width="100%" align="center">
                        <td valign="middle" align="center" width="100%">

                            <table cellspacing="0" cellpadding="0" border="0">
                                <tbody>

                                    <tr><td width="7"><img src="modules/Alfresco/Alfresco/Common/Images/white_01.gif" width="7" height="7" alt=""></td>
                                        <td background="modules/Alfresco/Alfresco/Common/Images/white_02.gif">
                                            <img src="modules/Alfresco/Alfresco/Common/Images/white_02.gif" width="7" height="7" alt=""></td>
                                        <td width="7"><img src="modules/Alfresco/Alfresco/Common/Images/white_03.gif" width="7" height="7" alt=""></td>
                                    </tr>
                                    <tr><td background="modules/Alfresco/Alfresco/Common/Images/white_04.gif">
                                            <img src="modules/Alfresco/Alfresco/Common/Images/white_04.gif" width="7" height="7" alt=""></td><td bgcolor="white">

                                            <table border="0" cellspacing="4" cellpadding="2">
                                                <tbody><tr>
                                                        <td colspan="2">
                                                            <img src="modules/Alfresco/Alfresco/Common/Images/AlfrescoLogo200.png" width="200" height="58" alt="Alfresco" title="Alfresco">
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="2">
                                                            <span class="mainSubTitle">Create Folder to upload all documents:</span>
                                                        </td>
                                                    </tr>

                                                    <tr><?php
//$user = Alfresco_Record_Model::find($userid);

//$alfresco_folder = $user['alfresco_folder'];
?>
                                                        <td>
                                                            Folder Name:
                                                        </td>
                                                        <td>

                                                            <input name="alfresco_folder" value="" type=edit id="alf_folder" size=50 style="width:150px">
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td colspan="2" align="right">
                                                            <input type=button value="Cancel" onclick="window.history.back();">
                                                            <button onclick="" id="add_folder" >Create And Upload</button>
                                                        </td>
                                                    </tr>


                                                    <tr>
                                                        <td colspan="2">


                                                        </td>
                                                    </tr>
                                                </tbody></table>

                                        </td><td background="modules/Alfresco/Alfresco/Common/Images/white_06.gif">
                                            <img src="modules/Alfresco/Alfresco/Common/Images/white_06.gif" width="7" height="7" alt=""></td></tr>
                                    <tr><td width="7"><img src="modules/Alfresco/Alfresco/Common/Images/white_07.gif" width="7" height="7" alt=""></td>
                                        <td background="modules/Alfresco/Alfresco/Common/Images/white_08.gif">
                                            <img src="modules/Alfresco/Alfresco/Common/Images/white_08.gif" width="7" height="7" alt=""></td>
                                        <td width="7"><img src="modules/Alfresco/Alfresco/Common/Images/white_09.gif" width="7" height="7" alt=""></td></tr>
                                </tbody></table>

                            <div id="no-cookies" style="display:none">
                                <table cellpadding="0" cellspacing="0" border="0" style="padding-top:16px;">
                                    <tbody><tr>
                                            <td>
                                                <table cellspacing="0" cellpadding="0" style="border-width: 0px; width: 100%"><tbody><tr><td style="width: 7px;"><img src="/alfresco/images/parts/yellowInner_01.gif" width="7" height="7" alt=""></td><td style="background-image: url(/alfresco/images/parts/yellowInner_02.gif)"><img src="/alfresco/images/parts/yellowInner_02.gif" width="7" height="7" alt=""></td><td style="width: 7px;"><img src="/alfresco/images/parts/yellowInner_03.gif" width="7" height="7" alt=""></td></tr><tr><td style="background-image: url(/alfresco/images/parts/yellowInner_04.gif)"><img src="/alfresco/images/parts/yellowInner_04.gif" width="7" height="7" alt=""></td><td style="background-color:#ffffcc;">
                                                                <table cellpadding="0" cellspacing="0" border="0">
                                                                    <tbody><tr>
                                                                            <td valign="top" style="padding-top:2px" width="20"><img src="modules/Alfresco/Alfresco/Common/Images/info_icon.gif" height="16" width="16"></td>
                                                                            <td class="mainSubText">
                                                                                Cookies must be enabled in your browser for the Alfresco Web-Client to function correctly.
                                                                            </td>
                                                                        </tr>
                                                                    </tbody></table>
                                                            </td><td style="background-image: url(/alfresco/images/parts/yellowInner_06.gif)"><img src="/alfresco/images/parts/yellowInner_06.gif" width="7" height="7" alt=""></td></tr><tr><td style="width: 7px;"><img src="/alfresco/images/parts/yellowInner_07.gif" width="7" height="7" alt=""></td><td style="background-image: url(/alfresco/images/parts/yellowInner_08.gif)"><img src="/alfresco/images/parts/yellowInner_08.gif" width="7" height="7" alt=""></td><td style="width: 7px;"><img src="/alfresco/images/parts/yellowInner_09.gif" width="7" height="7" alt=""></td></tr></tbody></table>
                                            </td>
                                        </tr>
                                    </tbody></table>
                            </div>


                        </td>
                    </tr>

                </tbody></table></form>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>

        <script>
                                                                $("#alfresco_upload").click(function() {
                                                                    if ($('#alf_name').val() == "" || !(/^[A-Za-z0-9_-]*$/).test(($('#alf_name').val()).trim())) {
                                                                        alert('Invalid file name');
                                                                        return false;
                                                                    } else if ($('#alfresco_file').val() == "") {
                                                                        alert('no file selected');
                                                                        return false;
                                                                    }
                                                                    else {
                                                                        //alert('file selected');
                                                                        $('#alfresco_upload').hide();
                                                                        $('#cancel').hide();
                                                                        document.getElementById('alfresco_form').submit();
                                                                        // return true;
                                                                    }
                                                                });

                                                                $("#add_folder").click(function() {
                                                                    if ($('#alf_folder').val() == "") {
                                                                        alert('enter folder name');
                                                                        return false;
                                                                    }
                                                                    // else   if($('#alf_name').val()!='' && (/^[ A-Z a-z0-9_-]*$/).test($('#alf_name').val())){
                                                                    //  alert('Invalid name');
                                                                    //return false;
                                                                    // }
                                                                    else {
                                                                        //alert('file selected');

                                                                        document.getElementById('alfresco_form').submit();
                                                                        // return true;
                                                                    }
                                                                });
        </script>


    </body>

</html>
<?php  } ?>
