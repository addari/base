<?php

class IReportParser {

    public $report_count;
    public $group_count;
    public $newPageGroup;
    public $footershowed = true;
    public $titleheight = 0;

    public $parameters = array();
    public $pageSetting = array();
    public $y_axis;
    public $sql;
    public $fields = array();
    public $variables = array();
    public $group = array();
    public $pointer= array();
    public $grouphead = array();
    public $groupheadheight;
    public $band = array();
    public $groupfoot= array();
    public $groupfootheight;
    public $background = array();
    public $title= array();
    public $pageHeader = array();
    public $columnHeader = array();
    public $detail = array();
    public $columnFooter = array();
    public $pageFooter = array();
    public $summary = array();

    const adjust = 1.2;

    public function parser($xml) {
        $this->ParserPageSetting($xml);
        foreach ($xml as $node => $attribute) {
            switch ($node) {
                case "parameter":
                    $this->ParserParameter($attribute);
                    break;
                case "queryString":
                    $this->ParserQueryString($attribute);
                    break;
                case "field":
                    $this->ParserField($attribute);
                    break;
                case "variable":
                    $this->ParserVariable($attribute);
                    break;
                case "group":
                    $this->ParserGroup($attribute);
                    break;
                case "subDataset":
                    $this->ParsersubDataset($attribute);
                    break;
                case "background":
                    $this->pointer = &$this->background;
                    $this->pointer[] = array(
                        "height" => $attribute->band["height"],
                        "splitType" => $attribute->band["splitType"]);
                    foreach ($attribute as $bg) {
                        $this->ParserDefault($bg);
                    }
                    break;
                default:
                    foreach ($attribute as $object) {
                        eval("\$this->pointer=&" . "\$this->$node" . ";");
                        $this->band[] = array("name" => $node);
                        $this->pointer[] = array(
                            "type" => "band",
                            "height" => $object["height"],
                            "splitType" => $object["splitType"],
                            "y_axis" => $this->y_axis);
                        $this->ParserDefault($object);
                    }
                    $this->y_axis = $this->y_axis + $attribute->band["height"]; //after handle , then adjust y axis
                    break;
            }
        }
    }

    private function ParserPageSetting($xml_path) {
        $this->pageSetting["orientation"] = "P";
        $this->pageSetting["name"] = $xml_path["name"];
        $this->pageSetting["language"] = $xml_path["language"];
        $this->pageSetting["pageWidth"] = $xml_path["pageWidth"];
        $this->pageSetting["pageHeight"] = $xml_path["pageHeight"];
        if (isset($xml_path["orientation"])) {
            $this->pageSetting["orientation"] = substr($xml_path["orientation"], 0, 1);
        }
        $this->pageSetting["columnWidth"] = $xml_path["columnWidth"];
        $this->pageSetting["leftMargin"] = $xml_path["leftMargin"];
        $this->pageSetting["rightMargin"] = $xml_path["rightMargin"];
        $this->pageSetting["topMargin"] = $xml_path["topMargin"];
        $this->y_axis = $xml_path["topMargin"];
        $this->pageSetting["bottomMargin"] = $xml_path["bottomMargin"];
    }

    private function ParsersubDataset($data){
    $this->subdataset[$data['name'].'']= $data->queryString;

    }
    private function ParserParameter($xml_path) {
        $this->parameters[] = $xml_path['name'];
    }

    private function ParserQueryString($xml_path) {
        $this->sql = $xml_path;
    }

    private function ParserField($xml_path) {
        $this->fields[] = $xml_path["name"];
    }

    private function ParserVariable($xml_path) {

        $this->variables["$xml_path[name]"] = array(
            "calculation" => $xml_path["calculation"],
            "target" => substr($xml_path->variableExpression, 3, -1),
            "class" => $xml_path["class"],
            "resetType"=>$xml_path["resetType"]);
    }

    private function ParserGroup($xml_path) {
       $this->newPageGroup = ($xml_path["isStartNewPage"] == "true");
        foreach ($xml_path as $tag => $out) {
            switch ($tag) {
                case "groupHeader":
                    // TODO: check Next line commented, was called with empty $this->group, and next line is suppressing the effect.
                    //$this->pointer = &$this->group[${$xml_path["name"]}]["groupHeader"];
                    $this->pointer = &$this->grouphead;
                    $this->groupheadheight = $out->band["height"];
                    $this->band[] = array(
                        "name" => "group",
                        "gname" => $xml_path["name"],
                        "isStartNewPage" => $xml_path["isStartNewPage"],
                        "groupExpression" => substr($xml_path->groupExpression, 3, -1));
                    $this->pointer[] = array(
                        "type" => "band",
                        "height" => $out->band["height"] + 0,
                        "y_axis" => "",
                        "groupExpression" => substr($xml_path->groupExpression, 3, -1));
                    foreach ($out as $band) {
                        $this->ParserDefault($band);
                    }
                    $this->y_axis = $this->y_axis + $out->band["height"];  //after handle , then adjust y axis
                    break;
                case "groupFooter":
                    // TODO: check Next line commented, was called with empty $this->group, and next line is suppressing the effect.
                    //$this->pointer = &$this->group[${$xml_path["name"]}]["groupFooter"];
                    $this->pointer = &$this->groupfoot;
                    $this->groupfootheight = $out->band["height"];
                    $this->pointer[] = array(
                        "type" => "band",
                        "height" => $out->band["height"] + 0,
                        "y_axis" => "",
                        "groupExpression" => substr($xml_path->groupExpression, 3, -1));
                    foreach ($out as $b => $band) {
                        $this->ParserDefault($band);
                    }
                    break;
                default:

                    break;
            }
        }
    }

    private function ParserDefault($xml_path) {
        foreach ($xml_path as $k => $out) {
            switch ($k) {
                case "staticText":
                    $this->ParserElementStaticText($out);
                    break;
                case "image":
                    $this->ParserElementImage($out);
                    break;
                case "line":
                    $this->ParserElementLine($out);
                    break;
                case "rectangle":
                    $this->ParserElementRectangle($out);
                    break;
                case "textField":
                    $this->ParserElementTextField($out);
                    break;
                case "stackedBarChart":
                    $this->ParserChart($out,'stackedBarChart');
                    break;
                case "barChart":
                    $this->ParserChart($out,'barChart');
                    break;
                case "pieChart":
                    $this->ParserChart($out,'pieChart');
                    break;
                case "lineChart":
                    $this->ParserChart($out,'lineChart');
                    break;
                case "stackedAreaChart":
                    $this->ParserChart($out,'stackedAreaChart');
                    break;
                case "pie3DChart":
                    $this->element_pie3DChart($out,'pie3DChart');
                    break;
                case "subreport":
                    $this->ParserElementSubReport($out);
                    break;
                default:
                    break;
            }
        };
    }

    private function ParserElementStaticText($data) {
        $align = "L";
        $fill = 0;
        $border = 0;
        $fontsize = 10;
        $font = "helvetica";
        $fontstyle = "";
        $textcolor = array("r" => 0, "g" => 0, "b" => 0);
        $fillcolor = array("r" => 255, "g" => 255, "b" => 255);
        $txt = "";
        $rotation = "";
        $drawcolor = array("r" => 0, "g" => 0, "b" => 0);
        $height = $data->reportElement["height"];
        $stretchoverflow = "true";
        $printoverflow = "false";
        if (isset($data->reportElement["forecolor"])) {
            $textcolor = array(
                "r" => hexdec(substr($data->reportElement["forecolor"], 1, 2)),
                "g" => hexdec(substr($data->reportElement["forecolor"], 3, 2)),
                "b" => hexdec(substr($data->reportElement["forecolor"], 5, 2)));
        }
        if (isset($data->reportElement["backcolor"])) {
            $fillcolor = array(
                "r" => hexdec(substr($data->reportElement["backcolor"], 1, 2)),
                "g" => hexdec(substr($data->reportElement["backcolor"], 3, 2)),
                "b" => hexdec(substr($data->reportElement["backcolor"], 5, 2)));
        }
        if ($data->reportElement["mode"] == "Opaque") {
            $fill = 1;
        }
        if (isset($data["isStretchWithOverflow"]) && $data["isStretchWithOverflow"] == "true") {
            $stretchoverflow = "true";
        }
        if (isset($data->reportElement["isPrintWhenDetailOverflows"]) && $data->reportElement["isPrintWhenDetailOverflows"] == "true") {
            $printoverflow = "true";
            $stretchoverflow = "false";
        }
        if ((isset($data->box)) && ($data->box->pen["lineWidth"] > 0)) {
            $border = 1;
            if (isset($data->box->pen["lineColor"])) {
                $drawcolor = array(
                    "r" => hexdec(substr($data->box->pen["lineColor"], 1, 2)),
                    "g" => hexdec(substr($data->box->pen["lineColor"], 3, 2)),
                    "b" => hexdec(substr($data->box->pen["lineColor"], 5, 2)));
            }
        }
        if (isset($data->textElement["textAlignment"])) {
            $align = IReportHelper::get_first_value($data->textElement["textAlignment"]);
        }
        if (isset($data->textElement["rotation"])) {
            $rotation = $data->textElement["rotation"];
        }
        if (isset($data->textElement->font["pdfFontName"])) {
            $font = $data->textElement->font["pdfFontName"];
        }
        if (isset($data->textElement->font["size"])) {
            $fontsize = $data->textElement->font["size"];
        }
        if (isset($data->textElement->font["isBold"]) && $data->textElement->font["isBold"] == "true") {
            $fontstyle = $fontstyle . "B";
        }
        if (isset($data->textElement->font["isItalic"]) && $data->textElement->font["isItalic"] == "true") {
            $fontstyle = $fontstyle . "I";
        }
        if (isset($data->textElement->font["isUnderline"]) && $data->textElement->font["isUnderline"] == "true") {
            $fontstyle = $fontstyle . "U";
        }
        if (isset($data->reportElement["key"])) {
            $height = $fontsize * IReportParser::adjust;
        }
        $this->pointer[] = array(
            "type" => "SetXY",
            "x" => $data->reportElement["x"],
            "y" => $data->reportElement["y"],
            "hidden_type" => "SetXY");
        $this->pointer[] = array(
            "type" => "SetTextColor",
            "r" => $textcolor["r"],
            "g" => $textcolor["g"],
            "b" => $textcolor["b"],
            "hidden_type" => "textcolor");
        $this->pointer[] = array(
            "type" => "SetDrawColor",
            "r" => $drawcolor["r"],
            "g" => $drawcolor["g"],
            "b" => $drawcolor["b"],
            "hidden_type" => "drawcolor");
        $this->pointer[] = array(
            "type" => "SetFillColor",
            "r" => $fillcolor["r"],
            "g" => $fillcolor["g"],
            "b" => $fillcolor["b"],
            "hidden_type" => "fillcolor");
        $this->pointer[] = array(
            "type" => "SetFont",
            "font" => $font,
            "fontstyle" => $fontstyle,
            "fontsize" => $fontsize,
            "hidden_type" => "font");
        $this->pointer[] = array(
            "type" => "MultiCell",
            "width" => $data->reportElement["width"],
            "height" => $height,
            "txt" => $data->text,
            "border" => $border,
            "align" => $align,
            "fill" => $fill,
            "hidden_type" => "statictext",
            "soverflow" => $stretchoverflow,
            "poverflow" => $printoverflow,
            "rotation" => $rotation);
    }

    private function ParserElementImage($data) {
        $imagepath = $data->imageExpression;
        switch ($data["scaleImage"]) {
            case "FillFrame":
                $this->pointer[] = array(
                    "type" => "Image",
                    "path" => $imagepath,
                    "x" => $data->reportElement["x"] + 0,
                    "y" => $data->reportElement["y"] + 0,
                    "width" => $data->reportElement["width"] + 0,
                    "height" => $data->reportElement["height"] + 0,
                    "imgtype" => null,//TODO: was $imagetype
                    "link" => substr($data->hyperlinkReferenceExpression, 1, -1),
                    "hidden_type" => "image");
                break;
            default:
                $this->pointer[] = array(
                    "type" => "Image",
                    "path" => $imagepath,
                    "x" => $data->reportElement["x"] + 0,
                    "y" => $data->reportElement["y"] + 0,
                    "width" => $data->reportElement["width"] + 0,
                    "height" => $data->reportElement["height"] + 0,
                    "imgtype" => null, //TODO: was: $imagetype
                    "link" => substr($data->hyperlinkReferenceExpression, 1, -1),
                    "hidden_type" => "image");
                break;
        }
    }
 private function ParserChart($data, $type) {
        $seriesexp = array();
        $catexp = array();
        $valueexp = array();
        $labelexp = array();
        $height = $data->chart->reportElement["height"];
        $width = $data->chart->reportElement["width"];
        $x = $data->chart->reportElement["x"];
        $y = $data->chart->reportElement["y"];
        $charttitle['position'] = $data->chart->chartTitle['position'];
        $titlefontname = $data->chart->chartTitle->font['pdfFontName'];
        $titlefontsize = $data->chart->chartTitle->font['size'];
        $charttitle['text'] = $data->chart->chartTitle->titleExpression;
        $chartsubtitle['text'] = $data->chart->chartSubTitle->subtitleExpression;
        $chartLegendPos = $data->chart->chartLegend['position'];
        $dataset = $data->categoryDataset->dataset->datasetRun['subDataset'];
        $subcatdataset = $data->categoryDataset;
        $showshape = false;
        //echo $subcatdataset;
        $i = 0;
        foreach ($subcatdataset as $cat => $catseries) {
            foreach ($catseries as $a => $series) {
                if ("$series->categoryExpression" != '') {
                    array_push($seriesexp, "$series->seriesExpression");
                    array_push($catexp, "$series->categoryExpression");
                    array_push($valueexp, "$series->valueExpression");
                    array_push($labelexp, "$series->labelExpression");
                }
            }
        }
        $bb = $data->categoryDataset->dataset->datasetRun['subDataset'];
        if($bb!=null) {
            $sql = $this->arraysubdataset[$bb]['sql'];
        } else {
            $sql=null;
        }
        switch ($type) {
            case "barChart":
                $ylabel = $data->barPlot->valueAxisLabelExpression;
                $xlabel = $data->barPlot->categoryAxisLabelExpression;
                $maxy = $data->barPlot->rangeAxisMaxValueExpression;
                $miny = $data->barPlot->rangeAxisMinValueExpression;
                break;
            case "lineChart":
                $ylabel = $data->linePlot->valueAxisLabelExpression;
                $xlabel = $data->linePlot->categoryAxisLabelExpression;
                $maxy = $data->linePlot->rangeAxisMaxValueExpression;
                $miny = $data->linePlot->rangeAxisMinValueExpression;
                $showshape = $data->linePlot["isShowShapes"];
                break;
            case "stackedAreaChart":
                $ylabel = $data->areaPlot->valueAxisLabelExpression;
                $xlabel = $data->areaPlot->categoryAxisLabelExpression;
                $maxy = $data->areaPlot->rangeAxisMaxValueExpression;
                $miny = $data->areaPlot->rangeAxisMinValueExpression;
                break;
        }
        $param = array();
        if(isset($data->categoryDataset->dataset->datasetRun->datasetParameter)) {
            foreach ($data->categoryDataset->dataset->datasetRun->datasetParameter as $tag => $value) {
                $param[] = array("$value[name]" => $value->datasetParameterExpression);
            }
        }
        if ($maxy != '' && $miny != '') {
            $scalesetting = array(0 => array("Min" => $miny, "Max" => $maxy));
        }
        else
            $scalesetting="";

        $this->pointer[] = array(
            'type' => $type,
            'x' => $x,
            'y' => $y,
            'height' => $height,
            'width' => $width,
            'charttitle' => $charttitle,
            'chartsubtitle' => $chartsubtitle,
            'chartLegendPos' => $chartLegendPos,
            'dataset' => $dataset,
            'seriesexp' => $seriesexp,
            'catexp' => $catexp,
            'valueexp' => $valueexp,
            'labelexp' => $labelexp,
            'param' => $param,
            'sql' => $sql,
            'xlabel' => $xlabel,
            'showshape' => $showshape,
            'titlefontsize' => $titlefontname,
            'titlefontsize' => $titlefontsize,
            'scalesetting' => $scalesetting);
    }

    private function ParserElementLine($data) { //default line width=0.567(no detect line width)
        $drawcolor = array("r" => 0, "g" => 0, "b" => 0);
        $hidden_type = "line";
        if (isset($data->reportElement["forecolor"])) {
            $drawcolor = array(
                "r" => hexdec(substr($data->reportElement["forecolor"], 1, 2)),
                "g" => hexdec(substr($data->reportElement["forecolor"], 3, 2)),
                "b" => hexdec(substr($data->reportElement["forecolor"], 5, 2)));
        }
        $this->pointer[] = array(
            "type" => "SetDrawColor",
            "r" => $drawcolor["r"],
            "g" => $drawcolor["g"],
            "b" => $drawcolor["b"],
            "hidden_type" => "drawcolor");
        if (isset($data->reportElement["positionType"]) && $data->reportElement["positionType"] == "FixRelativeToBottom") {
            $hidden_type = "relativebottomline";
        }
        if ($data->reportElement["width"][0] + 0 > $data->reportElement["height"][0] + 0) { //width > height means horizontal line
            $this->pointer[] = array(
                "type" => "Line",
                "x1" => $data->reportElement["x"],
                "y1" => $data->reportElement["y"],
                "x2" => $data->reportElement["x"] + $data->reportElement["width"],
                "y2" => $data->reportElement["y"] + $data->reportElement["height"] - 1,
                "hidden_type" => $hidden_type);
        } elseif ($data->reportElement["height"][0] + 0 > $data->reportElement["width"][0] + 0) {  //vertical line
            $this->pointer[] = array(
                "type" => "Line",
                "x1" => $data->reportElement["x"],
                "y1" => $data->reportElement["y"],
                "x2" => $data->reportElement["x"] + $data->reportElement["width"] - 1,
                "y2" => $data->reportElement["y"] + $data->reportElement["height"],
                "hidden_type" => $hidden_type);
        }
        $this->pointer[] = array(
            "type" => "SetDrawColor",
            "r" => 0,
            "g" => 0,
            "b" => 0,
            "hidden_type" => "drawcolor");
    }

    private function ParserElementRectangle($data) {

        $drawcolor = array("r" => 0, "g" => 0, "b" => 0);
        if (isset($data->reportElement["forecolor"])) {
            $drawcolor = array(
                "r" => hexdec(substr($data->reportElement["forecolor"], 1, 2)),
                "g" => hexdec(substr($data->reportElement["forecolor"], 3, 2)),
                "b" => hexdec(substr($data->reportElement["forecolor"], 5, 2)));
        }
        $this->pointer[] = array(
            "type" =>"SetDrawColor",
            "r" => $drawcolor["r"],
            "g" => $drawcolor["g"],
            "b" => $drawcolor["b"],
            "hidden_type" => "drawcolor");
        $this->pointer[] = array(
            "type" => "Rect",
            "x" => $data->reportElement["x"],
            "y" => $data->reportElement["y"],
            "width" => $data->reportElement["width"],
            "height" => $data->reportElement["height"],
            "hidden_type" => "rect");
        $this->pointer[] = array(
            "type" => "SetDrawColor",
            "r" => 0,
            "g" => 0,
            "b" => 0,
            "hidden_type" => "drawcolor");
    }

    private function ParserElementTextField($data) {
        $align = "L";
        $fill = 0;
        $border = 0;
        $fontsize = 10;
        $font = "helvetica";
        $rotation = "";
        $fontstyle = "";
        $textcolor = array("r" => 0, "g" => 0, "b" => 0);
        $fillcolor = array("r" => 255, "g" => 255, "b" => 255);
        $stretchoverflow = "false";
        $printoverflow = "false";
        $height = $data->reportElement["height"];
        $drawcolor = array("r" => 0, "g" => 0, "b" => 0);
        if (isset($data->reportElement["forecolor"])) {
            $textcolor = array(
                "r" => hexdec(substr($data->reportElement["forecolor"], 1, 2)),
                "g" => hexdec(substr($data->reportElement["forecolor"], 3, 2)),
                "b" => hexdec(substr($data->reportElement["forecolor"], 5, 2)));
        }
        if (isset($data->reportElement["backcolor"])) {
            $fillcolor = array(
                "r" => hexdec(substr($data->reportElement["backcolor"], 1, 2)),
                "g" => hexdec(substr($data->reportElement["backcolor"], 3, 2)),
                "b" => hexdec(substr($data->reportElement["backcolor"], 5, 2)));
        }
        if ($data->reportElement["mode"] == "Opaque") {
            $fill = 1;
        }
        if (isset($data["isStretchWithOverflow"]) && $data["isStretchWithOverflow"] == "true") {
            $stretchoverflow = "true";
        }
        if (isset($data->reportElement["isPrintWhenDetailOverflows"]) && $data->reportElement["isPrintWhenDetailOverflows"] == "true") {
            $printoverflow = "true";
        }
        if (isset($data->box) && $data->box->pen["lineWidth"] > 0) {
            $border = 1;
            if (isset($data->box->pen["lineColor"])) {
                $drawcolor = array(
                    "r" => hexdec(substr($data->box->pen["lineColor"], 1, 2)),
                    "g" => hexdec(substr($data->box->pen["lineColor"], 3, 2)),
                    "b" => hexdec(substr($data->box->pen["lineColor"], 5, 2)));
            }
        }
        if (isset($data->reportElement["key"])) {
            $height = $fontsize * $this->adjust;
        }
        if (isset($data->textElement["textAlignment"])) {
            $align = IReportHelper::get_first_value($data->textElement["textAlignment"]);
        }
        if (isset($data->textElement["rotation"])) {
            $rotation = $data->textElement["rotation"];
        }
        if (isset($data->textElement->font["pdfFontName"])) {
            $font = $data->textElement->font["pdfFontName"];
        }
        if (isset($data->textElement->font["size"])) {
            $fontsize = $data->textElement->font["size"];
        }
        if (isset($data->textElement->font["isBold"]) && $data->textElement->font["isBold"] == "true") {
            $fontstyle = $fontstyle . "B";
        }
        if (isset($data->textElement->font["isItalic"]) && $data->textElement->font["isItalic"] == "true") {
            $fontstyle = $fontstyle . "I";
        }
        if (isset($data->textElement->font["isUnderline"]) && $data->textElement->font["isUnderline"] == "true") {
            $fontstyle = $fontstyle . "U";
        }
        $this->pointer[] = array(
            "type" => "SetXY",
            "x" => $data->reportElement["x"],
            "y" => $data->reportElement["y"],
            "hidden_type" => "SetXY");
        $this->pointer[] = array(
            "type" => "SetTextColor",
            "r" => $textcolor["r"],
            "g" => $textcolor["g"],
            "b" => $textcolor["b"],
            "hidden_type" => "textcolor");
        $this->pointer[] = array(
            "type" => "SetDrawColor",
            "r" => $drawcolor["r"],
            "g" => $drawcolor["g"],
            "b" => $drawcolor["b"],
            "hidden_type" => "drawcolor");
        $this->pointer[] = array(
            "type" => "SetFillColor",
            "r" => $fillcolor["r"],
            "g" => $fillcolor["g"],
            "b" => $fillcolor["b"],
            "hidden_type" => "fillcolor");
        $this->pointer[] = array(
            "type" => "SetFont",
            "font" => $font,
            "fontstyle" => $fontstyle,
            "fontsize" => $fontsize,
            "hidden_type" => "font");
        //$data->hyperlinkReferenceExpression=$this->analyse_expression($data->hyperlinkReferenceExpression);
        switch ($data->textFieldExpression) {
            case 'new java.util.Date()':
                $this->pointer[] = array(
                    "type" => "MultiCell",
                    "width" => $data->reportElement["width"],
                    "height" => $height,
                    "txt" => date("d/m/y h:i A", time()),
                    "border" => $border,
                    "align" => $align,
                    "fill" => $fill,
                    "hidden_type" => "date",
                    "soverflow" => $stretchoverflow,
                    "poverflow" => $printoverflow,
                    "link" => substr($data->hyperlinkReferenceExpression, 1, -1));
                break;
            case '"Page "+$V{PAGE_NUMBER}+" of"':
                $this->pointer[] = array(
                    "type" => "MultiCell",
                    "width" => $data->reportElement["width"],
                    "height" => $height,
                    "txt" => 'Page $this->PageNo() of',
                    "border" => $border,
                    "align" => $align,
                    "fill" => $fill,
                    "hidden_type" => "pageno",
                    "soverflow" => $stretchoverflow,
                    "poverflow" => $printoverflow,
                    "link" => substr($data->hyperlinkReferenceExpression, 1, -1),
                    "pattern" => $data["pattern"]);
                break;
            case '$V{PAGE_NUMBER}':
                if (isset($data["evaluationTime"]) && $data["evaluationTime"] == "Report") {
                    $this->pointer[] = array(
                        "type" => "MultiCell",
                        "width" => $data->reportElement["width"],
                        "height" => $height,
                        "txt" => '{nb}',
                        "border" => $border,
                        "align" => $align,
                        "fill" => $fill,
                        "hidden_type" => "pageno",
                        "soverflow" => $stretchoverflow,
                        "poverflow" => $printoverflow,
                        "link" => substr($data->hyperlinkReferenceExpression, 1, -1),
                        "pattern" => $data["pattern"]);
                } else {
                    $this->pointer[] = array(
                        "type" => "MultiCell",
                        "width" => $data->reportElement["width"],
                        "height" => $height,
                        "txt" => '$this->PageNo()',
                        "border" => $border,
                        "align" => $align,
                        "fill" => $fill,
                        "hidden_type" => "pageno",
                        "soverflow" => $stretchoverflow,
                        "poverflow" => $printoverflow,
                        "link" => substr($data->hyperlinkReferenceExpression, 1, -1),
                        "pattern" => $data["pattern"]);
                }
                break;
            case '" " + $V{PAGE_NUMBER}':
                $this->pointer[] = array(
                    "type" => "MultiCell",
                    "width" => $data->reportElement["width"],
                    "height" => $height,
                    "txt" => ' {nb}',
                    "border" => $border,
                    "align" => $align,
                    "fill" => $fill,
                    "hidden_type" => "nb",
                    "soverflow" => $stretchoverflow,
                    "poverflow" => $printoverflow,
                    "link" => substr($data->hyperlinkReferenceExpression, 1, -1),
                    "pattern" => $data["pattern"]);
                break;
            case '$V{REPORT_COUNT}':
                $this->report_count = 0;
                $this->pointer[] = array(
                    "type" => "MultiCell",
                    "width" => $data->reportElement["width"],
                    "height" => $height,
                    "txt" => &$this->report_count,
                    "border" => $border,
                    "align" => $align,
                    "fill" => $fill,
                    "hidden_type" => "report_count",
                    "soverflow" => $stretchoverflow,
                    "poverflow" => $printoverflow,
                    "link" => substr($data->hyperlinkReferenceExpression, 1, -1),
                    "pattern" => $data["pattern"]);
                break;
            case '$V{' . (isset($this->band[0]["gname"])?$this->band[0]["gname"]:"XXXX") . '_COUNT}':
                $this->group_count = 0;
                $this->pointer[] = array(
                    "type" => "MultiCell",
                    "width" => $data->reportElement["width"],
                    "height" => $height,
                    "txt" => &$this->group_count,
                    "border" => $border,
                    "align" => $align,
                    "fill" => $fill,
                    "hidden_type" => "group_count",
                    "soverflow" => $stretchoverflow,
                    "poverflow" => $printoverflow,
                    "link" => substr($data->hyperlinkReferenceExpression, 1, -1),
                    "pattern" => $data["pattern"]);
                break;
            default:
                $writeHTML = false;
                $isPrintRepeatedValues=false;
                if ($data->reportElement->property["name"] == "writeHTML")
                    $writeHTML = $data->reportElement->property["value"];
                if (isset($data->reportElement["isPrintRepeatedValues"]))
                    $isPrintRepeatedValues = $data->reportElement["isPrintRepeatedValues"];

                $this->pointer[] = array(
                    "type" => "MultiCell",
                    "width" => $data->reportElement["width"],
                    "height" => $height,
                    "txt" => $data->textFieldExpression,
                    "border" => $border,
                    "align" => $align,
                    "fill" => $fill,
                    "hidden_type" => "field",
                    "soverflow" => $stretchoverflow,
                    "poverflow" => $printoverflow,
                    "printWhenExpression" => $data->reportElement->printWhenExpression,
                    "link" => substr($data->hyperlinkReferenceExpression, 1, -1),
                    "pattern" => $data["pattern"],
                    "writeHTML" => $writeHTML,
                    "isPrintRepeatedValues" => $isPrintRepeatedValues,
                    "rotation" => $rotation);
                break;
        }
    }

    private function ParserElementSubReport($data) {
        $this->pointer[] = array(
            "type" => "subreport",
            "x" => $data->reportElement["x"],
            "y" => $data->reportElement["y"],
            "width" => $data->reportElement["width"],
            "height" => $data->reportElement["height"],
            "subreportparameterarray" => $data->subreportParameter,
            "connectionExpression" => $data->connectionExpression,
            "subreportExpression" => $data->subreportExpression);
    }

}
