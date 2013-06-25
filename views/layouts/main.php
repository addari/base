
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="author" content="">
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width">
        <title><?php echo CHtml::encode($this->pageTitle); ?></title>
        <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css" />
        <style>.brand
            {
                background: url(css/logo-35.png) no-repeat left center;
            }
            .checkboxgroup{
                overflow:auto;
            }
            .checkboxgroup div{
                width:200px;
                float:left;
            }

        </style>

    </head>

    <body>

     <div id="header">
            <div class="container-fluid" id="page">
                <!---Menu Superior -->
                
            </div>

            <div class="clear" ><br></div>
            <div class="clear" ><br></div>
            <div class="clear" ><br></div>
        </div>

        <div class="container-fluid"> 
            <div class="row-fluid">
                <div class="span12">

                    <?php if (isset($this->breadcrumbs)) { ?>
                        <?php
                        $this->widget('bootstrap.widgets.TbBreadcrumbs', array(
                            'links' => $this->breadcrumbs,
                        ));
                        ?>
                    <?php } ?>
                    <?php echo $content; ?>
                </div></div>

            <!--<div class="clear"></div>-->


        </div>
        <?php if (Yii::app()->user->name == 'admin') { ?>
            <div class="container-fluid"> 
                <div class="row-fluid">
                    <div class="span12"><?php
            //shortcut
            $translate = Yii::app()->translate;
//in your layout add
            echo $translate->dropdown();
//adn this
            if ($translate->hasMessages()) {
                //generates a to the page where you translate the missing translations found in this page
                echo $translate->translateLink('Translate');
                //or a dialog
                echo $translate->translateDialogLink('Translate', 'Translate page title');
            }
//link to the page where you edit the translations
            echo $translate->editLink('Edit translations page');
//link to the page where you check for all unstranslated messages of the system
            echo $translate->missingLink('Missing translations page');
            ?></div></div></div> <?php } ?>
    </body>
</html>

