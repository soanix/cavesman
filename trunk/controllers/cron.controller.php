<?
class cron extends display{
	private $accepted_codes = array(
		"CRONTOKEN-ALPHANUMERIC-KEY" // TPVCM WEB SERVER
	);
	function __construct(){
		$token = isset($_GET['token']) ? $_GET['token'] : '';
		if(!in_array($token, $this->accepted_codes)){
			header('HTTP/1.0 403 Forbidden');
			exit();
		}
		include_once(_ROOT_."/classes/db.class.php");
		$this->db = new db();
        $this->config = array();
        parent::__construct();
	}
	function testCron(){

	}
	function executeAll(){
		/* ALL FUNCTIONS*/
	}
}
?>
