<?php

include_once '_extends.php';

class OperateOrdersTable extends OperateTables {

    public function __construct() {
        $this->sTableName = 'orders';
        $this->sTableLabel = 'Orders';
        $this->aActionIcons["detail"] = '<img src="icons/page_white_text_width.png" name="action_close" title="Show/Hide details">';
        $this->aActionIcons["edit"] = '<img src="icons/page_white_edit.png" name="action_edit" title="Edit this order">';
        $this->aActionIcons["delete"] = '<img src="icons/cross.png" name="action_delete" title="Delete this order">';
        $this->aActionIcons["order"] = '<img src="icons/cart_add.png" name="action_order" title="Order this item again">';
        $this->aActionAuths["detail"] = 'checkUserIdentity(user,admin)';
        $this->aActionAuths["edit"] = 'checkUserIdentity(user,admin)';
        $this->aActionAuths["delete"] = 'checkUserIdentity(user,admin)';
        $this->aActionAuths["order"] = 'checkUserIdentity(user,admin)';
        $this->aaTableStructure = array(
            "name" => array("name" => "name", "label" => "Name", "brief" => "2", "edit" => "2", "detail" => "2", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
            "company" => array("name" => "company", "label" => "Company", "brief" => "3", "edit" => "3", "detail" => "3", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
            "company_catalog_number" => array("name" => "company_catalog_number", "label" => "Company Catalog Number", "brief" => "0", "edit" => "4", "detail" => "4", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
            "dealer" => array("name" => "dealer", "label" => "Dealer", "brief" => "0", "edit" => "5", "detail" => "5", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
            "dealer_catalog_number" => array("name" => "dealer_catalog_number", "label" => "Dealer Catalog Number", "brief" => "0", "edit" => "6", "detail" => "6", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
            "unit_size" => array("name" => "unit_size", "label" => "Unit Size", "brief" => "7", "edit" => "7", "detail" => "7", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
            "unit_price" => array("name" => "unit_price", "label" => "Unit Price", "brief" => "8", "edit" => "8", "detail" => "8", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
            "quantity" => array("name" => "quantity", "label" => "Quantity", "brief" => "9", "edit" => "9", "detail" => "9", "search" => "1", "data_type" => "int(1) unsigned NOT NULL DEFAULT '0'"),
            "total_price" => array("name" => "total_price", "label" => "Total Price", "brief" => "10", "edit" => "10", "detail" => "10", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
            "product_website" => array("name" => "product_website", "label" => "Product website", "brief" => "0", "edit" => "11", "detail" => "11", "search" => "1", "data_type" => "varchar(255) NOT NULL", "function" => "URL2Link(product_website)"),
            "requested_by" => array("name" => "requested_by", "label" => "Requested by", "brief" => "0", "edit" => "0", "detail" => "12", "search" => "1", "data_type" => "int(12) unsigned NOT NULL DEFAULT '0'", "function" => "userID2Name(requested_by)"),
            "requested_on" => array("name" => "requested_on", "label" => "Requested on", "brief" => "0", "edit" => "0", "detail" => "13", "search" => "0", "data_type" => "DATETIME NULL DEFAULT NULL", "value" => "mysqlDate2CloverDate(requested_on)"),
            "ordered_by" => array("name" => "ordered_by", "label" => "Ordered by", "brief" => "0", "edit" => "0", "detail" => "14", "search" => "1", "data_type" => "int(12) unsigned NOT NULL DEFAULT '0'", "function" => "userID2Name(ordered_by)"),
            "ordered_on" => array("name" => "ordered_on", "label" => "Ordered on", "brief" => "15", "edit" => "15", "detail" => "15", "search" => "0", "data_type" => "DATETIME NULL DEFAULT NULL", "value" => "mysqlDate2CloverDate(ordered_on)", "function" => "EmptyDate2Icon(ordered_on)"),
            "received_by" => array("name" => "received_by", "label" => "Received by", "brief" => "0", "edit" => "0", "detail" => "16", "search" => "1", "data_type" => "int(12) unsigned NOT NULL DEFAULT '0'", "function" => "userID2Name(received_by)"),
            "received_on" => array("name" => "received_on", "label" => "Received on", "brief" => "17", "edit" => "17", "detail" => "17", "search" => "0", "data_type" => "DATETIME NULL DEFAULT NULL", "value" => "mysqlDate2CloverDate(received_on)", "function" => "EmptyDate2Icon(received_on)"),
            "comments" => array("name" => "comments", "label" => "Comments", "brief" => "0", "edit" => "18", "detail" => "18", "search" => "1", "data_type" => "varchar(512) NOT NULL"),
        );
        parent::__construct();
    }

    protected function AjaxSearchWhere(&$sWhere, &$aNames) {
        $sFrom = $this->CloverDate2mysqlDate($_GET["from"]) . " 00:00:01";
        $sTo = $this->CloverDate2mysqlDate($_GET["to"]) . " 23:59:59";
        $aDetailColumns = array();
        $this->getFields($aDetailColumns, "name", "search");
        $sWhere = "WHERE (`deleted`='0') AND (`requested_on` BETWEEN '" . $sFrom . "' AND '" . $sTo . "')";
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
                if ($aNames[$i] == "ordered_on" || $aNames[$i] == "received_on") {
                    $sWhere .= " AND `" . $aNames[$i] . "` LIKE '" . $this->real_escape_string($this->CloverDate2mysqlDate($_GET['sSearch_' . $i])) . " 00:00:00' ";
                } else {
                    $sWhere .= " AND `" . $aNames[$i] . "` LIKE '%" . $this->real_escape_string($_GET['sSearch_' . $i]) . "%' ";
                }
            }
        }
    }

    public function EmptyDate2Icon($index, &$aRow) {
        if ($index == "ordered_on" && $aRow[$index] == "") {
            return '<img src="icons/cart_go.png" name="action_ordered" title="Mark as ordered">';
        } elseif ($index == "received_on" && $aRow[$index] == "") {
            return '<img src="icons/package_green.png" name="action_received" title="Mark as received">';
        } else {
            return $aRow[$index];
        }
    }

    public function mysqlDate2CloverDate($index, &$aRaw) {
        if ($aRaw[$index] != "0000-00-00 00:00:00") {
            $mysqlDateTime = DateTime::createFromFormat("Y-m-d H:i:s", $aRaw[$index]);
            if ($mysqlDateTime) {
                return $mysqlDateTime->format($this->sDateFormat);
            }
        }
        return "";
    }

    public function CloverDate2mysqlDate($sDate) {
        if ($sDate == "") {
            return "0000-00-00";
        } else {
            $CloverDate = DateTime::createFromFormat($this->sDateFormat, $sDate);
            if ($CloverDate) {
                return $CloverDate->format("Y-m-d");
            } else {
                return $sDate;
            }
        }
    }

}

?>
