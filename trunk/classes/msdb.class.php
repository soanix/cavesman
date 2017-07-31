<?

/*
 * Autor: Soanix
 *
 *
 *
 * $db = new db(); // Simple
 *
 * Inicialización
 * ***************
 *
 * MAL: $db->consulta("SELECT mierda WHERE mierda = '".$mierda."'"); // Manera insegura, pero funciona igual...
 * BIEN: $db->consulta("SELECT mierda WHERE mierda = ':mierda'"); // Variable segura, se sustituye en la función safe
 *
 * $db->safe(':mierda', $mierda); // Aseguras la variable con un alias
 *
 * Simple ejecución
 * ******************
 *
 * $db->execute(); // Antiguo consulta
 *
 * Devolver arrays
 * ********************
 *
 * $db->get_array($type = MYSQL_ASSOC); // antiguo mysql_fetch_array
 * $db->get_full_array($type = MYSQL_ASSOC); // array completo con el while y toda la fiesta
 *
 *
 *
 * Ejemplo :
 * ***************
 *
 * $email = isset($_POST['email]) ? $_POST['email] : '';
 * $ssql = "SELECT user_id, firstname, lastname WHERE email = ':email'";
 *
 * $db->consulta($ssql);
 * $db->safe(':email', $email);
 *
 * Ahora puedes:
 *
 * $consulta = $db->exec();
 *
 * o tambien
 *
 * $linea = $db->get_array(_MYSQL_ASSOC_);
 *
 * o tambien
 *
 * $array = $db->get_full_array(_MYSQL_ASSOC_);
 *
 * */
class msdb
{
	private $binds = array();
	public $sql = '';
	public $db = false;
	public $query = NULL;
	public $result = false;

	function __construct(){ // Se conecta a mysqli
		$this->db = new PDO("sqlsrv:Server=IP;Database=DBNAME", "USER", "PASSWORD");
	}
  	function create($consulta){ // genera el string de SQL para ser compatible con todas las funciones
        $this->query = $this->db->prepare($consulta);
		//$this->set_charset("utf8");
    }
	function safe($bind, $var){ // Asegura las variables
		$safe = $this->real_escape_string(htmlspecialchars($var, ENT_QUOTES | ENT_HTML401, 'UTF-8'));
		$this->sql = str_replace($bind, $safe, $this->sql);
		return $safe;
	}
	function unsafe($var){
		return htmlspecialchars_decode($var, ENT_QUOTES | ENT_HTML401);
	}
	function safeLiteral($bind, $var){ // Asegura las variables
		$safe = $this->real_escape_string($var);
		$this->sql = str_replace($bind, $safe, $this->sql);
		return $safe;
	}
	function last_insert_id(){
		return $this->insert_id;
	}
	function multiple(){
		$consulta = $this->multi_query($this->sql);
		while ($this->next_result()) {;}
		if($this->error)
			$this->log_error();
		return $consulta;
	}
	function log_error(){
		/*$sql = "INSERT INTO error_log (user_id, tipo, url, description) VALUES (".(isLogged ? $_SESSION['user']['user_id'] : 5).", 'MYSQL', '".$_SERVER['REQUEST_URI']."', '".$this->real_escape_string($this->error." || ".$this->sql)."')";
		$this->query($sql);*/
		/*if(_DEV_MODE_)
			echo $this->error;*/
		header("Content-Type: text/plain");
		echo $this->error."<br><br>";
		echo $this->sql."<br>";
		exit();
	}
	function execute($consulta = NULL){ // Ejecuta una consulta al servidor
		$this->result = $this->query->execute();
		return $this->result;
	}
	function get_array($type = PDO::FETCH_ASSOC){ // Devuelve un array compatible con while
		$this->result = $this->query->fetch($type);

		return $this->result;
	}
	function get_full_array($type = PDO::FETCH_ASSOC){ // Devuelve un array con todos los resultados
		$this->result = $this->query->fetchAll($type);

		return $this->result;
	}
	function close(){
		$this->query = NULL;
		$this->db = NULL;
	}
}

?>
