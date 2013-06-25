   public function filters()
   {
      return array(array('CrugeAccessControlFilter'));
   }

	public function accessRules()
	{
		return array(
			array('allow',  // allow authenticated user to perform 'index' actions
				'actions'=>array('index'),
			),
			array('allow',  // allow authenticated user to perform 'view' actions
				'actions'=>array('view'),
			),
			array('allow', // allow authenticated user to perform 'create' actions
				'actions'=>array('create'),
			),
			array('allow',  // allow authenticated user to perform 'update' actions
				'actions'=>array('update'),
			),
			array('allow',  // allow authenticated user to perform 'GeneratePdf' actions
				'actions'=>array('GeneratePdf'),
			),
			array('allow',  // allow authenticated user to perform 'GenerateExcel' actions
				'actions'=>array('GenerateExcel'),
			),
			array('allow', // allow authenticated user to perform 'admin' actions
				'actions'=>array('admin'),
			),
			array('allow', // allow authenticated user to perform 'delete' actions
				'actions'=>array('delete'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
