<?php

/* basic Clover class contains the basic settings */

class Clover extends mysqli {

    public $sUsername = "root"; //user name of the mysql server
    public $sPassword = ""; //password of the mysql server
    public $sHost = "127.0.0.1"; //IP address of the mysql server
    public $sCloverDatabaseName = "clover"; //the clover database name in the mysql server
    public $sTimeZone = "America/New_York"; //timezone used
    public $sDateFormat = "m/d/Y"; //date format used in php
    public $sJavaDateFormat="mm/dd/yy";//date format used in java which should be consistent with sDateFormat;
    public $sTimeFormat = "g:i a"; //time format used in php
    public $sDateTimeFormat = "m/d/Y, g:i a"; //datetime format used in php
    public $sCharaterSet = "utf8"; //character set used
    public $sCollation ="utf8_unicode_ci";//collation used in mysql database
    public $sSessionName = "clover";//session name
    public $aErrors = array(); //the array to store error messeages

    function __construct() {
        parent::__construct($this->sHost, $this->sUsername, $this->sPassword);
        if ($this->connect_error) {
            $this->aErrors[] = __FILE__ . " Line " . __LINE__ . " Database Error: cannot connect to the mysql datebase! " . $this->connect_error;
        }
    }

    function __destruct() {
        $this->close();
        if (count($this->aErrors)) {
            foreach ($this->aErrors as &$sError) {
                print_r('<p>' . $sError . '</p>');
            }
        }
    }

}

?>
