<?php

include_once(dirname(__FILE__) . '/IReportParser.php');
include_once(dirname(__FILE__) . '/IReportRender.php');
include_once(dirname(__FILE__) . '/IReportProvider.php');

//error_reporting(E_ERROR | E_WARNING | E_PARSE | E_NOTICE);

class IReport extends CComponent {

    /**
     * @var IReportParser
     */
    private $reportparser;
    /** @var bool*/
    public $debug = false;

    public function __construct($fileReport) {

        $this->reportparser = new IReportParser;
        $this->reportparser->parser(simplexml_load_file($fileReport));
        $this->setParameters(array('title'=>'hello world'));
    }

    public function getParameters() {
        return $this->reportparser->parameters;
    }

    public function setParameters($value) {
        $this->reportparser->parameters = $value;
    }

    public function execute($outpage = 'D') {
        $this->reportparser->debugsql = $this->debug;
        $IRender = new IReportRender($this->reportparser);
        $IRender->debuggroup = $this->debug;
        $IRender->provider = new IReportProviderDB(Yii::app()->db);
        $IRender->outpage = $outpage;
        $IRender->execute();
    }

}

?>
