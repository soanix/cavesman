<?
class user extends display{
    function __construct(){
        $this->db = new db();
		parent::__construct();
    }
    function login(){
		$array = $this->users->login();
		if($array){
			$_SESSION['user'] = $array;
		}
		header("Location: /");
    }
	function logout(){
		$_SESSION['theme'] = "public";
		unset($_SESSION['user']);
		header("Location: "._PATH_);
	}

}
