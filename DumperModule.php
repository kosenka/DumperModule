<?php
/**
 * Dumper module for Yii Framework.
 *
 * @author Vladimir Papaev <kosenka@gmail.com>
 * @version 0.1
 * @license http://www.opensource.org/licenses/bsd-license.php
 *
 */

class DumperModule extends CWebModule
{
        public $backupPath="";
        public $dbnames;
        public $dbhost;
        public $dbuser;
        public $dbpass;
        public $dbprefix;

	/**
	 * @var string the application layout.
	 * Change this if you wish to use a different layout with the module.
	 */
	public $appLayout = 'application.views.layouts.main';
	/**
	 * @var string string the id of the default controller for this module.
	 */
	public $defaultController = 'default';

        public $users = array();//array('admin',);
        public $roles = array();//array('Administrator',);
        public $ips   = array(); //allowed ip

        private $time_limit=600;
        public $sk;

	public function init()
	{
		// import the module-level models and components
		//$this->setImport(array(
		//	'dumper.components.*',
		//	'dumper.models.*',
		//));

                if(empty($this->backupPath))
                {
                        $this->backupPath=Yii::app()->getBasePath().DIRECTORY_SEPARATOR.'_backup'.DIRECTORY_SEPARATOR;
                }

                if(!file_exists($this->backupPath))
                {
                        @mkdir($this->backupPath);
                        if(!file_exists($this->backupPath))
                                throw new CException("\nCan't create folder: ".$this->backupPath."\n");
                }
                if (!is_writable($this->backupPath))
                        throw new CException("\nFolder is not writable: ".$this->backupPath."\n");

                $is_safe_mode = ini_get('safe_mode') == '1' ? 1 : 0;
                if (!$is_safe_mode && function_exists('set_time_limit')) set_time_limit($this->time_limit);

                $parsed=$this->parseDSN(Yii::app()->db->connectionString);

                if(empty($this->dbnames))
                {
                        $this->dbnames=$parsed['dbname'];
                }
                if(empty($this->dbhost))
                {
                        $this->dbhost=$parsed['dbhost'];
                }
                if(empty($this->dbuser))
                {
                        $this->dbuser=Yii::app()->db->username;
                }
                if(empty($this->dbpass))
                {
                        $this->dbpass=Yii::app()->db->password;
                }

                require dirname(__FILE__).DIRECTORY_SEPARATOR."dumper.php";
                $this->sk=new dumper();
                $this->sk->backupPath=$this->backupPath;
                $this->sk->dbprefix=$this->dbprefix;
                $this->sk->dbnames=$this->dbnames;
                $this->sk->dbhost=$this->dbhost;
                $this->sk->dbuser=$this->dbuser;
                $this->sk->dbpass=$this->dbpass;
	}

	public function beforeControllerAction($controller, $action)
	{
		if(parent::beforeControllerAction($controller, $action))
		{
			return true;
		}
		else
			return false;
	}

        public function getIp()
        {
                $strRemoteIP = $_SERVER['REMOTE_ADDR'];
                if (!$strRemoteIP) { $strRemoteIP = urldecode(getenv('HTTP_CLIENTIP')); }
                if (getenv('HTTP_X_FORWARDED_FOR')) { $strIP = getenv('HTTP_X_FORWARDED_FOR'); }
                elseif (getenv('HTTP_X_FORWARDED')) { $strIP = getenv('HTTP_X_FORWARDED'); }
                elseif (getenv('HTTP_FORWARDED_FOR')) { $strIP = getenv('HTTP_FORWARDED_FOR'); }
                elseif (getenv('HTTP_FORWARDED')) { $strIP = getenv('HTTP_FORWARDED'); }
                else { $strIP = $_SERVER['REMOTE_ADDR']; }

                if ($strRemoteIP != $strIP) { $strIP = $strRemoteIP.", ".$strIP; }
                return $strIP;
        }

        /**
        * array parseDSN(mixed $dsn)
        * Parse a data source name.
        * See parse_url() for details.
        */
        protected function parseDSN($dsn)
        {
                if (is_array($dsn)) return $dsn;
                $parsed = @parse_url($dsn);
                if (!$parsed) return null;
                $params = null;
                if (!empty($parsed['query']))
                {
                        parse_str($parsed['query'], $params);
                        $parsed += $params;
                }
                $parsed['dsn'] = $dsn;

                $path=explode(";",$parsed['path']);
                $host=explode("=",$path[0]);
                $dbname=explode("=",$path[1]);
                $parsed['dbhost']=$host[1];
                $parsed['dbname']=$dbname[1];

                return $parsed;
        }

}
