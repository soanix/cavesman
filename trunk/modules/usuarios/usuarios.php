<?
class usuarios extends modules{
    function __construct(){
        $this->db = new db();
        $this->config = array(
            "active" => 1,
            "name" => "usuarios",
            "directory" => dirname(__FILE__)."/tpl",
            "title" => "Usuarios",
            "description" => "Modulo que permite administrar usuarios"
        );
        self::install();
        parent::loadSmarty();
		$this->lang = new lang();
		$this->smarty->assign("iso", $this->lang->iso);
    }
    function install(){
        if(!is_dir(_ROOT_."/img/m/".$this->config['name']))
            mkdir(_ROOT_."/img/m/".$this->config['name']);
        if(!is_dir(_ROOT_."/img/m/".$this->config['name']."/b"))
            mkdir(_ROOT_."/img/m/".$this->config['name']."/b");
        if(!is_dir(_ROOT_."/img/m/".$this->config['name']."/s"))
            mkdir(_ROOT_."/img/m/".$this->config['name']."/s");
        if(!is_dir(_ROOT_."/img/m/".$this->config['name']."/o"))
            mkdir(_ROOT_."/img/m/".$this->config['name']."/o");
    }
    function single(){
        $return = array();
        return $return;
    }
    function get(){
        $promociones['all'] = $this->getList();
        $promociones['config'] = $this->config;
        return $promociones;
    }
    function getFromIdRowTable($user_id = 0){
        $ssql = "SELECT user_id, firstname, lastname, active
				FROM users
				WHERE user_id = ':user_id'";
        $this->db->create($ssql);
        $this->db->safe(":user_id", $user_id);
        return $this->db->get_array();
    }
    function getList(){
        $ssql = "SELECT user_id, firstname, lastname, active FROM users WHERE sysuser = 1 OR DATE(date_expire) >= DATE(NOW()) ORDER BY sysuser DESC, firstname ASC";
        $this->db->create($ssql);
        return $this->db->get_full_array();
    }
    function edit(){
        $ssql = "SELECT
						user_id, firstname, lastname, user, active, permisos
					FROM users
					WHERE user_id = ':user_id'
					GROUP BY user_id";
        $this->db->create($ssql);
        $this->db->safe(":user_id", $this->p("user_id"));
        $return = $this->db->get_array();
        return $return;
    }
    function create($user = ''){
        $ssql = "INSERT INTO users (user, permisos, sysuser) VALUES (':user', '2', 1)";
        $this->db->create($ssql);
        $this->db->safe(":user", $user);
        $this->db->execute();
        return  $this->db->last_insert_id();
    }
    function delete(){
        $promocion_id = isset($_POST['promocion_id']) ? $_POST['promocion_id'] : 0;
        $ssql = "DELETE FROM promociones WHERE promocion_id = ':promocion_id'";
        $this->db->create($ssql);
        $this->db->safe(":promocion_id", $promocion_id);
        return  $this->db->execute();
    }
    function save(){

        $user_id = $this->p("user_id", false) ? $this->p("user_id", false) : $this->create($this->p("user"));

		/* ACTUALIZADO LOS DATOS BASICOS DE USUARIO */
        $ssql = "
            UPDATE users
            SET
				firstname = ':firstname',
				lastname = ':lastname',
				permisos = ':permisos',
                user = ':user'";
		if($this->p("password"))
			$ssql .=", password = ':password'";
		$ssql .= "
            	WHERE user_id = ':user_id'
                ";
        $this->db->create($ssql);
		$this->db->safe(":user_id", $user_id);
        $this->db->safe(":firstname", $this->p("firstname"));
        $this->db->safe(":lastname", $this->p("lastname"));
		$this->db->safe(":permisos", implode(",", $this->p("permisos", array())));
		$this->db->safe(":user", $this->p("user"));
        $this->db->safe(":password", hash("sha256", $this->p("password")));
        $this->db->execute();
		$almacenes = $this->p("tiendas", array());

        $usuario = $this->getFromIdRowTable($user_id);
        $this->smarty->assign("usuario", $usuario);
        $return["user_id"] = $user_id;
        $return["html"] = $this->smarty->fetch("usuario.tpl");
        return $return;
    }
}
?>
