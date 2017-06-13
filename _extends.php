<?php

include_once '_basics.php';

/* sha256 encryption */

function sha256($sString) {
    return hash("sha256", $sString);
}

class Connect2Clover extends Clover {

    public $sSessionUserType; //the usertype of the user of the current session
    public $sSessionUserName; //the name of the user of the current session
    public $sSessionUserEmail; //the email of the user of the current session
    public $iSessionUserId; //the user_id of the user of the current session

    function __construct() {
        /* set timezone */
        date_default_timezone_set($this->sTimeZone) or $this->aErrors[] = __FILE__ . " Line " . __LINE__ . " PHP Error: cannot set timezone to " . $this->sTimeZone;

        /* establish connection to the clover database */
        parent::__construct();
        $this->select_db($this->sCloverDatabaseName) or $this->aErrors[] = __FILE__ . " Line " . __LINE__ . " Database Error: cannot select the " . $this->sCloverDatabaseName . " database.";

        /* set character set*/
        $this->set_charset($this->sCharaterSet) or $this->aErrors[] = __FILE__ . " Line " . __LINE__ . " Database Error: cannot set character set to " . $this->sCharaterSet . ". <br/> Code " . $this->errno . ": " . $this->error;

    }

    public function queryClover($sQuery) {
        $rResult = $this->query($sQuery);
        if ($rResult === FALSE) {
            $this->aErrors[] = __FILE__ . " Line " . __LINE__ . " Database Error: cannot query the Clover database with query: " . $sQuery . " <br/> Code " . $this->errno . ": " . $this->error;
            return FALSE;
        } else {
            return $rResult;
        }
    }
}

class OperateTables extends Connect2Clover {

    public $sTableName; //the database table name.
    public $sTableLabel; //the database table label.
    public $aaTableStructure = array();
    public $aActionIcons = array();
    public $aActionAuths = array();

    function __construct($bLogin = true) {
        parent::__construct();

        
        $sQuery="SHOW TABLES LIKE 'users'";
        $rResult=$this->queryClover($sQuery);
        if ($rResult&&$rResult->num_rows==1&&$bLogin) {
            /* start sesssion */
            if (!isset($_SESSION['user_id'])) {
                session_name($this->sSessionName);
                session_start();
            }
            if (isset($_SESSION["user_id"])) {
                $sQuery = "SELECT * FROM `users` WHERE `id`='" . $_SESSION["user_id"] . "' AND `identity`!='visitor' AND `deleted`='0'";
                $rResult = $this->queryClover($sQuery);
                if ($rResult) {
                    $aRow = $rResult->fetch_assoc();
                    if ($aRow) {
                        $this->sSessionUserEmail = $aRow["email"];
                        $this->sSessionUserName = $aRow["name"];
                        $this->sSessionUserType = $aRow["identity"];
                        $this->iSessionUserId = $_SESSION["user_id"];
                    }
                    $rResult->close();
                } else {
                    $this->aErrors[] = __FILE__ . " Line " . __LINE__ . " Database Error: cannot obtain user information using user ID: " . $_SESSION["user_id"];
                }
            } else {
                header('Location: index.php');
            }
        }

        $aadefault = array(
            "id" => array("name" => "id", "label" => "ID", "brief" => "1", "edit" => "0", "detail" => "1", "search" => "1", "data_type" => "int(12) unsigned NOT NULL AUTO_INCREMENT, PRIMARY KEY (`id`)"),
            "deleted" => array("name" => "deleted", "brief" => "0", "edit" => "0", "detail" => "0", "label" => "Deleted", "search" => "0", "data_type" => "int(12) unsigned NOT NULL DEFAULT '0'"),
            "last_modified_by" => array("name" => "last_modified_by", "brief" => "0", "edit" => "0", "detail" => "254", "search" => "1", "label" => "Last Modified by", "data_type" => "int(12) unsigned NOT NULL DEFAULT '0'", "function" => "userID2Name(last_modified_by)"),
            "last_modified_on" => array("name" => "last_modified_on", "brief" => "0", "edit" => "0", "detail" => "255", "search" => "0", "label" => "Last Modified on", "data_type" => "DATETIME NULL DEFAULT NULL", "function" => "mysql2CloverDateTime(last_modified_on)")
        );
        $this->aaTableStructure = array_merge($aadefault, $this->aaTableStructure);
    }

    public function createTable() {
        $sQuery = "CREATE TABLE IF NOT EXISTS `" . $this->sTableName . "` (";
        foreach ($this->aaTableStructure as $aField) {
            if (!isset($aField["name"])) {
                echo $this->sTableName;
            }
            $sQuery.="`" . $aField["name"] . "` " . $aField["data_type"] . ",";
        }
        return substr($sQuery, 0, strlen($sQuery) - 1) . ") ENGINE = InnoDB CHARACTER SET " . $this->sCharaterSet . " COLLATE ".$this->sCollation.";";
    }



    function checkIfRecordExist(&$aRecord) {
        //check if the a record stored in $aRecord exist in the database.
        //$sRecord is an array whose key is the name of the column and the value is the column's value;
        //return number of rows found in the database if exist.
        //otherwise, return 0.
        $sQuery = "SELECT `id` FROM `" . $this->sTableName . "` WHERE `deleted`=0 AND ";
        $aTemporary = array();
        foreach ($aRecord as $sKey => $sValue) {
            $aTemporary[] = "`" . $sKey . "`='" . $this->real_escape_string($sValue) . "'";
        }
        $sQuery.=implode(" AND ", $aTemporary);
        $rResult = $this->queryClover($sQuery);
        if ($rResult && $rResult->num_rows) {
            return $rResult->num_rows;
        } else {
            return 0;
        }
    }

    function checkIfRecordIDExist($iId) {
        //check if record id exist in the database;
        //return number of records found in database if exist;
        //otherwise, return 0;
        $aRecord['id'] = $iId;
        return $this->checkIfRecordExist($aRecord);
    }

    public function readRecord($iId, $sType = "system", $withLabel = FALSE, $withFunction = TRUE) {
        //read a record according to its ID in to an Array which will be return by this function;
        //$sType define which fields should be read out;
        //$withLabel defines whether the label of this field should be used as the Key in the Result Array;
        //TRUE to be yes, and FALSE to be no, in which case the name of the field will be used as the Key.
        //$withFunction defines whether to excute the inLine Function;        

        $aResult = array();
        $aNames = array();
        $aLabels = array();
        $this->getFields($aNames, "name", $sType);
        $this->getFields($aLabels, "label", $sType);
        $sQuery = "SELECT `" . implode("`, `", $aNames) . "`
		FROM " . $this->sTableName . " WHERE `id`='" . $iId . "'";
        $rResult = $this->queryClover($sQuery);
        while ($aRow = $rResult->fetch_assoc()) {
            $iNumFields = count($aNames);
            for ($i = 0; $i < $iNumFields; $i++) {
                if (isset($this->aaTableStructure[$aNames[$i]]["value"])) {
                    $aRow[$aNames[$i]] = $this->inLineFunction($this->aaTableStructure[$aNames[$i]]["value"], $aRow);
                }
                if (isset($this->aaTableStructure[$aNames[$i]]["function"]) && $withFunction) {
                    $aResult[($withLabel ? $aLabels[$i] : $aNames[$i])] = $this->inLineFunction($this->aaTableStructure[$aNames[$i]]["function"], $aRow);
                } else {
                    $aResult[($withLabel ? $aLabels[$i] : $aNames[$i])] = $aRow[$aNames[$i]];
                }
            }
        }
        return $aResult;
    }

    public function addRecords(&$aaData) {
        //return the number of inserted records; 0 if no records were inserted.
        //$aaData is an array of records.
        //each record is an array like Record[key]=value.
        if (count($aaData)) {
            $aNames = array();
            $this->getFields($aNames); //get all the column names.
            $sQuery = "INSERT INTO `" . $this->sTableName . "` ( `" . implode("`, `", $aNames) . "` ) VALUES ";
            $aTemporay = array();
            foreach ($aaData as &$aRecord) {
                $aRecord["id"] = "";
                $aRecord["deleted"] = 0;
                $aRecord["last_modified_by"] = $this->iSessionUserId;
                $aRecord["last_modified_on"] = $this->CloverDateTime2mysqlDateTime(date($this->sDateTimeFormat));
                foreach ($aRecord as &$sData) {
                    $sData = $this->real_escape_string($sData);
                }
                ksort($aRecord);
                $aTemporay[] = "('" . implode("', '", $aRecord) . "')";
            }
            $sQuery.=implode(",", $aTemporay);
            $this->queryClover($sQuery);
            return $this->affected_rows;
        } else {
            return 0;
        }
    }

    public function deleteRecords(&$aIds) {
        //$aIds is an array of IDs.
        //return the number of the successfully deleted records.
        $iResult = 0;
        foreach ($aIds as &$iId) {
            $sQuery = "UPDATE `" . $this->sTableName . "` SET `last_modified_by` ='" . $this->real_escape_string($this->iSessionUserId) . "', `last_modified_on`='" . $this->CloverDateTime2mysqlDateTime(date($this->sDateTimeFormat)) . "', `deleted`=" . $iId . " WHERE `id`='" . $iId . "'";
            if ($this->queryClover($sQuery)) {
                $iResult++;
            }
        }
        return $iResult;
    }

    public function updateRecords(&$aaData) {
        //$aaData is an array of records.
        //each record is an array like Record[key]=value.
        //this function set the `deleted` field of the old record to the new record `id` value
        //and set the `id` value of the new record to the old record `id` value.
        //return the number of the successfully updated records.

        $iResult = 0;
        foreach ($aaData as &$aRecord) {
            // insert new records;
            if ($this->checkIfRecordIDExist($aRecord['id'])) {
                //maker a new copy of the old record 
                //and change the deleted field of the new copy to its original ID; this will serve as the deleted one in the trash can;
                $aOldRecord = $this->readRecord($aRecord['id'], "system", FALSE, FALSE);
                $aOldRecord['deleted'] = $aRecord['id'];
                $aNames = array();
                $this->getFields($aNames); //get all the column names.
                $sQuery = "INSERT INTO `" . $this->sTableName . "` ( `" . implode("`, `", $aNames) . "` ) VALUES ";
                foreach ($aOldRecord as &$sData) {
                    $sData = $this->real_escape_string($sData);
                }
                $aOldRecord['id'] = "";
                ksort($aOldRecord);
                $aTemporay[] = "('" . implode("', '", $aOldRecord) . "')";
                $sQuery.=implode(",", $aTemporay);
                $this->queryClover($sQuery);

                //update the old record information;
                $sQuery = "UPDATE `" . $this->sTableName . "` SET ";
                foreach ($aRecord as $sKey => &$sValue) {
                    $sQuery.="`" . $sKey . "`='" . $this->real_escape_string($sValue) . "',";
                }
                $sQuery.="`last_modified_by`='" . $this->iSessionUserId . "',";
                $sQuery.="`last_modified_on`='" . $this->real_escape_string($this->CloverDateTime2mysqlDateTime(date($this->sDateTimeFormat))) . "'";
                $sQuery.=" WHERE `id`='" . $aRecord['id'] . "'";
                $this->queryClover($sQuery);
                $iResult++;
            }
        }
        return $iResult;
    }

    public function inLineFunction($sFunction, &$aRow = null) {
        //$aRow is the array pointer of the Row from table search;
        //inLIneFunction can use |, &, ., but no parentheses;
        //the order is always from left to right regardless of which operator it is.
        $aaMatches = array();
        $bResult = FALSE;
        $aFn = array();

        preg_match_all('/([\|\&\.])?(!)?([^\|\&\.]+)/', $sFunction, $aaMatches, PREG_SET_ORDER);
        //aaMatches[1] is | (OR) or & (AND) or . (XOR)
        //aaMatches[2] is ! (NOT)
        //aaMatches[3] is the function string.

        foreach ($aaMatches as &$aMatch) {
            //resolve each function
            preg_match('/([^\(\)]+)\(([^\(\)]+)?\)/', $aMatch[3], $aFn);
            //$aFn(1) is the function name;
            //$aFn(2) is the function parameter string;
            if ($aMatch[1] == "|") {
                $bResult = $bResult || ($aMatch[2] == "!" ? !$this->$aFn[1]($aFn[2], $aRow) : $this->$aFn[1]($aFn[2], $aRow));
            } elseif ($aMatch[1] == "&") {
                $bResult = $bResult && ($aMatch[2] == "!" ? !$this->$aFn[1]($aFn[2], $aRow) : $this->$aFn[1]($aFn[2], $aRow));
            } elseif ($aMatch[1] == ".") {
                $bResult = $bResult . $this->$aFn[1]($aFn[2], $aRow);
            } else {
                $bResult = ($aMatch[2] == "!" ? !$this->$aFn[1]($aFn[2], $aRow) : $this->$aFn[1]($aFn[2], $aRow));
            }
        }
        return $bResult;
    }

    /* ---------------------------------------
     * The followings are functions for Ajax
      ---------------------------------------- */

    public function AjaxAdd(&$aaData) {
        //return the total number of  as json in the "changed" parameter.
        $iResult = $this->addRecords($aaData);
        $aResult = array("changed" => $iResult);
        return json_encode($aResult);
    }

    public function AjaxDelete(&$aIds) {
        //return the number of deleted records as json in the "changed" parameter.
        $iResult = $this->deleteRecords($aIds);
        $aResult = array("changed" => $iResult);
        return json_encode($aResult);
    }

    public function AjaxUpdate(&$aaData) {
        //return the number of updated record as json in the "changed" parameter.
        $iResult = $this->updateRecords($aaData);
        $aResult = array("changed" => $iResult);
        return json_encode($aResult);
    }

    public function AjaxRead($id, $sType, $withlabel = FALSE, $withFunction = TRUE) {
        //read the field from a record whose id is $id according to $sType--detail, brief, edit, system;
        return json_encode($this->readRecord($id, $sType, $withlabel, $withFunction));
    }
    
    protected function AjaxSearchLimit(&$sLimit){

        if (isset($_GET['iDisplayStart']) && $_GET['iDisplayLength'] != '-1') {
            $sLimit = "LIMIT " . $this->real_escape_string($_GET['iDisplayStart']) . ", " .
                    $this->real_escape_string($_GET['iDisplayLength']);
        }        
    }
    
    protected function AjaxSearchOrder(&$sOrder, &$aNames){
        if (isset($_GET['iSortCol_0'])) {
            $sOrder = "ORDER BY  ";
            for ($i = 0; $i < intval($_GET['iSortingCols']); $i++) {
                if ($_GET['bSortable_' . intval($_GET['iSortCol_' . $i])] == "true") {
                    //intval( $_GET['iSortCol_'.$i])-1: the -1 is to cancel out the first action_icon column;
                    $sOrder .="`" . $aNames[intval($_GET['iSortCol_' . $i]) - 1] . "` 
				 	" . $this->real_escape_string($_GET['sSortDir_' . $i]) . ", ";
                }
            }
            $sOrder = substr_replace($sOrder, "", -2); //delete last comma or the last two spaces;
            if ($sOrder == "ORDER BY") {
                $sOrder = "";
            }
        }
        
    }
    
    protected function AjaxSearchWhere(&$sWhere, &$aNames){
        $aDetailColumns = array();
        $this->getFields($aDetailColumns, "name", "search");
        $sWhere = "WHERE (`deleted`='0')";
        if ($_GET['sSearch'] != "") {
            $sWhere .= " AND (";
            for ($i = 0; $i < count($aDetailColumns); $i++) {
                $sWhere .= "`" . $aDetailColumns[$i] . "` LIKE '%" . $this->real_escape_string($_GET['sSearch']) . "%' OR ";
            }
            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';
        }

        /* Individual column filtering if individual column search is on */
        for ($i = 0; $i < count($aNames); $i++) {
            //$i+1 is to cancel out the first action_icon column;
            if ($_GET['bSearchable_' . ($i)] == "true" && $_GET['sSearch_' . ($i)] != '') {
                $sWhere .= " AND `" . $aNames[$i] . "` LIKE '%" . $this->real_escape_string($_GET['sSearch_' . $i]) . "%' ";
            }
        }        
    }

    public function AjaxSearch($sType = "brief") {
        //return mysql searched data to the DataTable on the display page through ajax.
        //$sType--as the detail, brief, edit, system in the getFields function.
        //the firt column in the output is the action_icon column.
        $aNames = array();
        $this->getFields($aNames, "name", $sType);

        /* Indexed column (used for fast and accurate table cardinality) */
        $sIndexColumn = "id";

        /* DB table to use */
        $sTable = $this->sTableName;
        
        /* Paging */
        $sLimit = "";
        $this->AjaxSearchLimit($sLimit);
        


        /* Ordering/Sorting */
        $sOrder="";
        $this->AjaxSearchOrder($sOrder, $aNames);
        /*
         * Filtering
         * NOTE this does not match the built-in DataTables filtering which does it
         * word by word on any field. It's possible to do here, but concerned about efficiency
         * on very large tables, and MySQL's regex functionality is very limited
         */
        $sWhere="";
        $this->AjaxSearchWhere($sWhere, $aNames);

        /*
         * SQL queries
         * Get data to display
         */
        $sQuery = "
		SELECT SQL_CALC_FOUND_ROWS `" . implode("`, `", $aNames) . "`
		FROM   $sTable
		$sWhere
		$sOrder
		$sLimit
	";
        $rResult = $this->queryClover($sQuery);
        /* Data set length after filtering */
        $sQuery = "
		SELECT FOUND_ROWS()
	";
        $rResultFilterTotal = $this->queryClover($sQuery);
        $aResultFilterTotal = $rResultFilterTotal->fetch_row();
        $iFilteredTotal = $aResultFilterTotal[0];
        /* Total data set length */
        $sQuery = "
		SELECT COUNT(" . $sIndexColumn . ")
		FROM   $sTable WHERE `deleted`=0
	";
        $rResultTotal = $this->queryClover($sQuery);
        $aTotalRow = $rResultTotal->fetch_row();
        $iTotal = $aTotalRow[0];


        /* Output as JSON */
        $aOutput = array(
            "sEcho" => intval($_GET['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
        );

        while ($aRow = $rResult->fetch_assoc()) {
            $aTempRow = array();
            $sAct = "";
            /* put actions icons here for each row;
             * which is determined by the 
             */

            foreach ($this->aActionIcons as $sKey => &$Action) {

                if ($this->inLineFunction($this->aActionAuths[$sKey], $aRow)) {
                    $sAct.=$Action;
                }
            }
            $aTempRow[] = $sAct;
            $iNumFields = count($aNames);
            for ($i = 0; $i < $iNumFields; $i++) {
                if (isset($this->aaTableStructure[$aNames[$i]]["value"])) {
                    $aRow[$aNames[$i]] = $this->inLineFunction($this->aaTableStructure[$aNames[$i]]["value"], $aRow);
                }
                if (isset($this->aaTableStructure[$aNames[$i]]["function"])) {
                    $aTempRow[] = $this->inLineFunction($this->aaTableStructure[$aNames[$i]]["function"], $aRow);
                } else {
                    $aTempRow[] = $aRow[$aNames[$i]];
                }
            }
            $aOutput['aaData'][] = $aTempRow;
        }

        return json_encode($aOutput);
    }

    public function mysqlDateTime2CloverDateTime($sDateTime) {
        if ($sDateTime == "0000-00-00 00:00:00") {
            return "";
        } else {
            $mysqlDateTime = DateTime::createFromFormat("Y-m-d H:i:s", $sDateTime);
            if ($mysqlDateTime) {
                return $mysqlDateTime->format($this->sDateTimeFormat);
            } else {
                return $sDateTime;
            }
        }
    }

    public function CloverDateTime2mysqlDateTime($sDateTime) {
        if ($sDateTime == "") {
            return "0000-00-00 00:00:00";
        } else {
            $CloverDateTime = DateTime::createFromFormat($this->sDateTimeFormat, $sDateTime);
            if ($CloverDateTime) {
                return $CloverDateTime->format("Y-m-d H:i:s");
            } else {
                return $sDateTime;
            }
        }
    }

//inLine functions
    public function email2Link($index, &$aRow) {
        //inLine function: change the user's email to an link.
        //return email with link
        return '<a href="mailto:' . $aRow[$index] . '">' . $aRow[$index] . '</a>';
    }

    public function mysql2CloverDateTime($index, &$aRow) {
        return $this->mysqlDateTime2CloverDateTime($aRow[$index]);
    }

    public function userID2Name($index, &$aRow) {
        //inLine function: convert user ID to user name
        //return user name with email link
        $iId = $aRow[$index];
        if ($iId == 0) {
            return "";
        } else {
            $sQuery = "SELECT `name`, `email` FROM `users` WHERE `deleted`=0 AND `id`='" . $iId . "'";
            $rResult = $this->queryClover($sQuery);
            $row = $rResult->fetch_assoc();
            if ($row) {
                return '<a href="mailto:' . $row["email"] . '" title="Contact ' . $row["name"] . ': ' . $row["email"] . '">' . $row["name"] . '</a>';
            }
            else
                return "Deleted User";
        }
    }

    public function checkUserIdentity($sRules, &$aRow) {
        //inLine function to check if the user Identify according to sRules.
        //TRUE for yes; FALSE for no.
        $aRules = explode(",", $sRules);
        if (in_array($this->sSessionUserType, $aRules) !== false) {
            return true;
        } elseif ((in_array("self", $aRules) !== false) && (isset($aRow["email"]) ? ($aRow["email"] == $this->sSessionUserEmail) : ($aRow["user_id"] == $this->iSessionUserId))) {
            return true;
        } else {
            return FALSE;
        }
    }

    public function URL2Link($index, &$aRow) {

        //inLine function: change the url to a link.
        //return url with link.
        return '<a href="' . $aRow[$index] . '">' . $aRow[$index] . '</a>';
    }

}

?>
