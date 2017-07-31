<?
class test extends display{
	function __construct(){
		$this->db = new db();
		$this->config = array(
			"smartyList" => ""
		);
		$this->find = '';
		parent::__construct();
	}
	function session(){
		header("COntent-Type: text/plain");
		var_dump($_SESSION);
	}
	function sessionDestroy(){
		session_destroy();
	}
}
?>
