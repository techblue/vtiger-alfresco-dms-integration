<?php
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('memory_limit', '256M');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="it-IT">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="author" content="Fabrizio Vettore/">
<link rel="SHORTCUT ICON" href="layouts/vlayout/skins/images/favicon.ico">
</head>

<body>

    <?php
    require_once ('modules/Alfresco/Alfresco/cmis_repository_wrapper.php');
    require_once ('modules/Alfresco/Alfresco/cmis_service.php');
    require_once "modules/Alfresco/Alfresco/config.php";
    require_once "modules/Alfresco/Alfresco/Alfresco_CMIS_API.php";
    ob_start();
    //session_start();
    //global $repositoryUrl;
    //global $folderPath;

//global $path;
    class Alfresco_copyTo_View extends Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
    global $repositoryUrl;
    global $folderPath;
    global $limit_doc;
    global $limit_fol;
        //$folderPath=$GLOBALS['folderPath'];
        //$repositoryUrl=$GLOBALS['repositoryUrl'];
    global $current_user;
    $userid = $current_user->id;
    if ($userid == '')
        header("location:index.php?module=Documents&view=List");

    if(($_REQUEST['copy']!='' && $_REQUEST['paste']=='') || ($_REQUEST['move']!='' && $_REQUEST['paste']=='')){
        if($_SESSION['docid']=='')
            $_SESSION['docid']=$_REQUEST['obj_Id'];
        $copy_doc_id=$_SESSION['docid'];
        if($_REQUEST['parent_objid']!='' && $_REQUEST['move']!='')
            $_SESSION['parent_objid']=$_REQUEST['parent_objid'];
       // echo "<script>alert('$copy_doc_id');</script>";
    $user = Alfresco_Record_Model::find($userid);
    if ($user != null) {
        $userName = $user['alfresco_username'];
        $password = $user['alfresco_password'];
        $f = $user['alfresco_folder'];
        if ($f == '')
            header('location:index.php?module=Alfresco&view=alfrescologin');
        // Authenticate the user and create a session
        $repo = new CMISalfRepo($repositoryUrl, $userName, $password);
        $repo->connect($repositoryUrl, $userName, $password);
        if ($repo->connected != true) {
            header("location:index.php?module=Alfresco&view=alfrescologin");
        }
    }

    if ($f != '') {
        $folder = new CMISalfObject($repositoryUrl, $userName, $password, null, null, $folderPath . $f);
        if (!$folder->loaded) {
            //die("\nSORRY! cannot open folder!\nThe last HTTP request returned the following status: " . $folder->lastHttpStatus . "\n\n");
            //else
            //Alfresco_Record_Model::createFolder($userid, $_REQUEST['alfresco_folder']);
//Load contained objects
            $folder = new CMISalfObject($repositoryUrl, $userName, $password, null, null, $folderPath);
            $folder->createFolder($f);
            // header("location:index.php?module=Documents&view=List");
        }
    }


    if ($_REQUEST["create"] != true && $_REQUEST["delete"] != true) {
        $user = Alfresco_Record_Model::find($userid);
        if ($user == null)
            header("location:index.php?module=Alfresco&view=alfrescologin");
        // print_r($user);
        $username = $user['alfresco_username'];
        $password = $user['alfresco_password'];
        $folde    = $user['alfresco_folder'];
        $alfresco_folder = '/' . $user['alfresco_folder'];
       // $alfresco_folder='/';

       // if ($_GET['logout'])
           // session_unset();

//if($_POST['username']){
        $_SESSION['username'] = $username;
        $_SESSION['password'] = $password;
        $_SESSION['repourl'] =  $repositoryUrl;
//}

        if (!isset($_SESSION['username'])) {
            ?>

            <?php
        } else {
            if (!isset($_GET['objId'])) {
                $repo = new CMISalfObject($_SESSION['repourl'], $_SESSION['username'], $_SESSION['password'], NULL, NULL, $alfresco_folder);
                $objId = $repo->objId;
            } else {
                $objId = $_GET['objId'];
            }
            if ($_REQUEST['name'] == '') {
                // $space='/';
            } else {
                //$space='/'.$space;
            }
            $repoObject = new CMISalfObject($_SESSION['repourl'], $_SESSION['username'], $_SESSION['password'], $objId);
            ?>
            <html>
                <head>
                    
                    <style>
                        #alfr
                        {
                            font-size: 11px;
                           // color: #465F7D;
                           // text-decoration: none;
                           // font-family: Tahoma, Arial, Helvetica, sans-serif;
                            //font-weight: normal;
                        }
                       // body {font-family: verdana; font-size: 8pt;}
                       // tr {font-family: verdana; font-size: 8pt;}
                       // td {font-family: verdana; font-size: 8pt;}
                       // input {font-family: verdana; font-size: 8pt;}
                        //.maintitle {font-family: verdana; font-size: 10pt; font-weight: bold; padding-bottom: 15px;}
                      /*  a:link, a:visited
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
                        }*/
                    </style>


                    <style type="text/css">

                        .tooltip1 {
                            display:none;
                            position:absolute;
                            border:1px solid #333;
                            background-color:rgb(211, 230, 254);
                            border-radius:5px;
                            padding:10px;
                            color:#161616;
                            font-size:12px Arial;//width: 110px;
                        }
                    </style><!--<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.6.2/jquery.min.js"></script>-->
                        <script type="text/javascript" src="modules/Alfresco/Alfresco/MyRss.js"></script>

                    <script type="text/javascript">
                        $(document).ready(function() {
                           // jQuery('#RssFeedAdd').trigger('click');
                        $("input:file").change(function (){
                           //alert('file selected'); 
                        });
                            
                            jQuery('#RssFeedAdd').click(function(e){
                                        var formTplClone = jQuery('#RssFeedAddFormTpl').clone().css({display: 'block'});
                                        app.showModalWindow(formTplClone, function(){
                                                var targetForm = jQuery('.RssFeedAddForm', formTplClone);
                                                /*targetForm.validationEngine(app.validationEngineOptions);
                                                targetForm.submit(function(){
                                                        if (!targetForm.validationEngine('validate')) {
                                                                return;
                                                        }
      
                                                        var params = 'module=MyRss&action=Save&';
                                                        params += jQuery(targetForm).serialize();
                                                        AppConnector.request(params).then(function(response){
                                                                window.location.reload();
                                                                //app.hideModalWindow();
                                                        });
                                                });*/
                                        });
                                });
                            jQuery('#RssFeedAdd').trigger('click');
                            // Tooltip only Text
                            $('.masterTooltip').hover(function() {
                                // Hover over code
                                var title = $(this).attr('title');
                               // alert(title);
                               // $(this).data('tipText', title).removeAttr('title');
                                $('<p class="tooltip1"></p>')
                                        .text(title)
                                        .appendTo('body')
                                        .fadeIn('slow');
                            }, function() {
                                // Hover out code
                                $(this).attr('title', $(this).data('tipText'));
                                $('.tooltip1').remove();
                            }).mousemove(function(e) {
                                var mousex = e.pageX - 80; //Get X coordinates
                                var mousey = e.pageY - 40; //Get Y coordinates
                                $('.tooltip1')
                                        .css({top: mousey, left: mousex})
                            });
                        });
                    </script>

                </head>

                                <body>
                                   
                                   <div id="RssFeedAddFormTpl" style="overflow-y: auto;height: 600px;">
        <div class="modelContainer" >
               
   
                                    

                                    <table cellspacing=0 cellpadding=2 width=95% align=center>
                                        <tr>
                                            <td width=100%>

                                                <table cellspacing=0 cellpadding=0 width=100%>
                                                    <tr>
                                                        <td style="padding-right:4px;"><img src="modules/Alfresco/Alfresco/Common/Images/AlfrescoLogo32.png" border=0 alt="Alfresco" title="Alfresco" align=absmiddle></td>
                                                        <td><img src="modules/Alfresco/Alfresco/Common/Images/titlebar_begin.gif" width=10 height=30></td>
                                                        <td width=100% style="background-image: url(modules/Alfresco/Alfresco/Common/Images/titlebar_bg.gif)">
                                                            <b><font style='color: white'> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Home</font></b>
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

                                            </td>
                                        </tr>
                                        <tr>
                                            <td width=100%>
                         <a href="index.php?module=Alfresco&view=copyTo&paste=true&parent_objid=<?php echo $_SESSION['parent_objid']; ?>&docid=<?php echo $_SESSION['docid']; ?>&objid=<?php echo $objId;  ?>" id="" class="btn btn-block">Paste Here Or Select Folder</a>

                                            </td>
                                        </tr>
                                    </table>
                                    

                                   <br>


            <?php
            if ($_REQUEST['objId'] != '') {


                function outputBreadcrumb($url, $path) {
                    // global $session, $store;

                    print(
                            '<table border="0" width="95%" align="center">' .
                            '   <tr>' .
                            '      <td>');
                    if ($url == '') {
                        $values = explode('|', $path);
                        $uri = 'index.php?module=Alfresco&view=copyTo&copy=true&parent_objid='.$_SESSION['parent_objid'].'&docid='.$_SESSION['docid'].'&objId=';
                    } else {
                        $values = explode('$', $path);
                        $path = $values[1];
                    }

                    //$values = split("\|", $path);
                    //$home = $values[0];
                    //$path = $home;
                    //$id_map = array();
                    //for ($counter = 1; $counter < count($values); $counter += 2)
                    //{
                    //   $id_map[$values[$counter]] = $values[$counter+1];
                    //}
                    $home = ' Home';
                    print("<a href='index.php?module=Alfresco&view=copyTo&copy=true&parent_objid=".$_SESSION['parent_objid']."&docid=".$_SESSION['docid']."'><b>" . $home . "</b></a>");
                    //foreach($id_map as $id=>$name)
                    //{
                    //  $path .= '|'.$id.'|'.$name;
                    //  print("&nbsp;&gt;&nbsp;<a href='".getURL($session->getNode($store, $id))."'><b>".$name."</b></a>");
                    // }
                    if($_REQUEST['num']!='')
                        $num_c=$_REQUEST['num'];
                    $cn=0;
                    $p = '';
                    $i = 1;
                    $prevpath = '';
                    if ($url != '') {
                        print("&nbsp;&gt;&nbsp;<a href='" . $url . "'><b>" . $path . "</b></a>");
                    } else {
                        foreach ($values as $value) {
                            if (count($values) > $i) {
                                if ($p == '') {
                                    $p = $value;
                                } else {
                                    $p = $p . '|' . $value;
                                }
                            }
                            $cn++;
                            $i++;
                            if($num_c!='' && $num_c>=0)
                            $num_c=$num_c-1;
                            else 
                                $num_c=1;
                            $prevId=$curId;
                            $param = explode('$', $value);
                            //  echo $value.'<br>';
                            //$p.='|';
                            $curId=$param[0];
                           // $prevpath = $path;
                            $path = $param[1]; //echo $param[0].'&&'.$param[1];
                           // if($prevpath==$path)
                           // echo "<script>alert('$num_c');</script>";
                            if ($prevId != $curId)
                                print("&nbsp;&gt;&nbsp;<a href='" . $uri . $param[0] . "&path=" . $p . "&num=$cn&name=" . $path . "'><b>" . $path . "</b></a>");
                        }
                    }
                    print(
                            '      </td>' .
                            '   </tr>' .
                            '</table>');
                }

                if ($_REQUEST['path'] != '' || $_REQUEST['name'] != '') {
                    if ($_REQUEST['path'] == '' && $_REQUEST['name'] != '') {

                        $url = 'index.php?module=Alfresco&view=copyTo&copy=true&parent_objid='.$_SESSION['parent_objid'].'&docid='.$_SESSION['docid'].'&objId=' . $_REQUEST['objId'] . '&name=' . $_REQUEST['name'] . '&path=' . $_REQUEST['objId'] . '$' . $_REQUEST['name'];
                        //$objid=$_REQUEST['objId'];
                        $name = $_REQUEST['name'];
                        $path = $_REQUEST['objId'] . '$' . $_REQUEST['name'];
                        $_SESSION['path'] = $path;
                    } else {
                        $path = $_REQUEST['path'] . '|' . $_REQUEST['objId'] . '$' . $_REQUEST['name'];
                        $url = '';
                        $_SESSION['path'] = $path;
                    }
                }

                outputBreadcrumb($url, $path);
            }
            //echo "<hr><hr><h3>Contents:</h3>";

            if ($repoObject->properties['cmis:baseTypeId'] == "cmis:folder") {

                //It is a folder or something similar
                function outputTable($title, $repoObject, $type_filter, $objId, $found,$create) {
                    if ($_REQUEST['path'] == '' && $_REQUEST['name'] == '')
                        $_SESSION['path'] = '';
                    $url = 'index.php?module=Alfresco&view=copyTo&copy=true&parent_objid='.$_SESSION['parent_objid'].'&docid='. $_SESSION['docid'];
                    print(
                            "<table cellspacing=0 cellpadding=0 border=0 width=95% align=center>" .
                            "   <tr>" .
                            "       <td width=7><img src='modules/Alfresco/Alfresco/Common/Images/blue_01.gif' width=7 height=7 alt=''></td><td background='modules/Alfresco/Alfresco/Common/Images/blue_02.gif'><img src='modules/Alfresco/Alfresco/Common/Images/blue_02.gif' width=7 height=7 alt=''></td>" .
                            "       <td width=7><img src='modules/Alfresco/Alfresco/Common/Images/blue_03.gif' width=7 height=7 alt=''></td></tr><tr><td background='modules/Alfresco/Alfresco/Common/Images/blue_04.gif'><img src='modules/Alfresco/Alfresco/Common/Images/blue_04.gif' width=7 height=7 alt=''></td>" .
                            "       <td bgcolor='#D3E6FE'>" .
                            "           <table border='0' cellspacing='0' cellpadding='0' width='100%'><tr><td><span class='mainSubTitle'>" . $title . "</span></td></tr></table>" .
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

                    if ($type_filter == 'f') {
                        $spaces = 0;
                        $contents = 1;
                    } elseif ($type_filter == 'c') {
                        $spaces = 1;
                        $contents = 0;
                    }
                    $db = PearDatabase::getInstance();
                           
               
                            foreach ($repoObject as $object) {
                      //  if ($object->properties['cmis:objectTypeId'] != "F:st:site" && $object->properties['cmis:objectTypeId'] != "F:st:sites") {


                            if (($object->properties['cmis:objectTypeId'] == "cmis:folder" && $type_filter == 'f' && $object->properties['cmis:objectId']!=$_SESSION['docid'])) {
                                echo "<hr>";
                                echo "<a  style='' href=\"" . $url . "&name=" . $object->properties['cmis:name'] . "&objId=" . $object->properties['cmis:objectId'] . '&path=' . $_SESSION['path'] . "\">";
                                echo "<img src=\"modules/Alfresco/Alfresco/img/space-icon-default.gif\" width='25px'></a>&nbsp;";
                                echo "\n<b id='alfr'>" . $object->properties['cmis:name'] . "</b>";
                                if($create){
                              //  echo "<a  style='float:right;' onclick='if(!confirm(\"Are you sure want to delete this folder and its contents\")) return false;' href=\"" . $url . "&obj_id=" . $object->properties['cmis:objectId'] . '&delete=true&f=1&prev=' . $_REQUEST['objId'] . '&path=' . $_SESSION['path'] . "\">";
                               // echo "<img class='masterTooltip' src=\"modules/Alfresco/Alfresco/img/deletefolder.gif\" title='delete folder'></a>&nbsp;<br>";
                                }
                                $spaces = 1;
                                $contents = 1;
                            }

                             if(($object->properties['cmis:objectTypeId']=="F:st:site" || $object->properties['cmis:objectTypeId']=="F:st:sites") && $type_filter == 'f'){
                              echo "<hr>";
                                echo "<a style='' href=\"" . $url . "&name=" . $object->properties['cmis:name'] . "&objId=" . $object->properties['cmis:objectId'] . '&path=' . $_SESSION['path'] . "\">";
                              echo "<img src=\"modules/Alfresco/Alfresco/img/world1.gif\"></a>&nbsp;";
                                echo "\n<b id='alfr'>" . $object->properties['cmis:name'] . "</b>";
                              } 
                          
                            
                            //echo "\n<b>" . $object->title . "</b><br>";
                            // echo "\n" . $object->aspects['cm:title'] . "<br>";
                            // echo "\n<span style=\"font-size: 0.8em\">" . $object->aspects['cm:description'] . "</span><br>";
                            //echo "\n<span style=\"font-size: 0.8em\">Object ID:" . $object->properties['cmis:objectId'] . "</span><br>";
                       // }
                    }
                    $name = $_REQUEST['name'];
                    $path = $_REQUEST['path'];
                    $objid = $_REQUEST['objId'];
                    if ($objid == '')
                        $objid = $objId;
                    $pageD = $_REQUEST['pageD'];
                    if ($pageD == '' || $pageD < 0)
                        $pageD = 1;
                    else
                        $pageD = $pageD + 1;

                    $pageF = $_REQUEST['pageF'];
                    if ($pageF == '' || $pageF < 0)
                        $pageF = 1;
                    else
                        $pageF = $pageF + 1;

                    $n = 1;
                    $p = 1;
                    if ($spaces == 0) {
                        echo '<b>no spaces</b>';
                        $p = 0;
                        $pageF = $pageF - 2;
                    } elseif ($contents == 0) {
                        echo '<b>no contents</b>';
                        $n = 0;
                        $pageD = $pageD - 2;
                    }


                    //$_SESSION['doc']=$_SESSION['path'];
                    // $_SESSION['path']='';unset( $_SESSION['path']);

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
                    //if($found || isset($_REQUEST['pageD'])){
                    echo '<table cellspacing="0" cellpadding="0" border="0" width="85%" align="center"> ';
                    echo '<tr><td width=7>';
                    if ($type_filter == 'f' && ($found || isset($_REQUEST['pageF']))) {
                        if ($p == 1) {
                            echo "<br><a style='' href='$url&name=$name&objId=$objid&path=$path&pageF=" . ($pageF - 2) . "'><b>Prev</b></a>";
                            if($found)
                            echo "&nbsp;&nbsp;&nbsp;&nbsp;<a style='' href='$url&name=$name&objId=$objid&path=$path&pageF=$pageF'><b>Next</b></a></b>";
                        } else {
                            echo "<br><b><a style='' href='$url&name=$name&objId=$objid&path=$path&pageF=$pageF'><b>Prev</b></a></b>";
                        }
                    }
                  
                    echo '</td></tr>';
                    echo '</table>';
                    echo '<br>';
                    // }
                }

                if ($_REQUEST['pageD'] == '' || $_REQUEST['pageD'] < 0)
                    $pageD = 0;
                else
                    $pageD = $_REQUEST['pageD'];
                $skipD = $pageD * $limit_doc;

                if ($_REQUEST['pageF'] == '' || $_REQUEST['pageF'] < 0)
                    $pageF = 0;
                else
                    $pageF = $_REQUEST['pageF'];
                $skipF = $pageF * $limit_fol;

                $default_hash_values_folder = array(
                    "includeAllowableActions" => "true",
                    "searchAllVersions" => "false",
                    "maxItems" => $limit_fol,
                    "skipCount" => $skipF
                );
                $default_hash_values_document = array(
                    "includeAllowableActions" => "true",
                    "searchAllVersions" => "false",
                    "maxItems" => $limit_doc,
                    "skipCount" => $skipD
                );

                $client = new CMISService($repositoryUrl, $userName, $password);
                $client->connect($repositoryUrl, $userName, $password);
                if ($client->authenticated == true) {
                    //$client->workspace;
                }

                $query = "SELECT cmis:name,cmis:objectId,cmis:baseTypeId,cmis:objectTypeId FROM cmis:document WHERE IN_FOLDER('$objId') order by cmis:lastModificationDate DESC";
                $objs = $client->query($query, array(), $default_hash_values_document);

                $query1 = "SELECT cmis:name,cmis:objectId,cmis:baseTypeId,cmis:objectTypeId FROM cmis:folder WHERE IN_FOLDER('$objId') order by cmis:lastModificationDate DESC";
                $objs1 = $client->query($query1, array(), $default_hash_values_folder);


                $default_hash_values_folder_c = array(
                    "includeAllowableActions" => "true",
                    "searchAllVersions" => "false",
                    "maxItems" => 1,
                    "skipCount" => $skipF + $limit_fol
                );
                $default_hash_values_document_c = array(
                    "includeAllowableActions" => "true",
                    "searchAllVersions" => "false",
                    "maxItems" => 1,
                    "skipCount" => $skipD + $limit_doc
                );



                $query3 = "SELECT cmis:name,cmis:objectId,cmis:baseTypeId,cmis:objectTypeId FROM cmis:document WHERE IN_FOLDER('$objId') order by cmis:lastModificationDate";
                $objs3 = $client->query($query3, array(), $default_hash_values_document_c);

                $query4 = "SELECT cmis:name,cmis:objectId,cmis:baseTypeId,cmis:objectTypeId FROM cmis:folder WHERE IN_FOLDER('$objId') order by cmis:lastModificationDate";
                $objs4 = $client->query($query4, array(), $default_hash_values_folder_c);
                $found_d = TRUE;
                $found_f = TRUE;
                if ($objs4->objectList == null)
                    $found_f = FALSE;
                if ($objs3->objectList == null)
                    $found_d = FALSE;




                    $client = new CMISService($repositoryUrl, $userName, $password);
                    $myfolder = $client->getObject($objId);
                    $per = $client->getAllowableActions($myfolder->id);


                    if ($per['canUpdateProperties']) {
                        // echo 'can create';
                        $create = true;
                    } else {
                        $create = false;
                        //echo 'you do not have permission to create folder in root, please select different user';
                    }//canCreateDocument


                outputTable(' Space', $objs1->objectList, 'f', $objId, $found_f,$create);
               // outputTable("Click On Content Items To Add Documents", $objs->objectList, 'c', $objId, $found_d,$create);
                $_SESSION['path'] = '';
                unset($_SESSION['path']);
            } else if ($repoObject->properties['cmis:baseTypeId'] == "cmis:document") {
                //It is a document or something similar
                $name = $repoObject->properties['cmis:name'];
                print(
                        "<table cellspacing=0 cellpadding=0 border=0 width=95% align=center>" .
                        "   <tr>" .
                        "       <td width=7><img src='modules/Alfresco/Alfresco/Common/Images/blue_01.gif' width=7 height=7 alt=''></td><td background='modules/Alfresco/Alfresco/Common/Images/blue_02.gif'><img src='modules/Alfresco/Alfresco/Common/Images/blue_02.gif' width=7 height=7 alt=''></td>" .
                        "       <td width=7><img src='modules/Alfresco/Alfresco/Common/Images/blue_03.gif' width=7 height=7 alt=''></td></tr><tr><td background='modules/Alfresco/Alfresco/Common/Images/blue_04.gif'><img src='modules/Alfresco/Alfresco/Common/Images/blue_04.gif' width=7 height=7 alt=''></td>" .
                        "       <td bgcolor='#D3E6FE'>" .
                        "           <table border='0' cellspacing='0' cellpadding='0' width='100%'><tr><td><span class='mainSubTitle'>" . $title . "</span></td></tr></table>" .
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
              //  $nam= str_replace("", "#", $name);

                $url = "index.php?module=Alfresco&view=copyTo&copy=true&parent_objid=".$_SESSION['parent_objid']."&docid=".$_SESSION['docid']."&name=".urlencode($name)."&obj_id=" . $_REQUEST['objId'] . "&create=true&url=";
                echo "<hr>";
                echo "<tr><td><a href=\"" . $url . $repoObject->contentUrl . "\">";
                //echo "<hr>";
                echo "<b>Add To Vtiger: </b><img src=\"modules/Alfresco/Alfresco/img/generic.gif\"></a></td></tr></br>";
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
            } else {
                // echo 'no contents';
            }
        }

        //if(isset($_SESSION['username'])) echo "<hr><a href=\"".$_SERVER['PHP_SELF']."?logout=1\">logout</a>"; 
        ?>
                                                                                                   
        </div>
</div>
        </body>
        </html>
        <?php
    }
}

    //copy files into folder
    if ($_REQUEST['paste'] == 'true' && $_SESSION['parent_objid']=='') {
        
        $folder = new CMISService($repositoryUrl, $_SESSION['username'], $_SESSION['password']);
        //$ext = pathinfo($name, PATHINFO_EXTENSION);
        //$fname=  basename($name,'.'.$ext);
        //echo '<script>alert("'.$_REQUEST['objid'].$_REQUEST['docid'].'");</script>';
        try {
            
            $folder->addObjectToFolder($_REQUEST['docid'],$_REQUEST['objid']);
            unset($_SESSION['docid']);
          //  unset($_SESSION['parent_objid']);
         
        } catch (Exception $e) {
            $error='something goes wrong.';
            echo "<script>alert('$error');</script>";
        }
       // echo "<script>window.history.back();</script>";
        header("location:index.php?module=Alfresco&view=selectfromalfresco");
    }

    //for moving files and folder
    if($_REQUEST['paste']=='true' && $_SESSION['parent_objid']!=''){
         $folder = new CMISService($repositoryUrl, $_SESSION['username'], $_SESSION['password']);
        //$ext = pathinfo($name, PATHINFO_EXTENSION);
        //$fname=  basename($name,'.'.$ext);
       // echo '<script>alert("'.$_REQUEST['objid'].$_REQUEST['docid'].'");</script>';
        try {
            
            $folder->moveObject($_REQUEST['docid'], $_REQUEST['objid'], $_SESSION['parent_objid']);
         unset($_SESSION['parent_objid']);
        } catch (Exception $e) {
            $error='something goes wrong.';
             echo "<script>alert('$error');</script>";
        }
       // echo "<script>window.history.back();</script>";
        header("location:index.php?module=Alfresco&view=selectfromalfresco");
    
    }

   
   ?>


                                                                   <!-- <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.11.2/jquery.min.js"></script>-->
                                                                    <script>

                        $("#alfresco_upload").click(function() {
                            if ($('#alfresco_file').val() == "") {
                                alert('no file selected');
                                return false;
                            }
                            //check whether browser fully supports all File API
                            if (window.File && window.FileReader && window.FileList && window.Blob)
                            {
                                //get the file size and file type from file input field
                                var fsize = $('#alfresco_file')[0].files[0].size;

                                if (fsize > 2048576) //do something if file size more than 1 mb (1048576)
                                {
                                    alert("max upload file size 2 mb!");
                                    return false;
                                }
                            }//else {
                            // alert('file selected');
                            $('#alfresco_upload').hide();
                            // $('#cancel').hide();
                            document.getElementById('upload').submit();
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
<?php
if($error!='')
    echo "<script>alert('$error');</script>";
    
 }
    }
    ?>
