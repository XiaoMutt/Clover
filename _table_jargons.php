<?php
include_once '_extends.php';

class OperateJargonsTable extends OperateTables{
    public function __construct() {
        $this->sTableName='jargons';
        $this->sTableLabel='Jargon';
        
        $this->aaTableStructure = array(
            "term" => array("name" => "term", "label" => "Term", "brief" => "3", "edit" => "3", "detail" => "3", "search" => "1","data_type" => "varchar(128) NOT NULL"), 
            "jargon" => array("name" => "jargon", "label" => "Jargon", "brief" => "4", "edit" => "4", "detail" => "4", "search" => "1","data_type" => "varchar(128) NOT NULL"),
            "comments" => array("name" => "comments", "label" => "Comments", "brief" => "9", "edit" => "9", "detail" => "9", "search" => "1","data_type" => "varchar(512) NOT NULL")            
        );
        parent::__construct();

        $this->aActionIcons["detail"] = '<img src="icons/page_white_text_width.png" name="action_close" title="Show/Hide details">';
        $this->aActionIcons["edit"] = '<img src="icons/page_white_edit.png" name="action_edit" title="Edit this jargon">';
        $this->aActionIcons["delete"] = '<img src="icons/cross.png" name="action_delete" title="Delete this jargon">';
        $this->aActionAuths["detail"] = 'checkUserIdentity(admin,user)';
        $this->aActionAuths["edit"] = 'checkUserIdentity(admin,user)';
        $this->aActionAuths["delete"] = 'checkUserIdentity(admin,user)';
    }
}
?>
