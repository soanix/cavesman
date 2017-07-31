<?
class users {
	function __construct(){
        $this->db = new db();
    }
	/**
	 *
	 * create function
	 *
	 *
	 * @return 	INT 	Created user ID
	 *
 	*/
	function login(){
		$ssql = "SELECT u.user_id, u.firstname, u.lastname, u.permisos
				FROM users u
				WHERE u.user = ':user'
				AND u.password = ':password'
				AND	(
					DATE(date_expire) >= DATE(NOW())
					OR sysuser = 1
				)
				LIMIT 1
				";
		$this->db->create($ssql);
		$this->db->safe(":user", $this->p("user"));
		$this->db->safe(":password", hash("sha256", $this->p("password")));
		return $this->db->get_array();
	}
	/**
	 *
	 * create function
	 *
	 *
	 * @return 	INT 	Created user ID
	 *
 	*/
	function create($user = ''){
		$ssql = "INSERT IGNORE INTO users (user, permisos) VALUES (':user', '2')";
		$this->db->create($ssql);
		$this->db->safe(":user", $user);
		$this->db->execute();
		return  $this->db->last_insert_id();
	}
	/**
	 *
	 * getPermisos function
	 *
	 *
	 * @return 	Array 	List of user permisions
	 *
 	*/
	function getPermisos(){
		$this->db->create("SELECT permisos FROM users WHERE user_id = ':user_id'");
		$this->db->safe(":user_id", $_SESSION['user']['user_id']);
		$user = $this->db->get_array();
		return explode(",", $user['permisos']);
	}
	/**
	 *
	 * getPermisos function
	 *
	 *
	 * @return 	Array 	List of user permisions
	 *
 	*/
	function getUserPermisos($user_id = 0){
		$this->db->create("SELECT permisos FROM users WHERE user_id = ':user_id'");
		$this->db->safe(":user_id", $user_id);
		$user = $this->db->get_array();
		return explode(",", $user['permisos']);
	}

	/**
	 *
	 * update function
	 *
	 *
	 * @return 	BOOL 	Execute update boolean
	 *
 	*/
	function update($user_id = '', $user = '', $name = '', $password = '', $date_expire = '', $active = 1, $permanent = 0){
		$permisos = $this->getUserPermisos($user_id);
		$ssql = "UPDATE users SET
					firstname = ':name',
					user = ':puser',
					password = ':password',
					date_expire = ':date_expire',
					active = ':active',
					permanent = ':permanent'";
		if(empty($permiso))
			$ssql .= ", permisos = '2'";
		$ssql .=" WHERE user_id = ':user_id'";
		$this->db->create($ssql);
		$this->db->safe(":name", $name);
		$this->db->safe(":puser", $password);
		$this->db->safe(":code", $user);
		$this->db->safe(":date_expire", $date_expire);
		$this->db->safe(":password", hash("sha256", $password));
		$this->db->safe(":active", $active);
		$this->db->safe(":permanent", $permanent);
		$this->db->safe(":user_id", $user_id);
		return $this->db->execute();
	}
	function p($value = '', $default = ''){
        return isset($_POST[$value]) ? $_POST[$value] : $default;
    }
    function g($value = '', $default = ''){
        return isset($_GET[$value]) ? $_GET[$value] : $default;
    }
}
?>
