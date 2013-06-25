<?php

include_once(dirname(__FILE__) . '/tcpdf/tcpdf.php');

class _TCPDF extends TCPDF {

    public function out($s) {
        return $this->_out($s);
    }

    public function k() {
        return $this->k;
    }

    public function h() {
        return $this->h;
    }

}

class IReportRender {

    private $RParser;
    /**
     *
     * @var _TCPDF
     */
    private $pdf;
    private $group_pointer;
    private $group_name;
    public $provider;
    public $outpage; //I/D/F/S
    public $filename = '';

    public function __construct($ReportParser) {
        $this->RParser = $ReportParser;

      if ($this->RParser->pageSetting["orientation"] == "P")
            $conf = array(
                (string)$this->RParser->pageSetting["pageWidth"],
                (string)$this->RParser->pageSetting["pageHeight"]);

         else
            $conf = array(
                    (string)$this->RParser->pageSetting["pageHeight"],
                    (string)$this->RParser->pageSetting["pageWidth"]);

        $this->pdf = new _TCPDF($this->RParser->pageSetting["orientation"], 'px',$conf);

        $this->pdf->setPrintHeader(false);
        $this->pdf->setPrintFooter(false);
    }

    public function execute() {

        $this->pdf->SetLeftMargin($this->RParser->pageSetting["leftMargin"]);
        $this->pdf->SetRightMargin($this->RParser->pageSetting["rightMargin"]);
        $this->pdf->SetTopMargin($this->RParser->pageSetting["topMargin"]);
        $this->pdf->SetAutoPageBreak(true, $this->RParser->pageSetting["bottomMargin"] / 2);
        $this->pdf->AliasNbPages();

        $this->global_pointer = 0;

        $this->render();

        if ($this->filename == "")
            $this->filename = $this->RParser->pageSetting["name"] . ".pdf";
        return $this->pdf->Output($this->filename, $this->outpage);
    }

    private function render() {
        $this->provider->execute($this->RParser);
        $this->data = $this->provider->getData();
        foreach ($this->RParser->band as $band) {
            switch ($band["name"]) {
                case "pageHeader":
                    if (!$this->RParser->newPageGroup) {
                        $headerY = $this->RParser->pageSetting["topMargin"] + $this->RParser->pageHeader[0]["height"];
                        $this->renderPageHeader($headerY);
                    } else {
                        $this->renderPageHeaderNewPage();
                    }
                    break;
                case "title":
                    // Todo: render title.
                    break;
                case "detail":
                    if (!$this->RParser->newPageGroup) {
                        $this->renderDetail();
                    } else {
                        $this->renderDetailNewPage();
                    }
                    break;
                case "group":
                    $this->group_pointer = $band["groupExpression"];
                    $this->group_name = $band["gname"];
                    break;
                default:
                    break;
            }
        }
    }

    private function background() {
        foreach ($this->RParser->background as $out) {
            switch (isset($out["hidden_type"])?$out["hidden_type"]:null) {
                case "field":
                    $this->display($out, $this->RParser->pageSetting["topMargin"], true);
                    break;
                default:
                    $this->display($out, $this->RParser->pageSetting["topMargin"], false);
                    break;
            }
        }
    }

    private function renderPageHeader($headerY) {
        $this->pdf->AddPage();
        $this->background();
        if (isset($this->RParser->pageHeader)) {
            $this->RParser->pageHeader[0]["y_axis"] = $this->RParser->pageSetting["topMargin"];
        }
        foreach ($this->RParser->pageHeader as $out) {
            switch (isset($out["hidden_type"])?$out["hidden_type"]:null) {
                case "field":
                    $this->display($out, $this->RParser->pageHeader[0]["y_axis"], true);
                    break;
                default:
                    $this->display($out, $this->RParser->pageHeader[0]["y_axis"], false);
                    break;
            }
        }
    }

    private function renderPageHeaderNewPage() {
        $this->pdf->AddPage();
        $this->background();
        if (isset($this->RParser->pageHeader)) {
            $this->RParser->pageHeader[0]["y_axis"] = $this->RParser->pageSetting["topMargin"];
        }
        foreach ($this->RParser->pageHeader as $out) {
            switch (isset($out["hidden_type"])?$out['hidden_type']:null) {
                case "textfield":
                    $this->display($out, $this->RParser->pageHeader[0]["y_axis"], true);
                    break;
                default:
                    $this->display($out, $this->RParser->pageHeader[0]["y_axis"], true);
                    break;
            }
        }
        $this->renderGroupHeader($this->RParser->pageSetting["topMargin"] + $this->RParser->pageHeader[0]["height"]);
    }

    private function renderPageFooter() {
        if (isset($this->pageFooter)) {
            foreach ($this->pageFooter as $out) {
                switch ($out["hidden_type"]) {
                    case "field":
                        $this->display($out, $this->RParser->pageSetting["pageHeight"] - $this->pageFooter[0]["height"] - $this->RParser->pageSetting["bottomMargin"], true);
                        break;
                    default:
                        $this->display($out, $this->RParser->pageSetting["pageHeight"] - $this->pageFooter[0]["height"] - $this->RParser->pageSetting["bottomMargin"], false);
                        break;
                }
            }
        } else {
            $this->renderLastPageFooter();
        }
    }

    private function renderLastPageFooter() {
        if (isset($this->RParser->lastPageFooter)) {
            foreach ($this->RParser->lastPageFooter as $out) {
                switch ($out["hidden_type"]) {
                    case "field":
                        $this->display($out, $this->RParser->pageSetting["pageHeight"] - $this->lastPageFooter[0]["height"] - $this->RParser->pageSetting["bottomMargin"], true);
                        break;
                    default:
                        $this->display($out, $this->RParser->pageSetting["pageHeight"] - $this->lastPageFooter[0]["height"] - $this->RParser->pageSetting["bottomMargin"], false);
                        break;
                }
            }
        }
    }

    private function NbLines($w, $txt) {
        //Computes the number of lines a MultiCell of width w will take
        ///return $this->pdf->getNumLines($txt,$w);
        //$cw = &$this->pdf->CurrentFont['cw']; // protected pro
        if ($w == 0)
            $w = $this->pdf->w - $this->pdf->rMargin - $this->pdf->x;
        $cell_margins = $this->pdf->getCellMargins();
        $cMargin = $cell_margins['L']+$cell_margins['R'];
        $wmax = ($w - 2 * $cMargin) * 1000 / $this->pdf->getFontSize();
        $s = str_replace("\r", '', $txt);
        $nb = strlen($s);
        if ($nb > 0 and $s[$nb - 1] == "\n")
            $nb--;
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        $nl = 1;
        while ($i < $nb) {
            $c = $s[$i];
            if ($c == "\n") {
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
                continue;
            }
            if ($c == ' ')
                $sep = $i;
            $l+=$this->pdf->getRawCharWidth($c);  // Was: $cw[$c]
            if ($l > $wmax) {
                if ($sep == -1) {
                    if ($i == $j)
                        $i++;
                }
                else
                    $i=$sep + 1;
                $sep = -1;
                $j = $i;
                $l = 0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }

    private function renderDetail() {
        $field_pos_y = $this->RParser->detail[0]["y_axis"];
        $biggestY = 0;
        $checkpoint = $this->RParser->detail[0]["y_axis"];
        $tempY = $this->RParser->detail[0]["y_axis"];
        $this->renderGroupHeader($this->RParser->pageSetting["topMargin"] + $this->RParser->pageHeader[0]["height"]);

        foreach ($this->data as $row) {
            if (isset($this->group) && ($this->global_pointer > 0) &&
                    ($this->data[$this->global_pointer][$this->group_pointer] != $this->data[$this->global_pointer - 1][$this->group_pointer])) { //check the group's groupExpression existed and same or not
                $headerY = $biggestY; //+40;
                $checkpoint = $headerY; //+40;
                $biggestY = $headerY; //+40;
                $tempY = $this->RParser->detail[0]["y_axis"];
                //  $this->pdf->Cell(10,10,"??".$this->RParser->pageSetting["pageHeight"].",". $this->pdf->getY() .",". $this->groupfootheight.",".$this->RParser->pageSetting["bottomMargin"].",".$this->pageFooter[0]["height"]."??");
                if ($this->RParser->pageSetting["pageHeight"] < $this->pdf->getY() + $this->RParser->groupfootheight + $this->RParser->pageSetting["bottomMargin"] + $this->RParser->pageFooter[0]["height"]) {
                    $this->renderPageFooter();
                    $this->renderPageHeader($headerY);
                    $checkpoint = $headerY;
                    $biggestY = $this->RParser->pageSetting["topMargin"] + $this->RParser->pageHeader[0]["height"];
                    $tempY = $headerY;
                }

                if ($this->RParser->pageSetting["pageHeight"] < $this->pdf->getY() + $this->RParser->groupheaderheight + $this->RParser->pageSetting["bottomMargin"] + $compare["height"]) {

                    $this->renderPageHeader();
                    $checkpoint = $headerY;
                    $biggestY = $this->RParser->pageSetting["topMargin"] + $this->RParser->pageHeader[0]["height"];
                    $tempY = $headerY;
                }
                $this->renderGroupHeader($this->pdf->getY());

                $checkpoint = $this->pdf->getY();
            }

            foreach ($this->RParser->detail as $compare) { //this loop is to count possible biggest Y of the coming row
                switch (isset($compare["hidden_type"])?$compare["hidden_type"]:null) {
                    case "field":
                        $txt = $this->analyse_expression($compare["txt"]);// TODO: was $this->analyse_expression($row[${$compare["txt"]}]);
                        if (($this->group_name!==null) && isset($this->group[${$this->group_name}]["groupFooter"]) && (($checkpoint + ($compare["height"] * $txt)) > ($this->RParser->pageSetting["pageHeight"] - $this->RParser->groupfootheight - $this->RParser->pageSetting["bottomMargin"]))) {//check group footer existed or not
                            $this->renderPageFooter();
                            $checkpoint = $this->RParser->detail[0]["y_axis"];
                            $biggestY = 0;
                            $tempY = $this->RParser->detail[0]["y_axis"];
                        } elseif (isset($this->RParser->pageFooter) && (($checkpoint + ($compare["height"] * ($this->NbLines($compare["width"], $txt)))) > ($this->RParser->pageSetting["pageHeight"] - $this->RParser->pageFooter[0]["height"] - $this->RParser->pageSetting["bottomMargin"]))) {//check pagefooter existed or not //  $this->showGroupFooter($compare["height"]+$biggestY);
                            $this->renderPageFooter();
                            $headerY = $this->RParser->pageSetting["topMargin"] + $this->RParser->pageHeader[0]["height"];
                            $this->renderPageHeader($headerY);
                            $checkpoint = $this->RParser->detail[0]["y_axis"];
                            $biggestY = 0;
                            $tempY = $this->RParser->detail[0]["y_axis"];
                        } elseif (isset($this->RParser->lastPageFooter) && (($checkpoint + ($compare["height"] * ($this->NbLines($compare["width"], $txt)))) > ($this->RParser->pageSetting["pageHeight"] - $this->RParser->lastPageFooter[0]["height"] - $this->RParser->pageSetting["bottomMargin"]))) {//check lastpagefooter existed or not   //$this->showGroupFooter($compare["height"]+$biggestY);
                            $this->renderLastPageFooter();

                            $checkpoint = $this->RParser->detail[0]["y_axis"];
                            $biggestY = 0;
                            $tempY = $this->RParser->detail[0]["y_axis"];
                        }

                        if (($checkpoint + ($compare["height"] * ($this->NbLines($compare["width"], $txt)))) > $tempY) {
                            $tempY = $checkpoint + ($compare["height"] * ($this->NbLines($compare["width"], $txt)));
                        }
                        break;
                    case "relativebottomline":
                        break;
                    case "report_count":
                        $this->report_count++;
                        break;
                    case "group_count":
                        $this->group_count++;
                        break;
                    default:
                        $this->display($compare, $checkpoint);
                        break;
                }
            }

            if ($checkpoint + $this->RParser->detail[0]["height"] > ($this->RParser->pageSetting["pageHeight"]
                     - (isset($this->pageFooter[0]["height"])?$this->pageFooter[0]["height"]:0)
                     - $this->RParser->pageSetting["bottomMargin"])) { //check the upcoming band is greater than footer position or not
                $this->background();
                $headerY = $this->RParser->pageSetting["topMargin"] + $this->RParser->pageHeader[0]["height"];
                $this->renderPageHeader($headerY);
                $checkpoint = $this->RParser->detail[0]["y_axis"];
                $biggestY = 0;
                $tempY = $this->RParser->detail[0]["y_axis"];
            }

            foreach ($this->RParser->detail as $out) {
                switch (isset($out["hidden_type"])?$out['hidden_type']:null) {
                    case "field":
                        $this->prepare_print_array = array(
                            "type" => "MultiCell",
                            "width" => $out["width"],
                            "height" => $out["height"],
                            "txt" => $out["txt"],
                            "border" => $out["border"],
                            "align" => $out["align"],
                            "fill" => $out["fill"],
                            "hidden_type" => $out["hidden_type"],
                            "printWhenExpression" => $out["printWhenExpression"],
                            "soverflow" => $out["soverflow"],
                            "poverflow" => $out["poverflow"],
                            "link" => $out["link"],
                            "pattern" => $out["pattern"],
                            "writeHTML" => $out["writeHTML"],
                            "isPrintRepeatedValues" => $out["isPrintRepeatedValues"]);
                        $this->display($this->prepare_print_array, 0, true);
                        if ($this->pdf->GetY() > $biggestY) {
                            $biggestY = $this->pdf->GetY();
                        }
                        break;
                    case "relativebottomline":
                        $this->relativebottomline($out, $biggestY);
                        break;
                    default:
                        $this->display($out, $checkpoint);
                        //$checkpoint=$this->pdf->GetY();
                        break;
                }
            }
            $this->pdf->SetY($biggestY);
            if ($biggestY > $checkpoint + $this->RParser->detail[0]["height"]) {
                $checkpoint = $biggestY;
            } elseif ($biggestY < $checkpoint + $this->RParser->detail[0]["height"]) {
                $checkpoint = $checkpoint + $this->RParser->detail[0]["height"];
            } else {
                $checkpoint = $biggestY;
            }
            if (($this->group_pointer!==null) &&isset($this->RParser->group) && ($this->global_pointer > 0) &&
                    ($this->data[$this->global_pointer][$this->group_pointer] != $this->data[$this->global_pointer + 1][$this->group_pointer])) {
                $this->renderGroupFooter($compare["height"] + $biggestY);
                $checkpoint = $this->pdf->getY();
                $biggestY = 0;
            }
            $this->global_pointer++;
        }

        $this->global_pointer--;
        if (isset($this->RParser->lastPageFooter)) {
            $this->renderLastPageFooter();
        } else {
            $this->renderPageFooter();
        }
    }

    private function renderDetailNewPage() {
        $field_pos_y = $this->RParser->detail[0]["y_axis"];
        $biggestY = 0;
        $checkpoint = $this->RParser->detail[0]["y_axis"];
        $tempY = $this->RParser->detail[0]["y_axis"];
        $i = 0;
        if ($this->data) {
            $oo = 0;
            foreach ($this->data as $row) {
                $oo++;
                if (isset($this->RParser->group) && ($this->global_pointer > 0) && ($this->data[$this->global_pointer][$this->group_pointer] != $this->data[$this->global_pointer - 1][$this->group_pointer])) {
                    $this->renderPageFooter();
                    $this->renderPageHeaderNewPage();
                    $checkpoint = $this->RParser->detail[0]["y_axis"];
                    $biggestY = 0;
                    $tempY = $this->RParser->detail[0]["y_axis"];
                    $this->group_count = 0;
                }
                foreach ($this->RParser->detail as $compare) { //this loop is to count possible biggest Y of the coming row
                    switch (isset($compare["hidden_type"])?$compare['hidden_type']:null) {
                        case "field":
                            $txt = $this->analyse_expression("$compare[txt]");
                            if (isset($this->group)&&isset($this->group[${$this->group_name}]["groupFooter"]) && (($checkpoint + ($compare["height"] * $txt)) > ($this->RParser->pageSetting[pageHeight] - $this->group["$this->group_name"][groupFooter][0]["height"] - $this->RParser->pageSetting["bottomMargin"]))) {
                                $this->renderGroupFooter();
                                $this->renderPageFooter();
                                $this->renderPageHeaderNewPage();
                                $checkpoint = $this->RParser->detail[0]["y_axis"];
                                $biggestY = 0;
                                $tempY = $this->RParser->detail[0]["y_axis"];
                            } elseif (isset($this->RParser->pageFooter) && (($checkpoint + ($compare["height"] * ($this->NbLines($compare["width"], $txt)))) > ($this->RParser->pageSetting["pageHeight"] - $this->RParser->pageFooter[0]["height"] - $this->RParser->pageSetting["bottomMargin"]))) {
                                $this->renderGroupFooter(30);
                                $this->renderPageFooter();
                                $this->renderPageHeaderNewPage();
                                $headerY = $this->RParser->pageSetting["topMargin"] + $this->RParser->pageHeader[0]["height"];
                                $checkpoint = $this->RParser->detail[0]["y_axis"];
                                $biggestY = 0;
                                $tempY = $this->RParser->detail[0]["y_axis"];
                            } elseif (isset($this->RParser->lastPageFooter) && (($checkpoint + ($compare["height"] * ($this->NbLines($compare["width"], $txt)))) > ($this->RParser->pageSetting["pageHeight"] - $this->RParser->lastPageFooter[0]["height"] - $this->RParser->pageSetting["bottomMargin"]))) {
                                $this->renderGroupFooter();
                                $this->renderLastPageFooter();
                                $this->renderPageHeaderNewPage();
                                $checkpoint = $this->RParser->detail[0]["y_axis"];
                                $biggestY = 0;
                                $tempY = $this->RParser->detail[0]["y_axis"];
                            }

                            if (($checkpoint + ($compare["height"] * ($this->NbLines($compare["width"], $txt)))) > $tempY) {
                                $tempY = $checkpoint + ($compare["height"] * ($this->NbLines($compare["width"], $txt)));
                            }
                            break;
                        case "relativebottomline":
                            break;
                        case "report_count":
                            $this->report_count++;
                            break;
                        case "group_count":
                            $this->group_count++;
                            break;
                        default:
                            $this->display($compare, $checkpoint);
                            break;
                    }
                }

                if ($checkpoint + $this->RParser->detail[0]["height"] > ($this->RParser->pageSetting["pageHeight"] - (isset($this->pageFooter[0]["height"])?$this->pageFooter[0]["height"]:0) - $this->RParser->pageSetting["bottomMargin"])) { //check the upcoming band is greater than footer position or not
                    $this->renderPageFooter();
                    $headerY = $this->RParser->pageSetting["topMargin"] + $this->RParser->pageHeader[0]["height"];
                    $this->renderPageHeaderNewPage();
                    $checkpoint = $this->RParser->detail[0]["y_axis"];
                    $biggestY = 0;
                    $tempY = $this->RParser->detail[0]["y_axis"];
                }

                foreach ($this->RParser->detail as $out) {
                    switch (isset($out["hidden_type"])?$out['hidden_type']:null) {
                        case "field":
                            $this->prepare_print_array = array(
                                "type" => "MultiCell",
                                "width" => $out["width"],
                                "height" => $out["height"],
                                "txt" => $out["txt"],
                                "border" => $out["border"],
                                "align" => $out["align"],
                                "fill" => $out["fill"],
                                "hidden_type" => $out["hidden_type"],
                                "printWhenExpression" => $out["printWhenExpression"],
                                "soverflow" => $out["soverflow"],
                                "poverflow" => $out["poverflow"],
                                "link" => $out["link"],
                                "pattern" => $out["pattern"]);
                            $this->display($this->prepare_print_array, 0, true);

                            if ($this->pdf->GetY() > $biggestY) {
                                $biggestY = $this->pdf->GetY();
                            }
                            break;
                        case "relativebottomline":
                            $this->relativebottomline($out, $biggestY);
                            break;
                        default:

                            $this->display($out, $checkpoint);
                            break;
                    }
                }
                $this->pdf->SetY($biggestY);
                if ($biggestY > $checkpoint + $this->RParser->detail[0]["height"]) {
                    $checkpoint = $biggestY;
                } elseif ($biggestY < $checkpoint + $this->RParser->detail[0]["height"]) {
                    $checkpoint = $checkpoint + $this->RParser->detail[0]["height"];
                } else {
                    $checkpoint = $biggestY;
                }
                if (isset($this->RParser->group) && ($this->global_pointer > 0) && isset($this->data[$this->global_pointer+1]) && ($this->data[$this->global_pointer][$this->group_pointer] != $this->data[$this->global_pointer + 1][$this->group_pointer]))
                    $this->renderGroupFooter($tempY);
                $this->global_pointer++;
            }
        }else {
            echo utf8_decode("Sorry cause there is not result from this query.");
            exit(0);
        }
        $this->global_pointer--;
        if (isset($this->RParser->lastPageFooter)) {
            $this->renderLastPageFooter();
        } else {
            $this->renderPageFooter();
        }
    }

    private function renderGroupHeader($y) {
        $bandheight = isset($this->RParser->grouphead[0]['height'])?$this->RParser->grouphead[0]['height']:0;
        foreach ($this->RParser->grouphead as $out) {
            $this->display($out, $y, true);
        }
        return $bandheight;
    }

    private function renderGroupFooter($y) {
        $bandheight = $this->RParser->groupfoot[0]['height'];
        foreach ($this->RParser->groupfoot as $out) {
            $this->display($out, $y, true);
        }
        $this->RParser->footershowed = true;
        return $bandheight;
    }

    private function display($arraydata, $y_axis=0, $fielddata=false) {
        if(isset($arraydata['rotation'])) {
            $this->Rotate($arraydata["rotation"]);
            if ($arraydata["rotation"] != "") {
                if ($arraydata["rotation"] == "Left") {
                    $w = $arraydata["width"];
                    $arraydata["width"] = $arraydata["height"];
                    $arraydata["height"] = $w;
                    $this->pdf->SetXY($this->pdf->GetX() - $arraydata["width"], $this->pdf->GetY());
                } elseif ($arraydata["rotation"] == "Right") {
                    $w = $arraydata["width"];
                    $arraydata["width"] = $arraydata["height"];
                    $arraydata["height"] = $w;
                    $this->pdf->SetXY($this->pdf->GetX(), $this->pdf->GetY() - $arraydata["height"]);
                } elseif ($arraydata["rotation"] == "UpsideDown") {
                    //soverflow"=>$stretchoverflow,"poverflow"
                    $arraydata["soverflow"] = true;
                    $arraydata["poverflow"] = true;
                    $this->pdf->SetXY($this->pdf->GetX() - $arraydata["width"], $this->pdf->GetY() - $arraydata["height"]);
                }
            }
        }

        if(isset($arraydata['type'])) {
            switch($arraydata['type']) {
                case 'band':
                    break;
                default:
                    //CVarDumper::dump($arraydata);exit;
            }

            if ($arraydata["type"] == "SetFont") {
                if ($arraydata["font"] == 'uGB')
                    $this->pdf->isUnicode = true;
                else
                    $this->pdf->isUnicode = false;

                $this->pdf->SetFont($arraydata["font"], $arraydata["fontstyle"], $arraydata["fontsize"]);
            }
            elseif ($arraydata["type"] == "subreport") {

                $this->runSubReport($arraydata);
            } elseif ($arraydata["type"] == "MultiCell") {
                if ($fielddata == false) {
                    $this->checkoverflow($arraydata, $this->updatePageNo($arraydata["txt"]));
                } elseif ($fielddata == true) {
                    $this->checkoverflow(
                            $arraydata, $this->updatePageNo($this->analyse_expression($arraydata["txt"], isset($arraydata["isPrintRepeatedValues"])?$arraydata["isPrintRepeatedValues"]:null)));
                }
            } elseif ($arraydata["type"] == "SetXY") {


                $this->pdf->SetXY($arraydata["x"] + $this->RParser->pageSetting["leftMargin"], $arraydata["y"] + $y_axis);
            } elseif ($arraydata["type"] == "Cell") {


                $this->pdf->Cell(
                        $arraydata["width"], $arraydata["height"], $this->updatePageNo($arraydata["txt"]), $arraydata["border"], $arraydata["ln"], $arraydata["align"], $arraydata["fill"], $arraydata["link"]);
                if ($this->debuggroup == true)
                    $this->pdf->MultiCell(100, 10, "SampleText");
            }
            elseif ($arraydata["type"] == "Rect") {
                $this->pdf->Rect(
                        $arraydata["x"] + $this->RParser->pageSetting["leftMargin"], $arraydata["y"] + $y_axis, $arraydata["width"], $arraydata["height"]);
            } elseif ($arraydata["type"] == "Image") {
                $path = $this->analyse_expression($arraydata["path"]);
                $imgtype = substr($path, -3);

                $this->pdf->Image($path, $arraydata["x"] + $this->RParser->pageSetting["leftMargin"], $arraydata["y"] + $y_axis, $arraydata["width"], $arraydata["height"], $imgtype, $arraydata["link"]);
            } elseif ($arraydata["type"] == "SetTextColor") {
                $this->pdf->SetTextColor($arraydata["r"], $arraydata["g"], $arraydata["b"]);
            } elseif ($arraydata["type"] == "SetDrawColor") {
                $this->pdf->SetDrawColor($arraydata["r"], $arraydata["g"], $arraydata["b"]);
            } elseif ($arraydata["type"] == "SetLineWidth") {
                $this->pdf->SetLineWidth($arraydata["width"]);
            } elseif ($arraydata["type"] == "Line") {
                $this->pdf->Line(
                        $arraydata["x1"] + $this->RParser->pageSetting["leftMargin"], $arraydata["y1"] + $y_axis, $arraydata["x2"] +
                        $this->RParser->pageSetting["leftMargin"], $arraydata["y2"] +
                        $y_axis);
            } elseif ($arraydata["type"] == "SetFillColor") {
                $this->pdf->SetFillColor($arraydata["r"], $arraydata["g"], $arraydata["b"]);
            }
        }
    }

    private function relativebottomline($path, $y) {
        $extra = $y - $path["y1"];
        $this->display($path, $extra);
    }

    private function updatePageNo($s) {
        return str_replace('$this->PageNo()', $this->pdf->PageNo(), $s);
    }

    private function checkoverflow($arraydata, $txt="") {
        $this->print_expression($arraydata);
        if ($this->print_expression_result == true) {
            if (isset($arraydata["link"])) {
                $arraydata["link"] = $this->analyse_expression($arraydata["link"], "");
            }
            if (isset($arraydata['writeHTML']) &&$arraydata["writeHTML"] == 1 && ($this->pdf instanceof TCPDF)) {
                $this->pdf->writeHTML($txt);
            } elseif ($arraydata["poverflow"] == "true" && $arraydata["soverflow"] == "false") {
                $this->pdf->Cell(
                        $arraydata["width"], $arraydata["height"], IReportHelper::formatText($txt, isset($arraydata["pattern"])?$arraydata['pattern']:null), $arraydata["border"], "", $arraydata["align"], $arraydata["fill"], $arraydata["link"]);
                if ($this->debuggroup == true)
                    $this->pdf->MultiCell(100, 10, "SampleText");
            }
            elseif ($arraydata["poverflow"] == "false" && $arraydata["soverflow"] == "false") {
                while ($this->pdf->GetStringWidth($txt) > $arraydata["width"]) {
                    $txt = substr_replace($txt, "", -1);
                }
                $this->pdf->Cell(
                        $arraydata["width"], $arraydata["height"], IReportHelper::formatText($txt, isset($arraydata["pattern"])?$arraydata['pattern']:null), $arraydata["border"], "", $arraydata["align"], $arraydata["fill"], $arraydata["link"]);
                if ($this->debuggroup == true)
                    $this->pdf->MultiCell(100, 10, "SampleText");
            }
            elseif ($arraydata["poverflow"] == "false" && $arraydata["soverflow"] == "true") {
                $this->pdf->MultiCell(
                        $arraydata["width"], $arraydata["height"], IReportHelper::formatText($txt, isset($arraydata["pattern"])?$arraydata['pattern']:null), $arraydata["border"], $arraydata["align"], $arraydata["fill"]);
                if ($this->debuggroup == true)
                    $this->pdf->MultiCell(100, 10, "SampleText");
            }
            else {
                $this->pdf->MultiCell(
                        $arraydata["width"], $arraydata["height"], IReportHelper::formatText($txt, isset($arraydata["pattern"])?$arraydata['pattern']:null), $arraydata["border"], $arraydata["align"], $arraydata["fill"]);
                if ($this->debuggroup == true)
                    $this->pdf->MultiCell(100, 10, "SampleText");
            }
        }
        $this->print_expression_result = false;
        if ($this->debuggroup == true)
            $this->pdf->MultiCell(100, 10, "SampleText");
    }

    /**
     * Analyze expression.
     *
     * Expands $F{} , $V{} and $P{} terms.
     *
     * @param string $data  Expression.  Can contain '+'.
     * @param string $isPrintRepeatedValue
     * @return string|int Concatenation of expression or sum of terms
     */
    private function analyse_expression($data, $isPrintRepeatedValue="true") {
        $arrdata = explode("+", $data);
        $i = 0;
        foreach ($arrdata as $num => $out) {
            $i++;
            $arrdata[$num] = str_replace('"', "", $out);
            if (substr($out, 0, 3) == '$F{') {
                if ($isPrintRepeatedValue == "true" || $isPrintRepeatedValue == "") {
                    $arrdata[$num] = $this->data[$this->global_pointer][substr($out, 3, -1)];
                } else {
                    if ($this->previousarraydata[$arrdata[$num]] == $this->data[$this->global_pointer][substr($out, 3, -1)]) {
                        $arrdata[$num] = "";
                    } else {
                        $arrdata[$num] = $this->data[$this->global_pointer][substr($out, 3, -1)];
                        $this->previousarraydata[$out] = $this->data[$this->global_pointer][substr($out, 3, -1)];
                    }
                }
            } elseif (substr($out, 0, 3) == '$V{') {
                $arrdata[$num] = &$this->RParser->variables[substr($out, 3, -1)]["ans"];
            } elseif (substr($out, 0, 3) == '$P{') {
                $arrdata[$num] = $this->RParser->parameters[substr($out, 3, -1)];
            }
        }

        if (IReportHelper::left($data, 3) == '"("' && IReportHelper::right($data, 3) == '")"') {
            $total = 0;

            foreach ($arrdata as $num => $out) {
                if ($num > 0 && $num < $i)
                    $total+=$out;
            }
            return $total;
        }
        else {

            return implode($arrdata);
        }
    }

    private function print_expression($data) {
        if(isset($data['printWhenExpression'])) {
            $expression = $data["printWhenExpression"];
            $expression = str_replace('$F{', '$this->data[$this->global_pointer][', $expression);
            $expression = str_replace('$P{', '$this->data[$this->global_pointer][', $expression);
            $expression = str_replace('$V{', '$this->data[$this->global_pointer][', $expression);
            $expression = str_replace('}', ']', $expression);
            $this->print_expression_result = false;
        } else {
            $expression ="";
        }
        if ($expression != "") {
            eval('if(' . $expression . '){$this->print_expression_result=true;}');
        } elseif ($expression == "") {
            $this->print_expression_result = true;
        }
    }

    private function runSubReport($d) {
        foreach ($d["subreportparameterarray"] as $b) {
            $t = $b->subreportParameterExpression;
            $arrdata = explode("+", $t);
            $i = 0;
            foreach ($arrdata as $num => $out) {
                $i++;
                $arrdata[$num] = str_replace('"', "", $out);
                if (substr($out, 0, 3) == '$F{') {
                    $arrdata[$num] = $this->data[$this->global_pointer][substr($out, 3, -1)];
                } elseif (substr($out, 0, 3) == '$V{') {
                    $arrdata[$num] = &$this->RParser->variable[substr($out, 3, -1)]["ans"];
                } elseif (substr($out, 0, 3) == '$P{') {
                    $arrdata[$num] = $this->RParser->parameters[substr($out, 3, -1)];
                }
            }
            $t = implode($arrdata);
        }
    }

    private function Rotate($type, $x=-1, $y=-1) {
        if ($type == "")
            $angle = 0;
        elseif ($type == "Left")
            $angle = 90;
        elseif ($type == "Right")
            $angle = 270;
        elseif ($type == "UpsideDown")
            $angle = 180;

        if ($x == -1)
            $x = $this->pdf->getX();
        if ($y == -1)
            $y = $this->pdf->getY();
        if (isset($this->angle) && $this->angle != 0)
            $this->pdf->out('Q');
        $this->angle = $angle;
        if ($angle != 0) {
            $angle*=M_PI / 180;
            $c = cos($angle);
            $s = sin($angle);
            $cx = $x * $this->pdf->k();
            $cy = ($this->pdf->h() - $y) * $this->pdf->k();
            $this->pdf->out(sprintf('q %.5f %.5f %.5f %.5f %.2f %.2f cm 1 0 0 1 %.2f %.2f cm', $c, $s, -$s, $c, $cx, $cy, -$cx, -$cy));
        }
    }

}

?>
