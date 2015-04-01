<?php
error_reporting(E_ALL);
ini_set('display_errors',0);
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="it-IT">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="author" content="Fabrizio Vettore/">

</head>

<body>

<?php
//if (isset($_SERVER["ALF_AVAILABLE"]) == false) {
    require_once "modules/Alfresco/Alfresco/config.php";
    require_once "modules/Alfresco/Alfresco/Alfresco_CMIS_API.php";
//}
ob_start();
// Specify the connection details
//$repositoryUrl = "http://192.168.100.106:8080/alfresco/api";
        class Alfresco_alfrescoupload_View extends Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
    global $repositoryUrl;
    global $folderPath;
global $current_user;
$userid = $current_user->id;
$error='';
if ($userid == null)
    header("location:index.php?module=Documents&view=List");

  $user = Alfresco_Record_Model::find($userid);
        if ($user != null) {
            $userName = $user['alfresco_username'];
            $password = $user['alfresco_password'];
            //$f=$user['alfresco_upload'];
            // Authenticate the user and create a session
            $repo = new CMISalfRepo($repositoryUrl, $userName, $password);
            $repo->connect($repositoryUrl, $userName, $password);
            if ($repo->connected != true) {
                header("location:index.php?module=Alfresco&view=alfrescologin");
            }
        }else{
            header("location:index.php?module=Alfresco&view=alfrescologin");
        }

if (isset($_REQUEST["create"]) == true || $_POST['alfresco_folder'] != '') {
    $user = Alfresco_Record_Model::find($userid);
    // print_r($user);
    $username = $user['alfresco_username'];
    $password = $user['alfresco_password'];
    $alfresco_folder = $user['alfresco_folder'];

    // $userName = 'erf';
    //$password = 'erf'; 
    $username1 = $alfresco_folder;
}

if ($alfresco_folder != '') {
    $folder = new CMISalfObject($repositoryUrl, $username, $password, null, null, $folderPath . $alfresco_folder);
    if (!$folder->loaded) {
        //die("\nSORRY! cannot open folder!\nThe last HTTP request returned the following status: " . $folder->lastHttpStatus . "\n\n");
        //else
        //Alfresco_Record_Model::createFolder($userid, $_REQUEST['alfresco_folder']);
//Load contained objects
        $folder = new CMISalfObject($repositoryUrl, $username, $password, null, null, $folderPath);
        $folder->createFolder($alfresco_folder);
        $dirname = 'alfresco_upload';
        if (!is_dir($dirname))
            mkdir($dirname);
        // header("location:index.php?module=Documents&view=List");
    }
}

if ($_POST['alfresco_folder'] != '') {
    $fold = $_POST['alfresco_folder'];
    $folder = new CMISalfObject($repositoryUrl, $username, $password, null, null, $folderPath . $fold);
    if (!$folder->loaded) {
        //die("\nSORRY! cannot open folder!\nThe last HTTP request returned the following status: " . $folder->lastHttpStatus . "\n\n");
        $folder = new CMISalfObject($repositoryUrl, $username, $password, null, null, $folderPath);
        $folder->createFolder($fold);
        Alfresco_Record_Model::createFolder($userid, $_REQUEST['alfresco_folder']);
        $dirname = 'alfresco_upload';
        if (!is_dir($dirname))
            mkdir($dirname);
        header("location:index.php?module=Documents&view=List");
    }else {
        $error= 'folder exist';
    }
}

// Update the property if the form has been posted with the correct details
if (isset($_REQUEST["create"]) == true && $_POST['alfresco_folder'] == '') {
    $upload = FALSE;
    $dirname = 'alfresco_upload';
    if (!is_dir($dirname))
        mkdir($dirname);
    $target_file = $dirname . '/' . basename($_FILES["fileToUpload"]["name"]);
    move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file);

    $path = $_FILES['fileToUpload']['name'];
    $ext = pathinfo($path, PATHINFO_EXTENSION);
    if($ext!=''){
    $noteName = $_REQUEST['name'] . '_' . rand(111111, 999999) ;
    $file=$noteName.'.' . $ext;
    }
    else{
    $file = $_REQUEST['name'] . '_' . rand(111111, 999999);
    $noteName=$file;
    }
    if(file_exists($target_file)){
        $fname=$file;
       // $file='alfresco_upload/'.$file;
    //rename($target_file, $file);
    $fileName = $target_file;
//unlink($fileName);+
//Load folder object
    $folderPath.=$alfresco_folder;
    $folder = new CMISalfObject($repositoryUrl, $username, $password, null, null, $folderPath);
    if (!$folder->loaded) {
        unlink($fileName);
        $error="\nSORRY! cannot open folder!\nThe last HTTP request returned the following status: " . $folder->lastHttpStatus . "\n\n";
    }

//Load contained objects
   // $folder->listContent();


//Check if the supplied path is a valid folder object (should be a cmis:folder type)
    //if ($folder->properties['cmis:baseTypeId'] <> 'cmis:folder')
     //   die("Not a valid FOLDER\n\n");

   
//PAY ATTENTION! file MUST be in your script folder!
//check if file exists
    if (!is_file($fileName))
        $error="File not found!!\n";

//upload
    $newDocId = $folder->upload($fileName,$fname);

//the above returns FALSE if object cannot be loaded
    if (!$newDocId) {
        unlink($fileName);
        $error= "\n\nSORRY! cannot upload file!\nThe last HTTP request returned the following status: " . $folder->lastHttpStatus . "\n\n";
    } else {
        $upload = true; //echo "UPLOADED new doc with ID=$newDocId\n\n";
    }

//relist folder contained objects
    //$folder->listContent();
    $name = $fname;
//echo "\n====================================\n";
//echo "folder Contained objects AFTER uploading:\n\n";
    //foreach ($folder->containedObjects as $obj) {
        //echo $obj->properties['cmis:name']."  (".$obj->properties['cmis:baseTypeId'].")\n";
       // if ($obj->properties['cmis:name'] == $fileName) {
           // $objid = $obj->properties['cmis:objectId'];

            $folder1 = new CMISalfObject($repositoryUrl, $username, $password, $newDocId);
            $folder1->setAspect('cm:title', $_REQUEST['title']);
            $folder1->setAspect('cm:description', $_REQUEST['description']);
            $uiUrl = $folder1->contentUrl;
        //}
   // }


    unlink($fileName);
    }else{
        //unlink($target_file);
    }
}
if (!isset($_POST['fold']) && $_POST['fold'] == '') {
?>

<html>

    <head>
        <title>Alfresco File Upload</title>
    </head>
    <style>
        table,td,tr
        {
            max-height: 400px;
           // color: #004488;
           // font-family: Tahoma, Arial, Helvetica, sans-serif;
            //font-size: 11px;
        }
    </style>
    <body>




        <form action="index.php"  method="post" enctype="multipart/form-data" id="alfresco_form">
            <input type=hidden name=create value="true">
            <input type=hidden name=module value="Alfresco">
            <input type=hidden name=view value="alfrescoupload">
            <table width="100%" height="80%" align="center">
                <tbody><tr width="100%" align="center">
                        <td valign="middle" align="center" width="100%">

                            <table cellspacing="0" cellpadding="0" border="0">
                                <tbody>
<?php
if (isset($_REQUEST['error'])) {
    
}
?>
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
<?php
$user = Alfresco_Record_Model::find($userid);

$alfresco_folder = $user['alfresco_folder'];
if ($alfresco_folder == '') {
    ?>
                                                        <tr>
                                                            <td colspan="2">
                                                                <span class="mainSubTitle">Enter Folder name:</span>
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td>
                                                                Folder Name:
                                                            </td>
                                                            <td>

                                                                <input name="alfresco_folder" type=edit id="alf_folder" size=50 style="width:150px">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td colspan="2" align="right">
                                                                <input type=button value="Cancel" onclick="window.history.back();" class="btn btn-block">
                                                                    <button onclick="" id="add_folder" class="btn btn-block">Add Folder</button>
                                                            </td>
                                                        </tr>
<?php } else { ?>




                                                        <tr>
                                                            <td colspan="2">
                                                                <span class="mainSubTitle">Enter File details:</span>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                Folder Name:
                                                            </td>
                                                            <td>

                                                                <input name="fold" type=edit id="alf_folder" value="<?php echo $alfresco_folder; ?>" size=50 style="width:150px" readonly="readonly">
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td>
                                                                File Name:
                                                            </td>
                                                            <td>

                                                                <input name="name" type=edit id="alf_name" size=50 style="width:150px">
                                                            </td>
                                                        </tr>

                                                        <tr>
                                                            <td>
                                                                Title:
                                                            </td>
                                                            <td>


                                                                <input name="title" type=edit value="" id="alf_title" size=50 style="width:150px">
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                Description:
                                                            </td>
                                                            <td>


                                                                <textarea name="description" type=edit id="alf_des" value="" rows="5" cols="5" style="width:240px"></textarea>
                                                            </td>
                                                        </tr>
                                                        <tr>
                                                            <td>
                                                                File:
                                                            </td>
                                                            <td>


                                                                <input name="fileToUpload" size="50" type="file" id="alfresco_file">
                                                            </td>
                                                        </tr>


                                                        <tr>
                                                            <td colspan="2" align="right">
                                                                <input type=button id="cancel" value="Cancel" onclick="window.history.back();" class="btn btn-block">
                                                                <button  id="alfresco_upload" class="btn btn-block">Upload To Alfresco</button>
                                                            </td>
                                                        </tr>

<?php } ?>

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
        <!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>-->

        <script>
                                                                    $("#alfresco_upload").click(function() {
                                                                        if ($('#alf_name').val() == "" || !(/^[A-Za-z0-9_@:/#+-]*$/).test(($('#alf_name').val()).trim())) {
                                                                            alert('Invalid file name');
                                                                            return false;
                                                                        }else if($('#alf_name').val().length<=3){
                                                                            alert('please enter at least 4 characters for file name');
                                                                            return false;
                                                                        }
                                                                        else if ($('#alfresco_file').val() == "") {
                                                                            alert('no file selected');
                                                                            return false;
                                                                        }
                                                                        
                                                                        
                                                                        //check whether browser fully supports all File API
                                                                            else if (window.File && window.FileReader && window.FileList && window.Blob)
                                                                               {
                                                                                //get the file size and file type from file input field
                                                                            var fsize = $('#alfresco_file')[0].files[0].size;
                
                                                                               if(fsize>2048576) //do something if file size more than 1 mb (1048576)
                                                                                 {
                                                                                 alert("max upload file size 2 mb!");
                                                                                   return false;
                                                                                  }
                                                                                     }//else {
                                                                           // alert('file selected');
                                                                            $('#alfresco_upload').hide();
                                                                            $('#cancel').hide();
                                                                           // document.getElementById('alfresco_form').submit();
                                                                             return true;
                                                                        //}
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

<?php
}
if ($_REQUEST["create"] == true && $_POST['alfresco_folder'] == '' && $upload == true) {
    //$repoObject->contentUrl;
    //$uiUrl = $folder->contentUrl;

    $db = PearDatabase::getInstance();
    $rs1 = $db->pquery("select max(crmid) as id from vtiger_crmentity");
    if ($row = $db->fetch_row($rs1)) {
        $crmid = $row['id'];
        $crmid = $crmid + 1;
    }
    $userid = $current_user->id;
    $rs2 = $db->pquery("insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime) VALUES('$crmid','$userid','$userid','Documents',NOW(),NOW())");

    //$filetype='application/vnd.oasis.opendocument.text';
    //      $zipfilename=$entityid.$filename;

    $rs11 = $db->pquery("select cur_id,prefix from vtiger_modentity_num where semodule='Documents' ");
    if ($row = $db->fetch_row($rs11)) {
        $note_no = $row['prefix'] . $row['cur_id'];
        $curid = $row['cur_id'] + 1;
    }
    $notecontent=$_REQUEST['description'];


    //$title=$originalFileName.'_'.$entityid;
    // $filesize=  filesize($wordtemplatedownloadpath.$entityid.$filename);
    $rs7 = $db->pquery("insert into vtiger_notes (notesid,note_no,title,filename,notecontent,filetype,filelocationtype,filestatus) VALUES('$crmid','$note_no','$name','$uiUrl','$notecontent','','E','1')");

    $rs8 = $db->pquery("insert into vtiger_notescf (notesid) VALUES ('$crmid') ");
    $rs12 = $db->pquery("update vtiger_modentity_num set cur_id='$curid' where semodule='Documents' ");

    // $crmid1=$crmid+1;
    //  $rs2 = $db->pquery("insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime) VALUES('$crmid1','$userid','$userid','Documents Attachment',NOW(),NOW())");
    $rs3 = $db->pquery("update vtiger_crmentity_seq SET id='$crmid' ");
    //creating relation with alfresco documents
    $newDocId=  strstr($newDocId, ';',TRUE);
    $rs8 = $db->pquery("insert into vtiger_alfrescodocuments (crmid, document_objectid, document_title) VALUES ('$crmid', '$newDocId', '$name') ");
    
    header("location:index.php?module=Documents&view=List");
}
if($error!='')
    echo "<script>alert('$error')</script>";
    }
        }
?>

