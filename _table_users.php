<?php

include_once '_extends.php';

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class OperateUsersTable extends OperateTables {

    function __construct($bLogin=true) {
        $this->sTableName = 'users';
        $this->sTableLabel='Users';
        
        $this->aaTableStructure = array(
            "email" => array("name" => "email", "label" => "Email", "brief" => "3", "edit" => "3", "detail" => "3", "search" => "1", "data_type" => "char(64)  NOT NULL", "function" => "email2Link(email)"), /* User ID: user email. If this is a group the email will be GroupName@group */
            "password" => array("name" => "password", "label" => "Password", "brief" => "0", "edit" => "4", "detail" => "0", "search" => "0", "data_type" => "char(64) NOT NULL"), /* Password */
            "name" => array("name" => "name", "label" => "Name", "brief" => "2", "edit" => "2", "detail" => "2", "search" => "1", "data_type" => "varchar(128) NOT NULL"), /* User's Name */
            "identity" => array("name" => "identity", "label" => "Identity", "brief" => "5", "edit" => "5", "detail" => "5", "search" => "1", "data_type" => "enum('visitor','user','admin')  NOT NULL DEFAULT 'visitor'"), /* user type */
            "description" => array("name" => "description", "label" => "Description", "brief" => "6", "edit" => "6", "detail" => "6", "search" => "1", "data_type" => "varchar(256) NOT NULL"), /* User's Description */
        );
        parent::__construct($bLogin);

        $this->aActionIcons["detail"] = '<img src="icons/page_white_text_width.png" name="action_close" title="Show/Hide details">';
        $this->aActionIcons["edit"] = '<img src="icons/page_white_edit.png" name="action_edit" title="Edit this user">';
        $this->aActionIcons["delete"] = '<img src="icons/cross.png" name="action_delete" title="Delete this user">';
        $this->aActionIcons["password"] = '<img src="icons/key.png" name="action_password" title="Change the password of this user">';
        $this->aActionAuths["detail"] = 'checkUserIdentity(user,admin)';
        $this->aActionAuths["edit"] = 'checkUserIdentity(self,admin)';
        $this->aActionAuths["delete"] = 'checkUserIdentity(admin)&!checkUserIdentity(self)';
        $this->aActionAuths["password"] = 'checkUserIdentity(self)';
    }

    
    function checkIfUserExist($sEmail) {
        //check if the email has been used for registration.
        //return TRUE IF yes, or FALSE if no.
        $aEmail = array("email" => trim($sEmail), "deleted" => '0');
        return $this->checkIfRecordExist($aEmail);
    }

    public function AddUser($aUser) {
        //add a user to the userngroup table
        //TRUE if added. FALSE if the email already exists.
        if (!$this->checkIfUserExist($aUser["email"])) {
            $aaUser = array($aUser);
            $this->addRecords($aaUser);
            return TRUE; //added
        } else {
            return FALSE; //user email already exists;
        }
    }


    public function AjaxGenerateEmailList() {
        //generate the email list containing all the users who are not visitors.
        //return an array $aData["changed"]=1 if successful and $aData["emaillist"]=emaillist.
        //return an $aData["changed"]=0 if unsuccessful.
        $sQuery = "SELECT `email`
            FROM `users` WHERE `deleted`='0' AND `identity`!='visitor'";
        $sList = "";
        $rResult = $this->queryClover($sQuery);
        if ($rResult) {
            while ($aRow = $rResult->fetch_assoc()) {
                $sList.=$aRow["email"] . ";";
            }
            $aData = Array("changed" => 1, "emaillist" => $sList); //success
        } else {
            $aData = Array("changed" => 0, "emaillist" => "Cannot fetch email list."); //error
        }

        return json_encode($aData);
    }

}

?>
