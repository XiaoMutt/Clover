<?php

include_once '_extends.php';

class OperateStocksTable extends OperateTables {

    public $aStockLabels = array(
        'agrobacterium_stock' => 'Argobacterium Stock',
        'antibody_stock' => 'Antibody Stock',
        'ecoli_stock' => 'E. coli Stock',
        'plasmid_stock' => 'Plasmid Stock',
        'primer_stock' => 'Primer Stock',
        'seed_stock' => 'Seed Stock',
        'materials_sent_out' => 'Materials Sent Out'
    );

    public function __construct($sStockType = "agrobacterium_stock") {
        $this->aActionIcons["detail"] = '<img src="icons/page_white_text_width.png" name="action_close" title="Show/Hide details">';
        $this->aActionIcons["edit"] = '<img src="icons/page_white_edit.png" name="action_edit" title="Edit this stock">';
        $this->aActionIcons["delete"] = '<img src="icons/cross.png" name="action_delete" title="Delete this stock">';
        $this->aActionAuths["detail"] = 'checkUserIdentity(user,admin)';
        $this->aActionAuths["edit"] = 'checkUserIdentity(user,admin)';
        $this->aActionAuths["delete"] = 'checkUserIdentity(user,admin)';

        if ($sStockType == "") {
            $sStockType = "agrobacterium_stock";
        }
        $this->sTableName = $sStockType;
        $this->sTableLabel = $this->aStockLabels[$sStockType];

        switch ($sStockType) {
            case 'agrobacterium_stock':
                $this->aaTableStructure = array(
                    "name" => array("name" => "name", "label" => "Name", "brief" => "2", "edit" => "2", "detail" => "2", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "location" => array("name" => "location", "label" => "Storage Location", "brief" => "3", "edit" => "3", "detail" => "3", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "strain" => array("name" => "strain", "label" => "Strain", "brief" => "0", "edit" => "4", "detail" => "4", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "anti_bacteria" => array("name" => "anti_bacteria", "label" => "Antibiotic Resistance in Bacteria", "brief" => "4", "edit" => "5", "detail" => "5", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "anti_plant" => array("name" => "anti_plant", "label" => "Antibiotic Resistance in Planta", "brief" => "0", "edit" => "6", "detail" => "6", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "made_by" => array("name" => "made_by", "label" => "Made by", "brief" => "5", "edit" => "7", "detail" => "7", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "made_on" => array("name" => "made_on", "label" => "Made on", "brief" => "0", "edit" => "8", "detail" => "8", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "comments" => array("name" => "comments", "label" => "Comments", "brief" => "0", "edit" => "9", "detail" => "9", "search" => "1", "data_type" => "varchar(512) NOT NULL")
                );
                break;
            case 'antibody_stock':
                $this->aaTableStructure = array(
                    "name" => array("name" => "name", "label" => "Name", "brief" => "2", "edit" => "2", "detail" => "2", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "location" => array("name" => "location", "label" => "Storage Location", "brief" => "3", "edit" => "3", "detail" => "3", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "antigen" => array("name" => "antigen", "label" => "Antigen", "brief" => "4", "edit" => "4", "detail" => "4", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "animal_used" => array("name" => "animal_used", "label" => "Animal Used", "brief" => "5", "edit" => "5", "detail" => "5", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "made_by" => array("name" => "made_by", "label" => "Made by", "brief" => "0", "edit" => "6", "detail" => "6", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "made_on" => array("name" => "made_on", "label" => "Made on", "brief" => "0", "edit" => "7", "detail" => "7", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "comments" => array("name" => "comments", "label" => "Comments", "brief" => "8", "edit" => "8", "detail" => "8", "search" => "1", "data_type" => "varchar(512) NOT NULL")
                );
                break;
            case 'ecoli_stock':
                $this->aaTableStructure = array(
                    "name" => array("name" => "name", "label" => "Name", "brief" => "2", "edit" => "2", "detail" => "2", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "location" => array("name" => "location", "label" => "Storage Location", "brief" => "3", "edit" => "3", "detail" => "3", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "strain" => array("name" => "strain", "label" => "Strain", "brief" => "0", "edit" => "4", "detail" => "4", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "anti_bacteria" => array("name" => "anti_bacteria", "label" => "Antibiotic Resistance in Bacteria", "brief" => "4", "edit" => "5", "detail" => "5", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "anti_plant" => array("name" => "anti_plant", "label" => "Antibiotic Resistance in Planta", "brief" => "0", "edit" => "6", "detail" => "6", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "made_by" => array("name" => "made_by", "label" => "Made by", "brief" => "5", "edit" => "7", "detail" => "7", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "made_on" => array("name" => "made_on", "label" => "Made on", "brief" => "0", "edit" => "8", "detail" => "8", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "comments" => array("name" => "comments", "label" => "Comments", "brief" => "0", "edit" => "9", "detail" => "9", "search" => "1", "data_type" => "varchar(512) NOT NULL")
                );

                break;
            case 'plasmid_stock':
                $this->aaTableStructure = array(
                    "name" => array("name" => "name", "label" => "Name", "brief" => "2", "edit" => "2", "detail" => "2", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "location" => array("name" => "location", "label" => "Storage Location", "brief" => "3", "edit" => "3", "detail" => "3", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "anti_bacteria" => array("name" => "anti_bacteria", "label" => "Antibiotic Resistance in Bacteria", "brief" => "4", "edit" => "4", "detail" => "4", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "anti_plant" => array("name" => "anti_plant", "label" => "Antibiotic Resistance in Planta", "brief" => "0", "edit" => "5", "detail" => "5", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "made_by" => array("name" => "made_by", "label" => "Made by", "brief" => "5", "edit" => "6", "detail" => "6", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "made_on" => array("name" => "made_on", "label" => "Made on", "brief" => "0", "edit" => "7", "detail" => "7", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "comments" => array("name" => "comments", "label" => "Comments", "brief" => "0", "edit" => "8", "detail" => "8", "search" => "1", "data_type" => "varchar(512) NOT NULL")
                );
                break;

            case 'primer_stock':
                $this->aaTableStructure = array(
                    "name" => array("name" => "name", "label" => "Name", "brief" => "2", "edit" => "2", "detail" => "2", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "location" => array("name" => "location", "label" => "Storage Location", "brief" => "3", "edit" => "3", "detail" => "3", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "Sequence" => array("name" => "Sequence", "label" => "Sequence", "brief" => "4", "edit" => "4", "detail" => "4", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "type" => array("name" => "type", "label" => "Primer Type", "brief" => "0", "edit" => "5", "detail" => "5", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "tm" => array("name" => "tm", "label" => "Tm (℃)", "brief" => "5", "edit" => "6", "detail" => "6", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "made_by" => array("name" => "made_by", "label" => "Made by", "brief" => "0", "edit" => "7", "detail" => "7", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "made_on" => array("name" => "made_on", "label" => "Made on", "brief" => "0", "edit" => "8", "detail" => "8", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "comments" => array("name" => "comments", "label" => "Comments", "brief" => "0", "edit" => "9", "detail" => "9", "search" => "1", "data_type" => "varchar(512) NOT NULL")
                );
                break;
            case 'seed_stock':
                $this->aaTableStructure = array(
                    "name" => array("name" => "name", "label" => "Name", "brief" => "2", "edit" => "2", "detail" => "2", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "location" => array("name" => "location", "label" => "Storage Location", "brief" => "3", "edit" => "3", "detail" => "3", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "ecotype" => array("name" => "ecotype", "label" => "Ecotype", "brief" => "0", "edit" => "4", "detail" => "4", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "anti_plant" => array("name" => "anti_plant", "label" => "Antibiotic Resistance in Planta", "brief" => "4", "edit" => "5", "detail" => "5", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "made_by" => array("name" => "made_by", "label" => "Made by", "brief" => "5", "edit" => "6", "detail" => "6", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "made_on" => array("name" => "made_on", "label" => "Made on", "brief" => "0", "edit" => "7", "detail" => "7", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "comments" => array("name" => "comments", "label" => "Comments", "brief" => "0", "edit" => "8", "detail" => "8", "search" => "1", "data_type" => "varchar(512) NOT NULL")
                );
                break;
            case 'materials_sent_out':
                $this->aaTableStructure = array(
                    "materials" => array("name" => "materials", "label" => "Materials", "brief" => "2", "edit" => "2", "detail" => "2", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "sent_to" => array("name" => "sent_to", "label" => "Sent to", "brief" => "3", "edit" => "3", "detail" => "3", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "address" => array("name" => "address", "label" => "Address", "brief" => "0", "edit" => "4", "detail" => "4", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "sent_by" => array("name" => "sent_by", "label" => "Sent by", "brief" => "5", "edit" => "6", "detail" => "6", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "sent_on" => array("name" => "sent_on", "label" => "Sent on", "brief" => "0", "edit" => "7", "detail" => "7", "search" => "1", "data_type" => "varchar(128) NOT NULL"),
                    "comments" => array("name" => "comments", "label" => "Comments", "brief" => "0", "edit" => "8", "detail" => "8", "search" => "1", "data_type" => "varchar(512) NOT NULL")
                );
                break;                
                
        }
        parent::__construct();
    }

    public function createTable() {
        $sQuery = "";
        $aStocks = array_keys($this->aStockLabels);
        foreach ($aStocks as &$sStock) {
            $this->__construct($sStock);
            $sQuery.=parent::createTable();
        }
        return $sQuery;
    }

    private function getJargons($sKeyWords) {
        $sQuery = "SELECT `jargon` FROM `jargons` WHERE `deleted`=0 AND `term`='" . $this->real_escape_string($sKeyWords) . "' LIMIT 0, 10 ";
        $rResult1 = $this->queryClover($sQuery);
        $aResult = array();
        $aResult[] = $sKeyWords;
        while ($aRow = $rResult1->fetch_row()) {
            $aResult[] = $aRow[0];
        }
        $sQuery = "SELECT `term` FROM `jargons` WHERE `deleted`=0 AND `jargon`='" . $this->real_escape_string($sKeyWords) . "' LIMIT 0, 10 ";
        $rResult2 = $this->queryClover($sQuery);
        while ($aRow = $rResult2->fetch_row()) {
            $aResult[] = $aRow[0];
        }
        return array_unique($aResult);
    }

    private function getJargonReplacedKeywords(&$aFinalKeywords) {
        $aaKeywords = array();
        $aWords = preg_split("/[\s,\\\\\|\\/:]+/", $aFinalKeywords[0], 10);
        foreach ($aWords as &$sWords) {
            $aaKeywords[$sWords] = $this->getJargons($sWords);
        }
        foreach ($aaKeywords as $sKey => &$aKeywords) {
            $iFinalKeywordsCount = count($aFinalKeywords);
            for ($i = 0; $i < $iFinalKeywordsCount; $i++) {
                foreach ($aKeywords as &$sKeyword) {
                    if ($sKey != $sKeyword) {
                        $aFinalKeywords[] = preg_replace("/" . $sKey . "/", $sKeyword, $aFinalKeywords[$i]);
                    }
                }
            }
        }
    }

    protected function AjaxSearchWhere(&$sWhere, &$aNames) {
        //search all searchable columns;
        $aDetailColumns = array();
        $this->getFields($aDetailColumns, "name", "search");


        //get keywords
        $aFinalKeywords[] = $_GET['sSearch'];
        if ($_GET["checkjargon"] == "true") {
            $this->getJargonReplacedKeywords($aFinalKeywords);
        }

        $sWhere = "WHERE (`deleted`='0')";
        if ($_GET['sSearch'] != "") {
            $sWhere .= " AND (";
            for ($i = 0; $i < count($aDetailColumns); $i++) {
                foreach ($aFinalKeywords as &$sKeyword) {
                    $sWhere .= "`" . $aDetailColumns[$i] . "` LIKE '%" . $this->real_escape_string($sKeyword) . "%' OR ";
                }
            }
            $sWhere = substr_replace($sWhere, "", -3);
            $sWhere .= ')';
        }

        /* Individual column filtering if individual column search is on */
        for ($i = 0; $i < count($aNames); $i++) {
            //$i+1 is to cancel out the first action_icon column;
            if ($_GET['bSearchable_' . ($i)] == "true" && $_GET['sSearch_' . ($i)] != '') {
                $sWhere .= " AND (";
                $aKeywords = array();
                $aKeywords[] = $_GET['sSearch_' . ($i)];
                if ($_GET["checkjargon"] == "true") {
                    $this->getJargonReplacedKeywords($aKeywords);
                }
                foreach ($aKeywords as $sKeyword) {
                    $sWhere .= "`" . $aNames[$i] . "` LIKE '%" . $this->real_escape_string($sKeyword) . "%' OR ";
                }
                $sWhere = substr_replace($sWhere, "", -3);
                $sWhere .= ')';
            }
        }
    }

}

?>
