<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="it-IT">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="author" content="Fabrizio Vettore/">

</head>

<body>

<?php
error_reporting(E_ALL);
ini_set('display_errors',0);
require_once "modules/Alfresco/Alfresco/config.php";
require_once "modules/Alfresco/Alfresco/Alfresco_CMIS_API.php";
//session_start();
ob_start();
  class Alfresco_alfrescologin_View extends Vtiger_Index_View {

    public function process(Vtiger_Request $request) {
global $current_user;
global $repositoryUrl;
global $hostname;
    global $folderPath;
$userid = $current_user->id;
if($hostname!=''){
if ($userid == null)
    header("location:index.php?module=Documents&view=List");

$user = Alfresco_Record_Model::find($userid);

if ($user != null) {
    $userName = $user['alfresco_username'];
    $password = $user['alfresco_password'];
// Authenticate the user and create a session
    $repo = new CMISalfRepo($repositoryUrl, $userName, $password);
    $repo->connect($repositoryUrl, $userName, $password);
    if ($repo->connected == true) {
        header("location:index.php?module=Alfresco&view=authorise");
    } else {
        $error = 2;
    }
    

}
if(isset($_REQUEST['error']))
        $error=$_REQUEST['error'];
?>

<form action="index.php" method="post">
    <table width="100%" height="98%" align="center">
        <tbody><tr width="100%" align="center">
                <td valign="middle" align="center" width="100%">
                    <input type="hidden" name="module" value="Alfresco">
                    <input type="hidden" name="view" value="authorise">
                    <input type="hidden" name="login" value="login">
                    <table cellspacing="0" cellpadding="0" border="0">
                        <tbody>
                            <?php
                            if ($_REQUEST['error'] == '1') {
                                echo '<tr><td colspan="2" style="color:red;">Invalid user </td></tr><br/><tr><td colspan="2"><br/></td></tr>';
                            } elseif ($error == '2') {
                                echo '<tr><td colspan="2" style="color:red;">Your alfresco password has changed please login to set new password </td></tr><br/><tr><td colspan="2"><br/></td></tr>';
                                ?>
                            <input type="hidden" name="passupdate" value="true">
                        <?php
                        }elseif ($error == '5') {
                            echo '<tr><td colspan="2" style="color:red;">you do not have permission to create folder in root, please select different user </td></tr><br/><tr><td colspan="2"><br/></td></tr>';
                        } else {
                            echo '<tr><td colspan="2">Only First time login required your alfresco credentials will be saved to your database </td></tr><br/><tr><td colspan="2"><br/></td></tr>';
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




                                        <tr>
                                            <td colspan="2">
                                                <span class="mainSubTitle">Enter Login details:</span>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                User Name:
                                            </td>
                                            <td>

                                                <input id="alfresco_username" name="alfresco_username" type="text" value="" style="width:150px">
                                            </td>
                                        </tr>

                                        <tr>
                                            <td>
                                                Password:
                                            </td>
                                            <td>


                                                <input type="password" id="alfresco_password" name="alfresco_password" style="width:150px">
                                            </td>
                                        </tr>


                                        <tr>
                                            <td colspan="2" align="right">
                                                <input type="submit" value="Login" >
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
<?php  }else{
    echo 'please set host name in config file first';
} 
    }
  }
?>