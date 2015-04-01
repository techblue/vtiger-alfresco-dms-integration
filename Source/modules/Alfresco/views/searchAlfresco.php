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

require_once ('modules/Alfresco/Alfresco/cmis_repository_wrapper.php');
require_once ('modules/Alfresco/Alfresco/cmis_service.php');
require_once ('modules/Alfresco/Alfresco/config.php');
require_once "modules/Alfresco/Alfresco/Alfresco_CMIS_API.php";
 ob_start();
     class Alfresco_searchAlfresco_View extends Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
    global $current_user;
    global $repositoryUrl;
    global $folderPath;
    global $limit_doc;
$userid = $current_user->id;
if ($userid == null)
    header("location:index.php?module=Documents&view=List");
$user = Alfresco_Record_Model::find($userid);
if($user==null)
    header('location:index.php?module=Alfresco&view=alfrescologin');

// print_r($user);
$username = $user['alfresco_username'];
$password = $user['alfresco_password'];
$alfresco_folder = $user['alfresco_folder'];
//if($alfresco_folder=='')
   // header('location:index.php?module=Alfresco&view=alfrescologin');
 $repo = new CMISalfRepo($repositoryUrl, $username, $password);
    $repo->connect($repositoryUrl, $username, $password);
    if ($repo->connected != true) {
        header("location:index.php?module=Alfresco&view=alfrescologin");
    }
if (isset($_REQUEST["search"]) && $_REQUEST['search']!='') {

$repo_url = $repositoryUrl;
$repo_username = $username;
$repo_password = $password;
$repo_search_text = $_REQUEST['search'];
//$repo_debug = $_SERVER["argv"][5];

$client = new CMISService($repo_url, $repo_username, $repo_password);

//if ($repo_debug) {
    //print "Repository Information:\n===========================================\n";
   // print_r($client->workspace);
   // print "\n===========================================\n\n";
//}
$query="SELECT cmis:name,cmis:objectId,cmis:baseTypeId,cmis:objectTypeId FROM cmis:document WHERE cmis:name LIKE '%$repo_search_text%' OR CONTAINS('cmis:name:$repo_search_text')";
//$query = sprintf("SELECT * from cmis:document WHERE CONTAINS('cmis:name:%s')", $repo_search_text);
    if ($_REQUEST['pageS'] == '' || $_REQUEST['pageS'] < 0)
        $pageD = 0;
    else
        $pageD = $_REQUEST['pageS'];
    $skipD = $pageD * $limit_doc;


    $default_hash_values = array(
        "includeAllowableActions" => "true",
        "searchAllVersions" => "false",
        "maxItems" => $limit_doc,
        "skipCount" => $skipD
    );
    $objs = $client->query($query,array(),$default_hash_values);
    
    //check limit
 
                $default_hash_values_document_c = array(
                    "includeAllowableActions" => "true",
                    "searchAllVersions" => "false",
                    "maxItems" => 1,
                    "skipCount" => $skipD + $limit_doc
                );

                $query3="SELECT cmis:name,cmis:objectId,cmis:baseTypeId,cmis:objectTypeId FROM cmis:document WHERE cmis:name LIKE '%$repo_search_text%' OR CONTAINS('cmis:name:$repo_search_text') order by cmis:lastModificationDate DESC";
                $objs3 = $client->query($query3, array(), $default_hash_values_document_c);

                $found_d = TRUE;
           
                if ($objs3->objectList == null)
                    $found_d = FALSE;


    
    
//if ($repo_debug) {
    //print "Returned Objects\n:\n===========================================\n";
    //print_r($objs);
    //echo'<br>';
   // print "\n===========================================\n\n";
//}
}

  if ($_REQUEST["create"] != true) {
?>
<html>
    <head>
         
        
        
        <title>Browse Alfresco Repository</title>
        <style>/*
            body {font-family: verdana; font-size: 8pt;}
            tr {font-family: verdana; font-size: 8pt;}
            td {font-family: verdana; font-size: 8pt;}
            input {font-family: verdana; font-size: 8pt;}
            .maintitle {font-family: verdana; font-size: 10pt; font-weight: bold; padding-bottom: 15px;}
            a:link, a:visited
            {
                font-size: 11px;
                color: #465F7D;
                text-decoration: none;
                font-family: Tahoma, Arial, Helvetica, sans-serif;
                font-weight: normal;
            }
            a:hover
            {
                color: #4272B4;
                text-decoration: underline;
                font-weight: normal;
            }
     
input[type=text]
{
  border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
  width:200px;
  min-height: 28px;
  padding: 4px 20px 4px 8px;
  font-size: 12px;
  -moz-transition: all .2s linear;
  -webkit-transition: all .2s linear;
  transition: all .2s linear;
}
input[type=text]:focus
{
  width: 400px;
  border-color: #51a7e8;
  box-shadow: inset 0 1px 2px rgba(0,0,0,0.1),0 0 5px rgba(81,167,232,0.5);
  outline: none;
}
            input[type=text]
{
  border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
  width:200px;
  min-height: 25px;
  //padding: 4px 20px 4px 8px;
  font-size: 12px;
  -moz-transition: all .2s linear;
  -webkit-transition: all .2s linear;
  transition: all .2s linear;
}
input[type=submit]
{
  border: 1px solid #ccc;
  border-radius: 3px;
  box-shadow: inset 0 1px 2px rgba(0,0,0,0.1);
  width:119px;
  min-height: 33px;
  padding: 4px 20px 4px 8px;
  font-size: 12px;
  -moz-transition: all .2s linear;
  -webkit-transition: all .2s linear;
  transition: all .2s linear;
}*/
        </style>
    </head>

    <body>

        <table cellspacing=0 cellpadding=2 width=95% align=center>
            <tr>
                <td width=100%>

                    <table cellspacing=0 cellpadding=0 width=100%>
                        <tr>
                            <td style="padding-right:4px;"><img src="modules/Alfresco/Alfresco/Common/Images/AlfrescoLogo32.png" border=0 alt="Alfresco" title="Alfresco" align=absmiddle></td>
                            <td><img src="modules/Alfresco/Alfresco/Common/Images/titlebar_begin.gif" width=10 height=30></td>
                            <td width=100% style="background-image: url(modules/Alfresco/Alfresco/Common/Images/titlebar_bg.gif)">
                                <b><font style='color: white'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; Home</font></b>
                            </td>
                            <td><img src="modules/Alfresco/Alfresco/Common/Images/titlebar_end.gif" width=8 height=30></td>
                        </tr>
                    </table>

                </td>

                <td width=8>&nbsp;</td>
                <td><nobr>
                    <!--<img src="modules/Alfresco/Alfresco/Common/Images/logout.gif" border=0 alt="Logout" align=absmiddle><span style='padding-left:2px'><a href='#'>Logout</a></span>-->
            </nobr></td>
    </tr>
    <tr>
        <td width=100%>

        </td>
    </tr>
    <tr>
        <td width=100%>

        </td>
    </tr>
    <tr>
        <td width=100%>
            <a href="index.php?module=Documents&view=List"><b>Document Home</b></a>
        </td>
    </tr><tr></tr><td width=100%></td><tr><td width=100%></td></tr>
    <tr>
        <td>
            <div class="input-append searchBar">
            <form name="search" id="alfresco_search_form" action="index.php?module=Alfresco&view=searchAlfresco" method="post">
                <input type="text" id="alf_search" name="search">
                <input type="submit" name="submit" id="alf_submit" value="Search" class="btn btn-block">
                    </form></div>
        </td>
       <td>
           
       </td><td></td><td></td>
    </tr>
</table>

<br>
                    
    
    


<?php
print(
        "<table cellspacing=0 cellpadding=0 border=0 width=95% align=center>" .
        "   <tr>" .
        "       <td width=7><img src='modules/Alfresco/Alfresco/Common/Images/blue_01.gif' width=7 height=7 alt=''></td><td background='modules/Alfresco/Alfresco/Common/Images/blue_02.gif'><img src='modules/Alfresco/Alfresco/Common/Images/blue_02.gif' width=7 height=7 alt=''></td>" .
        "       <td width=7><img src='modules/Alfresco/Alfresco/Common/Images/blue_03.gif' width=7 height=7 alt=''></td></tr><tr><td background='modules/Alfresco/Alfresco/Common/Images/blue_04.gif'><img src='modules/Alfresco/Alfresco/Common/Images/blue_04.gif' width=7 height=7 alt=''></td>" .
        "       <td bgcolor='#D3E6FE'>" .
        "           <table border='0' cellspacing='0' cellpadding='0' width='100%'><tr><td><span class='mainSubTitle'>Searched Document(click to add to documents)</span></td></tr></table>" .
        "       </td>" .
        "       <td background='modules/Alfresco/Alfresco/Common/Images/blue_06.gif'><img src='modules/Alfresco/Alfresco/Common/Images/blue_06.gif' width=7 height=7 alt=''></td>" .
        "   </tr>" .
        "   <tr>" .
        "       <td width=7><img src='modules/Alfresco/Alfresco/Common/Images/blue_white_07.gif' width=7 height=7 alt=''></td>" .
        "       <td background='modules/Alfresco/Alfresco/Common/Images/blue_08.gif'><img src='modules/Alfresco/Alfresco/Common/Images/blue_08.gif' width=7 height=7 alt=''></td>" .
        "       <td width=7><img src='modules/Alfresco/Alfresco/Common/Images/blue_white_09.gif' width=7 height=7 alt=''></td>" .
        "   </tr>" .
        "   <tr>" .
        "       <td background='modules/Alfresco/Alfresco/Common/Images/white_04.gif'><img src='modules/Alfresco/Alfresco/Common/Images/white_04.gif' width=7 height=7 alt=''></td>" .
        "       <td bgcolor='white' style='padding-top:6px;'>" .
        "           <table border='0' width='100%'>");
if(isset($_REQUEST['search']) && $_REQUEST['search']!=''){
    if($objs->objectList==null){
        echo 'No Document';$doc=FALSE;
    }else{
        $doc=TRUE;
    }
    $db = PearDatabase::getInstance();
    foreach ($objs->objectList as $obj) {

            $objid = strstr($obj->properties['cmis:objectId'], ';', TRUE);

            $rs1 = $db->pquery("select vtiger_alfrescodocuments.crmid, vtiger_alfrescodocuments.document_objectid from vtiger_alfrescodocuments inner join vtiger_crmentity on vtiger_crmentity.crmid=vtiger_alfrescodocuments.crmid where document_objectid ='" . $objid . "'");
            if ($row = $db->fetch_row($rs1)) {
                //echo "<script>alert('$objid');</script>";
                $color = 'green';
                $status = 'File_Exist_In_Vtiger';
                $check = FALSE;
                //$crmid = $crmid + 1;
            } else {
                $color = '';
                $status = 'Add_To_Vtiger';
                $check = TRUE;
            }
            $name = $obj->properties['cmis:name'];
            $repoObj = new CMISalfObject($repositoryUrl, $username, $password, $obj->properties['cmis:objectId']);
            $url = "index.php?module=Alfresco&view=searchAlfresco&name=".urlencode($name)."&create=true&url=";
            $repoObject = new CMISalfObject($repo_url, $repo_username, $repo_password, $obj->properties['cmis:objectId']);
            //echo $repoObject->contentUrl;
            if ($check)
                print "Document: <a title='$status' href='" . $url . $repoObject->contentUrl . "&obj_id=" . $obj->properties['cmis:objectId'] . "'><font color='" . $color . "'>" . $obj->properties['cmis:name'] . "</font></a>\n";
            else
                print "Document: <a title='$status'><font color='" . $color . "'>" . $obj->properties['cmis:name'] . "</font></a>\n";
            echo "<a style='float:right;' href='$repoObj->contentUrl' target='_blank'>";
            echo "<img src=\"modules/Alfresco/Alfresco/img/download.gif\" class='masterTooltip'  title='download file'></a>&nbsp;";
                                
            echo'<br>';
            echo "<hr>";
        }

        $pageS = $_REQUEST['pageS'];
        if ($pageS == '' || $pageS < 0)
            $pageS = 1;
        else
            $pageS = $pageS + 1;
            $search=$_REQUEST['search'];

               $urls = "index.php?module=Alfresco&view=searchAlfresco";
               if($_REQUEST['pageS']!='' || $found_d){
            if ($doc) {
                echo "<br><br><a style='' href='$urls&search=$search&objId=$objid&path=$path&pageS=" . ($pageS - 2) . "'><b>Prev</b></a>";
                if($found_d)
                echo "&nbsp;&nbsp;&nbsp;&nbsp;<a style='' href='$urls&search=$search&objId=$objid&path=$path&pageS=$pageS'><b>Next</b></a></b>";
            } else {
               echo "<br><br><b><a style='' href='$urls&search=$search&objId=$objid&path=$path&pageS=".($pageS - 2)."'><b>Prev</b></a></b>";
            }
               }
       
    } else {
        echo 'No Document';
    }

    print(
        "         </table>" .
        "      </td>" .
        "      <td background='modules/Alfresco/Alfresco/Common/Images/white_06.gif'><img src='modules/Alfresco/Alfresco/Common/Images/white_06.gif' width=7 height=7 alt=''></td>" .
        "   </tr>" .
        "   <tr>" .
        "      <td width=7><img src='modules/Alfresco/Alfresco/Common/Images/white_07.gif' width=7 height=7 alt=''></td>" .
        "      <td background='modules/Alfresco/Alfresco/Common/Images/white_08.gif'><img src='modules/Alfresco/Alfresco/Common/Images/white_08.gif' width=7 height=7 alt=''></td>" .
        "      <td width=7><img src='modules/Alfresco/Alfresco/Common/Images/white_09.gif' width=7 height=7 alt=''></td>" .
        "   </tr>" .
        "</table>");

if ($repo_debug > 2) {
    print "Final State of CLient:\n===========================================\n";
    print_r($client);
}

  }

  if ($_REQUEST["create"] == true) {

                                    $uiUrl = $_REQUEST['url'];
                                    $name = $_REQUEST['name'];
                                    $db = PearDatabase::getInstance();
                                    $rs1 = $db->pquery("select max(crmid) as id from vtiger_crmentity");
                                    if ($row = $db->fetch_row($rs1)) {
                                        $crmid = $row['id'];
                                        $crmid = $crmid + 1;
                                    }
                                    $userid = $current_user->id;
                                    // echo $crmid;
                                    $rs2 = $db->pquery("insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime) VALUES('$crmid','$userid','$userid','Documents',NOW(),NOW())");
                                    // echo $rs2;
                                    //$filetype='application/vnd.oasis.opendocument.text';
                                    //      $zipfilename=$entityid.$filename;

                                    $rs11 = $db->pquery("select cur_id,prefix from vtiger_modentity_num where semodule='Documents' ");
                                    if ($row = $db->fetch_row($rs11)) {
                                        $note_no = $row['prefix'] . $row['cur_id'];
                                        $curid = $row['cur_id'] + 1;
                                    }


                                    //$title=$originalFileName.'_'.$entityid;
                                    // $filesize=  filesize($wordtemplatedownloadpath.$entityid.$filename);
                                    $rs7 = $db->pquery("insert into vtiger_notes (notesid,note_no,title,filename,notecontent,filetype,filelocationtype,filestatus) VALUES('$crmid','$note_no','$name','$uiUrl','','','E','1')");

                                    $rs8 = $db->pquery("insert into vtiger_notescf (notesid) VALUES ('$crmid') ");
                                    $rs12 = $db->pquery("update vtiger_modentity_num set cur_id='$curid' where semodule='Documents' ");

                                    // $crmid1=$crmid+1;
                                    //  $rs2 = $db->pquery("insert into vtiger_crmentity (crmid,smcreatorid,smownerid,setype,createdtime,modifiedtime) VALUES('$crmid1','$userid','$userid','Documents Attachment',NOW(),NOW())");
                                    $rs3 = $db->pquery("update vtiger_crmentity_seq SET id='$crmid' ");
                                    
                                    $newDocId=  strstr($_REQUEST['obj_id'], ';',TRUE);
                                    $rs9 = $db->pquery("insert into vtiger_alfrescodocuments (crmid, document_objectid, document_title) VALUES ('$crmid', '$newDocId', '$name') ");
    
                                    header("location:index.php?module=Documents&view=List");
                                }
                                
                                
                                ?>


 <!--<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>-->

        <script>
                                                                    $(".btn-block").click(function() {
                                                                        if ($('#alf_search').val().length <= 3) {
                                                                            alert('Enter at least 4 characters');
                                                                            return false;
                                                                        } 
                                                                        else {
                                                                            //alert('file selected');
                                                                           // $('#alfresco_upload').hide();
                                                                           // $('#cancel').hide();
                                                                           $('#alfresco_search_form').submit();
                                                                            //document.getElementById('alfresco_search_form').submit();
                                                                            // return true;
                                                                        }
                                                                    });

        </script>
        
        <?php 
    }
     }
        ?>
