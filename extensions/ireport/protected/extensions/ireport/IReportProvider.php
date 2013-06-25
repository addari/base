<?php

class IReportProvider {

    protected $data = array();

    public function getData() {
        return $this->data;
    }

    public function execute($IReportParserXML) {
        
    }

}

class IReportProviderDB extends IReportProvider {

    private $connection;

    public function __construct($connection) {
        $this->connection = $connection;
    }

    private function buildsql($IReportParserXML) {
        $sql = $IReportParserXML->sql;
        if (isset($IReportParserXML->parameters)) {
            foreach ($IReportParserXML->parameters as $v => $a) {
                $sql = str_replace('$P{' . $v . '}', $a, $sql);
            }
        }
        return $sql;
    }

    public function execute($IReportParserXML) {
        $this->m = 0;
        $command = $this->connection->createCommand($this->buildsql($IReportParserXML));
        $reader = $command->query();
        foreach ($reader as $row) {
            foreach ($IReportParserXML->fields as $out) {
                $this->data[$this->m]["$out"] = $row["$out"];
            }
            $this->m++;
        }
    }

}

class IReportProviderFileXml extends IReportProvider {

    private $fileName;

    public function __construct($fileName) {
        $this->fileName = $fileName;
    }

    public function execute($IReportParserXML) {
        if (!file_exists($fileName))
            echo "File - $fileName does not exist";
        else {
            $this->m = 0;
            $xmlAry = $this->xmlobj2arr(simplexml_load_file($fileName));
            foreach ($xmlAry[header] as $key => $value)
                $this->data["$this->m"]["$key"] = $value;
            foreach ($xmlAry[detail][record]["$this->m"] as $key2 => $value2)
                $this->data["$this->m"]["$key2"] = $value2;
        }
        //  if (isset($this->arrayVariable)) //if self define variable existing, go to do the calculation
        //        $this->variable_calculation($m);   
    }

    private function xmlobj2arr($Data) {
        if (is_object($Data)) {
            foreach (get_object_vars($Data) as $key => $val)
                $ret[$key] = $this->xmlobj2arr($val);
            return $ret;
        } elseif (is_array($Data)) {
            foreach ($Data as $key => $val)
                $ret[$key] = $this->xmlobj2arr($val);
            return $ret;
        }
        else
            return $Data;
    }

}

?>
