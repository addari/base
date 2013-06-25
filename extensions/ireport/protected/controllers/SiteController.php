<?php

Yii::import('application.extensions.ireport.*');

class SiteController extends Controller {

    public function actionIndex() {
        if (isset($_GET['file'])) {
            $filename = $_GET['file'] . '.jrxml';
            $reportfiledir = dirname(__FILE__) . '/../relatorios/';
            $reportfile = $reportfiledir . $filename;

            switch(strtolower(Yii::app()->request->getParam('type','ireport'))) {
                case 'ireport':
                    $AReport = new IReport($reportfile);

                    $AReport->parameters = array('parameter1'=>$_GET['p']);
                    $AReport->execute('I');
                    break;
                default:
                case 'phpjasper':
                    $JReport = new PHPJasper($reportfile);
                    $JReport->parameters = array('parameter1'=>$_GET['p']);
                    $JReport->execute('I');
                    break;
                case 'jasphp':
                    $title = 'Hello World!';
                    Yii::app()->jasPHP->create(
                    $reportfiledir ,
                    $filename,
                    array(
                    'SUBREPORT_DIR'=>'./',
                    'title' => $title,'parameter1'=>$_GET['p']
                    ));
                    break;
            }
        }



        $this->render('index');
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError() {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest)
                echo $error['message'];
            else
                $this->render('error', $error);
        }
    }

}