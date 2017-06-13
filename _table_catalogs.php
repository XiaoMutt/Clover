<?php

include_once '_extends.php';

class OperateCatalogsTable extends OperateTables {

    public function __construct() {
        $this->sTableName = 'catalogs';
        $this->sTableLabel = 'Catalogs';
        $this->aActionIcons["detail"]='<img src="icons/page_white_text_width.png" name="action_close" title="Show/Hide details">';
        $this->aActionIcons["edit"]='<img src="icons/page_white_edit.png" name="action_edit" title="Edit this item">';
        $this->aActionIcons["delete"]='<img src="icons/cross.png" name="action_delete" title="Delete this item">';
        $this->aActionIcons["order"]='<img src="icons/cart_add.png" name="action_order" title="Order this item">';
        $this->aActionAuths["detail"]='checkUserIdentity(user,admin)';
        $this->aActionAuths["edit"]='checkUserIdentity(user,admin)';
        $this->aActionAuths["delete"]='checkUserIdentity(user,admin)';
        $this->aActionAuths["order"]='checkUserIdentity(user,admin)';     
        $this->aaTableStructure = array(
            "name"=>array("name" => "name", "label" => "Name", "brief" => "2", "edit" => "2", "detail" => "2", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
            "company"=>array("name" => "company", "label" => "Company", "brief" => "3", "edit" => "3", "detail" => "3", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
            "company_catalog_number"=>array("name" => "company_catalog_number", "label" => "Company Catalog Number", "brief" => "0", "edit" => "4", "detail" => "4", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
            "dealer"=>array("name" => "dealer", "label" => "Dealer", "brief" => "0", "edit" => "5", "detail" => "5", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
            "dealer_catalog_number"=>array("name" => "dealer_catalog_number", "label" => "Dealer Catalog Number", "brief" => "0", "edit" => "6", "detail" => "6", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
            "unit_size"=>array("name" => "unit_size", "label" => "Unit Size", "brief" => "7", "edit" => "7", "detail" => "7", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
            "unit_price"=>array("name" => "unit_price", "label" => "Unit Price", "brief" => "8", "edit" => "8", "detail" => "8", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
            "product_website"=>array("name" => "product_website", "label" => "Product website", "brief" => "0", "edit" => "9", "detail" => "9", "search" => "1", "data_type" => "varchar(255)", "function"=>"URL2Link(product_website)"),
            "comments"=>array("name" => "comments", "label" => "Comments", "brief" => "0", "edit" => "10", "detail" => "10", "search" => "1", "data_type" => "varchar(512) NOT NULL")
        );
        parent::__construct();
    }

}

?>
