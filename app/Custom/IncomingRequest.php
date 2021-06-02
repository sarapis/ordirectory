<?php
namespace App\Custom;

class IncomingRequest
{
	protected $ip, $userAgent, $key, $cookies;
	
	// ===== singleton =============================================
	
	protected static $instance;

	public static function engine()
	{
        if (!isset(self::$instance)) 
		{
            $c = get_called_class();
            self::$instance = new $c;
        }
        return self::$instance;
	}
		
    public function __clone()
    {
        trigger_error('Clone is not allowed.', E_USER_ERROR);
    }

	// ===== methods =============================================

	protected function __construct()
	{
		$this->ip = $_SERVER['REMOTE_ADDR'] ?? false;
		$this->userAgent = $_SERVER['HTTP_USER_AGENT'] ?? false;
		$this->cookies = $_COOKIE;
		$this->data = (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] == 'application/json') 
				? json_decode(file_get_contents('php://input'), true)
				: '';
		//$this->output();
	}
	
	
	function auth()
	{
		$res = false;
		foreach ($this->cookies as $n=>$v)
			if (CookieModel::check($n, $v))
				$res = true;
		if ($_POST['login'] && $_POST['pass'] && UsersModel::check($_POST['login'], $_POST['pass']))
		{
			CookieModel::sendNew();
			$res = true;
		}
		return $res;
	}


	function logout()
	{
		foreach ($this->cookies as $n=>$v)
			CookieModel::unset($n);
	}
	
	function output()
	{
		echo '<pre>';
		foreach (['ip', 'userAgent', 'data', 'cookies'] as $n)
			if (is_array($this->$n))
			{	
				echo "{$n}=>\n";
				print_r($this->$n);
			}	
			else
				echo "{$n}=>{$this->$n}\n";

		if ($authType = $this->auth())
			echo "auth positive ({$authType})";
		else 
			echo 'auth negative';
		echo '</pre>';
	}
}