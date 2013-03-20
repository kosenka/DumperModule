<?php

class DefaultController extends CController
{
	protected function beforeAction()
	{
                $disable = in_array(Yii::app()->user->name, $this->module->users);
                foreach ($this->module->roles as $role)
                {
                        $disable = $disable || Yii::app()->user->checkAccess($role);
                }

                $disable = $disable || in_array($this->module->getIp(), $this->module->ips);

		if(!$disable)
                {
                        throw new CHttpException(404,'The requested page does not exist.');
                }
                return true;
	}

        /**
        * @return array action filters
        */
        public function filters()
        {
                return array(
                        'accessControl', // perform access control for CRUD operations
                );
        }

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules()
        {
		return array(
			array('allow',
				'actions'=>array('index','dbwork','dbbackup','dbrestore'),
				'users'=>array('@'),
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}

        public function actionIndex()
        {
                $results=Yii::app()->db->createCommand("SHOW TABLES")->queryColumn();
                $tables="";
                foreach($results as $result)
                {
		       $tables.= "<option value=\"$result\" selected>$result</option>\n";
                }

                $this->render('index',array('tables'=>$tables,'backupPath'=>$this->module->backupPath));
        }

        public function actionDbwork()
        {
                $query = "";

                switch(strtolower($_POST['whattodo']))
                {
                        case "optimize": { $query = "OPTIMIZE TABLE  "; break; }
                        case "repair"  : { $query = "REPAIR TABLE "; break; }
                        default        : { $this->redirect($this->createUrl('/dumper/default')); break; }
                }

                $query.=implode(", ", $_POST['tables']).';';

                if(!empty($query))
                {
                        $results=Yii::app()->db->createCommand($query)->execute();
                }

                $this->redirect($this->createUrl('/dumper/default'));
        }

        public function actionDBbackup()
        {
                $this->module->sk->backup();
        }

        public function actionDBrestore()
        {
                $this->module->sk->restore();
        }

}
