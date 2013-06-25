<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');
// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
return array(
    'basePath' => dirname(__FILE__) . DIRECTORY_SEPARATOR . '..',
    'name' => 'RET',
    'language' => 'es',
    'sourceLanguage' => 'en',
    'charset' => 'utf-8',
    'theme' => 'bootstrap',
    'timeZone' => 'America/Panama',
    // preloading 'log' component
    'preload' => array('log', 'bootstrap'),
    // autoloading model and component classes
    'aliases' => array(
        'ext' => '..' . DIRECTORY_SEPARATOR . 'base' . DIRECTORY_SEPARATOR . 'extensions',
    'modules' => '..' . DIRECTORY_SEPARATOR . 'base' . DIRECTORY_SEPARATOR . 'modules',
    ),
    'import' => array(
        'application.models.*',
        'application.components.*',
        'application.extensions.TabularInputManager',
        'ext.multimodelform.MultiModelForm',
        //'application.extensions.coco.*', 
        'ext.awegen.components.*',
        'modules.cruge.components.*',
        'modules.cruge.extensions.crugemailer.*',
    ),
    'modules' => array(
        'importcsv' => array(
            'path' => 'upload', // path to folder for saving csv file and file with import params
        ),
        // uncomment the following to enable the Gii tool
        'gii' => array(
            'class' => 'system.gii.GiiModule',
            'password' => 'compaq12',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters' => array('127.0.0.1', '::1'),
            'generatorPaths' => array(
                #	'application.modules.gii',
                //'ext.giiplus',
                'ext.awegen',
            ),
        ),
        'cruge' => array(
            'tableprefix' => 'cruge_',
            // para que utilice a protected.modules.cruge.models.auth.CrugeAuthDefault.php
            //
            // en vez de 'default' pon 'authdemo' para que utilice el demo de autenticacion alterna
            // para saber mas lee documentacion de la clase modules/cruge/models/auth/AlternateAuthDemo.php
            //
            'availableAuthMethods' => array('default'),
            'availableAuthModes' => array('username', 'email'),
            #'baseUrl' => 'http://coco.com/',
            // NO OLVIDES PONER EN FALSE TRAS INSTALAR
            'debug' => true,
            'rbacSetupEnabled' => false,
            'allowUserAlways' => false,
            // MIENTRAS INSTALAS..PONLO EN: false
            // lee mas abajo respecto a 'Encriptando las claves'
            //
            'useEncryptedPassword' => false,
            // Algoritmo de la función hash que deseas usar
            // Los valores admitidos están en: http://www.php.net/manual/en/function.hash-algos.php
            'hash' => 'md5',
            // a donde enviar al usuario tras iniciar sesion, cerrar sesion o al expirar la sesion.
            //
				// esto va a forzar a Yii::app()->user->returnUrl cambiando el comportamiento estandar de Yii
            // en los casos en que se usa CAccessControl como controlador
            //
				// ejemplo:
            //		'afterLoginUrl'=>array('/site/welcome'),  ( !!! no olvidar el slash inicial / )
            //		'afterLogoutUrl'=>array('/site/page','view'=>'about'),
            //
            'afterLoginUrl' => null,
            'afterLogoutUrl' => null,
            'afterSessionExpiredUrl' => null,
            // manejo del layout con cruge.
            //
            'loginLayout' => '//layouts/column',
            'registrationLayout' => '//layouts/column',
            'activateAccountLayout' => '//layouts/column2',
            'editProfileLayout' => '//layouts/column2',
            // en la siguiente puedes especificar el valor "ui" o "column2" para que use el layout
            // de fabrica, es basico pero funcional.  si pones otro valor considera que cruge
            // requerirá de un portlet para desplegar un menu con las opciones de administrador.
            //
            'generalUserManagementLayout' => 'ui',
        ),
    ),
    // application components
    'components' => array(
        'bootstrap' => array(
            'class' => 'ext.bootstrap.components.Bootstrap', // assuming you extracted bootstrap under extensions
        ),
        /*
          'localtime'=>array(
          'class'=>'LocalTime',
          ),
         */
        'user' => array(
            // enable cookie-based authentication
            'allowAutoLogin' => true,
            'class' => 'modules.cruge.components.CrugeWebUser',
            'loginUrl' => array('/cruge/ui/login'),
        ),
        'authManager' => array(
            'class' => 'modules.cruge.components.CrugeAuthManager',
        ),
        'crugemailer' => array(
            'class' => 'modules.cruge.components.CrugeMailer',
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

          /*
          'urlManager'=>array(
          'urlFormat'=>'path',
          'showScriptName'=>false,	// Linea añadida
          #'urlSuffix'=>'.php',		// Linea añadida
          'rules'=>array(
          '<controller:\w+>/<id:\d+>'=>'<controller>/view',
          '<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
          '<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
          ),
          ),
         */

        /* 'db'=>array(
          'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
          ), */
        // uncomment the following to use a MySQL database
        'db' => array(
            #'connectionString' => 'mysql:host=50.63.244.26;dbname=sipret',
            #'username' => 'sipret',
            #'password' => 'Compaq@12',
            'connectionString' => 'mysql:host=localhost;dbname=sipret',
            'username' => 'root',
            'password' => 'compaq12',
            'emulatePrepare' => true,
            'charset' => 'utf8',
            'attributes' => array(
                PDO::MYSQL_ATTR_LOCAL_INFILE
            ),
        ),
        'coreMessages' => array(
            'basePath' => 'protected/messages',
        ),
        /*
          'messages'=>array(
          'basePath'=>'protected/messages',
          ),
         */
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
    ),
    // application-level parameters that can be accessed
    // using Yii::app()->params['paramName']
    'params' => array(
        // this is used in contact page

        'adminEmail' => 'webmaster@example.com',
        'imageLogo' => 'tcpdf_logo.jpg',
    ),
);