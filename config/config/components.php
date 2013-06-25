<?php
return array(
  'clientScript'=>array(
    'coreScriptPosition' => CClientScript::POS_END,
),
	        'messages' => array(
            'class' => 'CDbMessageSource',
            'onMissingTranslation' => array('Ei18n', 'missingTranslation'),
            //'sourceMessageTable' => 'tbl_source_message',
            'sourceMessageTable' => 'SourceMessage',
            'translatedMessageTable' => 'Message'
        //'translatedMessageTable' => 'tbl_message'
        ),
        'translate' => array(
            'class' => 'translate.components.Ei18n',
            'createTranslationTables' => false,
            'connectionID' => 'db',
            'languages' => array(
                'en' => 'English',
                'es' => 'Español',
                'it' => 'Italiano'
            )
        ),
        'bootstrap' => array(
            'class' => 'ext.bootstrap.components.Bootstrap', // assuming you extracted bootstrap under extensions
            'responsiveCss' => true,
            'enableCdn' => true,
            'coreCss'=> true,
            'fontAwesomeCss'=>false,
        ),

        
        'uimanager' => array('class' => 'application.components.UiManager'),
        /*
          'localtime'=>array(
          'class'=>'LocalTime',
          ),
         */
        'user' => array(
// enable cookie-based authentication
            'allowAutoLogin' => true,
            'class' => 'application.modules.cruge.components.CrugeWebUser',
            'loginUrl' => array('/cruge/ui/login'),
        ),
        'authManager' => array(
            'class' => 'application.modules.cruge.components.CrugeAuthManager',
        ),
        'crugemailer' => array(
            'class' => 'application.modules.cruge.components.CrugeMailer',
            'mailfrom' => 'email-desde-donde-quieres-enviar-los-mensajes@xxxx.com',
            'subjectprefix' => 'Tu Encabezado del asunto - ',
            'debug' => true,
        ),
        'format' => array(
            'datetimeFormat' => "d M, Y h:m:s a",
        ),
        /* 'authManager'=>array(
          'class'=>'CDbAuthManager',
          'connectionID'=>'db',
          #	'itemTable'=>'auth_items',				// cambio del nombre de la tabla
          #	'itemChildTable'=>'auth_relacion',		// cambio del nombre de la tabla
          #	'assignmentTable'=>'auth_asignacion',	// cambio del nombre de la tabla
          ),
          // uncomment the following to enable URLs in path-format

         */
/*        'urlManager' => array(
            'urlFormat' => 'path',
            'showScriptName' => false, // Linea añadida
            //'showScriptName'=>false,
            #'urlSuffix'=>'.php',		// Linea añadida
            'rules' => array(
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>' => '<controller>/<action>',
            ),
        ),
        /* 'db'=>array(
          'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
          ), */
// uncomment the following to use a MySQL database
        'db2' => array(
            'connectionString' => 'mysql:host=localhost;dbname=isretorg_master',
            'username' => 'isretorg_user',
            'password' => 'compaq12',
            'emulatePrepare' => true,
            'charset' => 'utf8',
            'attributes' => array(
                PDO::MYSQL_ATTR_LOCAL_INFILE
            ),
        ),
        /*'db' => array(
            'connectionString' => 'mysql:host=localhost;dbname=sipret',
            'username' => 'root',
            'password' => 'compaq12',
            'emulatePrepare' => true,
            'charset' => 'utf8',
            'attributes' => array(
                PDO::MYSQL_ATTR_LOCAL_INFILE
            ),
        ),*/
        'db' => require($rutaabsoluta.'db.php'),
        'coreMessages' => array(
            'basePath' => 'protected/messages',
        ),
        /* 'messages' => array(
          'basePath' => 'protected/messages',
          ),
          /* 'messages'=>array(
          'class'=>'CDbMessageSource',
          'onMissingTranslation' => array('TranslateModule', 'missingTranslation'),


          'sourceMessageTable' => 'SourceMessage',
          'translatedMessageTable' => 'Message'
          ),
          'translate'=>array(//if you name your component something else change TranslateModule
          'class'=>'translate.components.MPTranslate',
          //any avaliable options here
          'acceptedLanguages'=>array(
          'en'=>'English',
          'pt'=>'Portugues',
          'es'=>'Español'
          ),
          ), */
        'errorHandler' => array(
// use 'site/error' action to display errors
            'errorAction' => 'site/error',
        ),
        'log' => array(
            'class' => 'CLogRouter',
            'routes' => array(
                array(
                    'class' => 'CFileLogRoute',
                    'levels' => 'error, warning',
                ),
            // uncomment the following to show log messages on web pages
            /*
              array(
              'class'=>'CWebLogRoute',
              ),
             */
            ),
        ),
);
?>
