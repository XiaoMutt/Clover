<?php

include_once '_basics.php';
$rHandle = opendir('.');
if ($rHandle) {
    while (false !== ($sEntry = readdir($rHandle))) {
        if (!is_dir($sEntry)) {
            if (strpos($sEntry, "_table_") === 0) {
                include_once $sEntry;
            }
        }
    }
    closedir($rHandle);
}

class SetupClover extends Clover {

    private $sErrMsg = "";
    public $jResult = "";

    function checkLocalHost() {
        //check whether visit from localhost or 127.0.0.1; return TRUE if yes, or FALSE if no.
        $whitelist = array('localhost', '127.0.0.1');
        if (!in_array($_SERVER['HTTP_HOST'], $whitelist)) {
            $this->sErrMsg.='Forbidden! Please visit this page using "localhost".<br/>';
            return FALSE;
        } else {
            return TRUE;
        }
    }

    function checkCloverNonexistent() {
        //check whether clover exsit or not;if exist return FALSE, or return TRUE;
        $sQuery = "SHOW DATABASES LIKE '" . $this->sCloverDatabaseName . "'";
        $rResult = $this->query($sQuery);

        if ($rResult) {
            if ($rResult->num_rows) {
                $this->sErrMsg.='Forbidden! Clover already exists!<br/>';
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            $this->aErrors[] = __FILE__ . " Line " . __LINE__ . " Database Error: cannot query mySQL database with the query: " . $sQuery;
            return FALSE;
        }
    }

    function checkInput(&$aData) {
        if (empty($aData['name'])) {
            $this->sErrMsg.="Please enter your name.<br/>";
        }
        if (!filter_var($aData['email'], FILTER_VALIDATE_EMAIL)) {
            $this->sErrMsg.="Please enter a validate email.<br/>";
        }
        if ($aData['password'] == "da39a3ee5e6b4b0d3255bfef95601890afd80709") {
            $this->sErrMsg.="Please enter a password.<br/>";
        }

        if ($aData['password'] != $aData['repeat_password']) {
            $this->sErrMsg.="Passwords you typed do not match.<br/>";
        }
    }

    function __construct($aData) {
        /* connect to mysql database */
        parent::__construct();
        if ($this->checkLocalHost() && $this->checkCloverNonexistent()) {
            //check user input;
            $this->checkInput($aData);
        }
        if (empty($this->sErrMsg)) {
            //all user information is correct;
            //create clover database;
            
            $sQuery = "CREATE DATABASE IF NOT EXISTS `" . $this->sCloverDatabaseName . "` DEFAULT CHARACTER SET " . $this->sCharaterSet . " COLLATE " . $this->sCollation;
            $this->query($sQuery) or $this->aErrors[] = __FILE__ . " Line " . __LINE__ . " Database Error: cannot query mySQL database with the query: " . $sQuery;

            //creat all tables;
            $this->select_db($this->sCloverDatabaseName) or $this->aErrors[] = __FILE__ . " Line " . __LINE__ . " Database Error: cannot select the " . $this->sCloverDatabaseName . " database.";

            $aClasses = get_declared_classes();
            $sQuery = "";
            foreach ($aClasses as $sClass) {
                $iPos = strpos($sClass, "Table");
                if ($iPos !== false && (strlen($sClass) - strlen("Table")) == $iPos) {
                    $cOpt = new $sClass;
                    $sQuery.=$cOpt->createTable();
                }
            }
            //add user;
            $currentTime = DateTime::createFromFormat($this->sDateTimeFormat, date($this->sDateTimeFormat));
            $sQuery .= "INSERT INTO `" . $this->sCloverDatabaseName . "`.`users`
                        (
                        `id` ,
                        `deleted` ,
                        `last_modified_by` ,
                        `last_modified_on` ,
                        `email` ,
                        `password` ,
                        `name` ,
                        `identity` ,
                        `description`
                        )
                    VALUES (
                        '1',  '0',  '0',  '" . $currentTime->format("Y-m-d H:i:s") . "',  '" . $aData["email"] . "',  '" . sha256($aData["password"]) . "',  '" . $aData["name"] . "',  'admin',  'system created 1st admin'
                        );
                    ";
            if ($this->multi_query($sQuery)) {//admin account added;
                session_name($this->sSessionName); //login
                session_start(); //login
                $_SESSION["user_id"] = 1; //login;
                $aResult["changed"] = 1;
            }//if error then clover class will handle it;
            else {
                $aResult["changed"] = 0;
                $aResult["errors"] = "Mysql database query error!";
            }
        } else {//user information incorrect;
            $aResult["changed"] = 0;
            $aResult["errors"] = $this->sErrMsg;
        }

        $this->jResult = json_encode($aResult);
    }

}

?>
