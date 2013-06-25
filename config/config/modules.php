<?php
return array(
'menubuilder'=>array(
            'theme'=>'bootstrap', //comment for bluegrid theme (=default)
            'checkInstall'=>false, //uncomment after first usage
            //'cacheDuration'=> -1, //uncomment for disabling the menucaching
            //'languages'=>array('de','en_us'),
            'supportedScenarios'=>array('backend' => 'Backend', 'frontend' => 'Frontend', 'dashboard' => 'Dashboard'),
            //'dataAdapterClass'=> 'EMBDbAdapter', //'EMBMongoDbAdapter', //'EMBDbAdapter',
 
            //the available menus/lists for the preview
            'previewMenus'=>array(
               // 'superfish'=>'Superfish',
               // 'mbmenu'=>'MbMenu',
                'bootstrapnavbar'=>'Bootstrap Navbar',
                'bootstrapmenu'=>'Bootstrap Menu',
              // 'dropdownlist'=>'Dropdownlist',
                'unorderedlist'=>'Unordered list'
            )
        ),

    'translate',
        'gii' => array(
            'class'          => 'system.gii.GiiModule',
            'password'       => 'compaq12',
            // If removed, Gii defaults to localhost only. Edit carefully to taste.
            'ipFilters'      => array('127.0.0.1', '::1'),
            'generatorPaths' => array(
				#	'application.modules.gii',
				//'ext.giiplus',
                'ext.awegen',
            ),
        ),
                
                'cruge'       => array(
                'tableprefix' => 'cruge_',
            // para que utilice a protected.modules.cruge.models.auth.CrugeAuthDefault.php
//
            // en vez de 'default' pon 'authdemo' para que utilice el demo de autenticacion alterna
// para saber mas lee documentacion de la clase modules/cruge/models/auth/AlternateAuthDemo.php
//
            'availableAuthMethods' => array('default'),
            'availableAuthModes'   => array('username', 'email'),
            #'baseUrl' => 'http://coco.com/',
            // NO OLVIDES PONER EN FALSE TRAS INSTALAR
            'debug'            => true,
            'rbacSetupEnabled' => false,
            'allowUserAlways'  => false,
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
            'loginLayout' => '//layouts/column1',
            'registrationLayout' => '//layouts/column1',
            'activateAccountLayout' => '//layouts/column1',
            'editProfileLayout' => '//layouts/column1',
            // en la siguiente puedes especificar el valor "ui" o "column2" para que use el layout
// de fabrica, es basico pero funcional.  si pones otro valor considera que cruge
// requerirá de un portlet para desplegar un menu con las opciones de administrador.
//
            'generalUserManagementLayout' => 'ui',
        ),

);
?>
