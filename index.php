<?php

$Q_DIR = "Q_html/";
$Q_MAX_NUM = 31;


if(isset($_COOKIE['PHPSESSID'])) {
        session_id($_COOKIE['PHPSESSID']);
        session_start();
        if(isset($_POST['comp']) && isset($_POST['id'])) {
                if($_POST['comp'] == 1) {
                        $_SESSION['COMP_Q_NUM']++;
                }
                if($_POST['id'] == $Q_MAX_NUM) {
                        require($Q_DIR."f.html");
                }else {
                        require($Q_DIR.$id.".html");
                }
        }
        if(isset($_POST['id'])) {
                $id = $_POST['id'];
                $_SESSIOM['NAME'] = $_POST['NAME'];
                //require($Q_DIR.$id.".html");
                header("HTTP/1.1 301 Moved Permanently");
                header("Location: ".$Q_DIR.$id.'.php');
        }
}else {

        session_start();
        $_SESSION['Q_NUM'] = 0;
        $_SESSION['COMP_Q_NUM'] = 0;
        $_SESSIOM['LOGON'] = 1;
        require("start.html");

}
?>
