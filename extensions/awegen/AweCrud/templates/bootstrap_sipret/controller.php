<?php
/**
 * This is the template for generating a controller class file for CRUD feature.
 * The following variables are available in this template:
 * - $this: the CrudCode object
 */
?>
<?php echo "<?php\n"; ?>
class <?php echo $this->controllerClass; ?> extends <?php echo $this->baseControllerClass." {\n"; ?>

<?php 
        $authpath = 'ext.awegen.AweCrud.templates.bootstrap.auth.';
	Yii::app()->controller->renderPartial($authpath . $this->authtype);
?>

    /*public function actionIndex() {
        $dataProvider = new CActiveDataProvider('<?php echo $this->modelClass; ?>');
        $this->render('index', array(
                'model' => $dataProvider,
        ));
    }*/
        
    public function actionView($id) {
        $this->render('view', array(
                'model' => $this->loadModel($id),
        ));
    }
        
    public function actionCreate() {
        $model = new <?php echo $this->modelClass; ?>;
        <?php if($this->validation == 1 || $this->validation == 3) { ?>
            $this->performAjaxValidation($model, '<?php echo $this->class2id($this->modelClass)?>-form');
        <?php } ?>
        if (isset($_POST['<?php echo $this->modelClass; ?>'])) {
            $model->setAttributes($_POST['<?php echo $this->modelClass; ?>']);

<?php
			
			foreach(CActiveRecord::model($this->modelClass)->relations() as $key => $relation)
			{
				if($relation[0] == CActiveRecord::BELONGS_TO || $relation[0] == CActiveRecord::MANY_MANY)
				{
				printf("\t\t\t if (isset(\$_POST['$this->modelClass']['$key'])) \$model->$key = \$_POST['$this->modelClass']['$key'];\n");	
				}
			}
?>
                
                try {
                    if($model->save()) {
                    if (isset($_GET['returnUrl'])) {
                            $this->redirect($_GET['returnUrl']);
                    } else {
                            $this->redirect(array('view','id'=>$model-><?php echo Awecms::getPrimaryKey($this) ?>));
                    }
                }
                } catch (Exception $e) {
                        $model->addError('<?php echo $this->identificationColumn;?>', $e->getMessage());
                }
        } elseif(isset($_GET['<?php echo $this->modelClass; ?>'])) {
                        $model->attributes = $_GET['<?php echo $this->modelClass; ?>'];
        }

        $this->render('create',array( 'model'=>$model));
    }

    public function actionUpdate($id) {
        $model = $this->loadModel($id);
        <?php if($this->validation == 1 || $this->validation == 3) { ?>
        $this->performAjaxValidation($model, '<?php echo $this->class2id($this->modelClass)?>-form');
        <?php } ?>

        if(isset($_POST['<?php echo $this->modelClass; ?>'])) {
            $model->setAttributes($_POST['<?php echo $this->modelClass; ?>']);
<?php
			foreach(CActiveRecord::model($this->modelClass)->relations() as $key => $relation) {
				if($relation[0] == CActiveRecord::BELONGS_TO || $relation[0] == CActiveRecord::MANY_MANY)
                                    printf("\t\t\t\$model->$key = \$_POST['$this->modelClass']['$key'];\n");	
			}
?>
                try {
                    if($model->save()) {
                        if (isset($_GET['returnUrl'])) {
                                $this->redirect($_GET['returnUrl']);
                        } else {
                                $this->redirect(array('view','id'=>$model-><?php echo  Awecms::getPrimaryKey($this) ?>));
                        }
                    }
                } catch (Exception $e) {
                        $model->addError('<?php echo $this->identificationColumn;?>', $e->getMessage());
                }

            }

        $this->render('update',array(
                'model'=>$model,
                ));
    }
                
               

    public function actionDelete($id) {
        if(Yii::app()->request->isPostRequest) {    
            try {
                $this->loadModel($id)->delete();
            } catch (Exception $e) {
                    throw new CHttpException(500,$e->getMessage());
            }

            if (!Yii::app()->getRequest()->getIsAjaxRequest()) {
                            $this->redirect(array('admin'));
            }
        }
        else
            throw new CHttpException(400,
                Yii::t('app', 'Invalid request.'));
    }
                
    public function actionIndex() {
        $model = new <?php echo $this->modelClass; ?>('search');
        $model->unsetAttributes();

        if (isset($_GET['<?php echo $this->modelClass; ?>']))
                $model->setAttributes($_GET['<?php echo $this->modelClass; ?>']);

        $this->render('index', array(
                'model' => $model,
        ));
    }
    
    <?php
    if ($this->hasBooleanColumns($this->tableSchema->columns) && $this->isJToggleColumnEnabled){
        ?>
    public function actionToggle($id, $attribute, $model) {
        if (Yii::app()->request->isPostRequest) {
            // we only allow deletion via POST request
            $model = $this->loadModel($id, $model);
            //loadModel($id, $model) from giix
            ($model->$attribute == 1) ? $model->$attribute = 0 : $model->$attribute = 1;
            $model->save();

            // if AJAX request (triggered by deletion via admin grid view), we should not redirect the browser
            if (!isset($_GET['ajax']))
                $this->redirect(isset($_POST['returnUrl']) ? $_POST['returnUrl'] : array('admin'));
        }
        else
            throw new CHttpException(400, 'Invalid request. Please do not repeat this request again.');
    }
    <?php
    }
    
    
    ?>

    public function loadModel($id) {
            $model=<?php echo $this->modelClass; ?>::model()->findByPk($id);
            if($model===null)
                    throw new CHttpException(404,Yii::t('app', 'The requested page does not exist.'));
            return $model;
    }
public function actionGenerateExcel()
	{
            $session=new CHttpSession;
            $session->open();		
            
             if(isset($session['<?php echo $this->modelClass; ?>_records']))
               {
                $model=$session['<?php echo $this->modelClass; ?>_records'];
               }
               else
                 $model = <?php echo $this->modelClass; ?>::model()->findAll();

		
		#Yii::app()->request->sendFile(date('YmdHis').'.xls',
		Yii::app()->request->sendFile('<?php echo $this->modelClass; ?>.xls',
			$this->renderPartial('excelReport', array(
				'model'=>$model
			), true)
		);
	}
        public function actionGeneratePdf() 
	{
           $session=new CHttpSession;
           $session->open();
		Yii::import('ext.giiplus.bootstrap.*');
                require_once(Yii::getPathOfAlias('ext').'/tcpdf/tcpdf.php');
                require_once(Yii::getPathOfAlias('ext').'/tcpdf/config/lang/eng.php');

             if(isset($session['<?php echo $this->modelClass; ?>_records']))
               {
                $model=$session['<?php echo $this->modelClass; ?>_records'];
               }
               else
                 $model = <?php echo $this->modelClass; ?>::model()->findAll();



		$html = $this->renderPartial('expenseGridtoReport', array(
			'model'=>$model
		), true);
		
		//die($html);
		
		$pdf = new TCPDF();
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor(Yii::app()->name);
		$pdf->SetTitle('<?php echo $this->modelClass; ?>');
		$pdf->SetSubject('<?php echo $this->modelClass; ?>');
		#$pdf->SetKeywords('example, text, report');
		$pdf->SetHeaderData('', 0, "Report", '');
		#$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, "Example Report by ".Yii::app()->name, "");
		#$pdf->SetHeaderData("".Yii::app()->name, "");
		$pdf->SetHeaderData("".Yii::app()->params['imageLogo'], "");
		$pdf->setHeaderFont(Array('helvetica', '', 8));
		$pdf->setFooterFont(Array('helvetica', '', 6));
		$pdf->SetMargins(15, 18, 15);
		$pdf->SetHeaderMargin(5);
		$pdf->SetFooterMargin(10);
		$pdf->SetAutoPageBreak(TRUE, 0);
		$pdf->SetFont('dejavusans', '', 7);
		$pdf->AddPage();
		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->LastPage();
		$pdf->Output("<?php echo $this->modelClass; ?>.pdf", "I");
	}

}