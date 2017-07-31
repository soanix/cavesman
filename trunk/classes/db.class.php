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


class db extends mysqli
{
	private $binds = array();
	public $sql = '';

	function __construct(){ // Se conecta a mysqli
		include(_CONFIG_.'/settings.inc.php');
		parent::__construct( "localhost" ,
							"cavesman" ,
							"1234",
							"cavesman" );

	}
  	function create($consulta){ // genera el string de SQL para ser compatible con todas las funciones
        $this->sql = $consulta;
		$this->set_charset("utf8");
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
		$fp = fopen(_ROOT_."/../material/log/".$_SERVER['SERVER_NAME'].".mysql.error.log", "a+");
		fwrite($fp, date("d-m-Y H:i:s")." - ".$this->error."\n\n".$this->sql."\n\n");
		fclose($fp);
	}
	function debug_bind_param(){
	    $numargs = func_num_args();
	    $numVars = $numargs - 2;
	    $arg2 = func_get_arg(1);
	    $flagsAr = str_split($arg2);
	    $showAr = array();
	    for($i=0;$i<$numargs;$i++){
	        switch($flagsAr[$i]){
	        case 's' :  $showAr[] = "'".func_get_arg($i+2)."'";
	        break;
	        case 'i' :  $showAr[] = func_get_arg($i+2);
	        break;
	        case 'd' :  $showAr[] = func_get_arg($i+2);
	        break;
	        case 'b' :  $showAr[] = "'".func_get_arg($i+2)."'";
	        break;
	        }
	    }
	    $query = func_get_arg(0);
	    $querysAr = str_split($query);
	    $lengthQuery = count($querysAr);
	    $j = 0;
	    $display = "";
	    for($i=0;$i<$lengthQuery;$i++){
	        if($querysAr[$i] === '?'){
	            $display .= $showAr[$j];
	            $j++;
	        }else{
	            $display .= $querysAr[$i];
	        }
	    }
	    if($j != $numVars){
	        $display = "Mismatch on Variables to Placeholders (?)";
	    }
	    return $display;
	}
	function execute($consulta = NULL){ // Ejecuta una consulta al servidor
		$actual = microtime(true);
		$this->sql = ($consulta) ? $consulta : $this->sql;
		$consulta = $this->query($this->sql);
		$report = array("error" => ($this->error ? true : false), "query" => $this->sql, "error_string" => $this->error);
		if($this->error)
			$this->log_error();
		//$GLOBALS['SQL_TIME'] = $GLOBALS['SQL_TIME']+((microtime(true)-$actual)/60);
		return $report;
	}
	function get_array($type = MYSQLI_ASSOC, $cache = true){ // Devuelve un array compatible con while
		global $consultas;
		if($cache && isset($consultas[md5($this->sql)])){
			$result = $consultas[md5($this->sql)]['result'];
			$consultas[md5($this->sql)]['times']++;
		}else{
			$actual = microtime(true);
			$consulta = $this->query($this->sql);
			if($this->error)
				$this->log_error();
			$result = ($consulta) ? $consulta->fetch_array($type) : false;
			$consultas[md5($this->sql)]['result'] = $result;
			$consultas[md5($this->sql)]['query'] = $this->sql;
			$consultas[md5($this->sql)]['times'] = 1;
		}
		return $result;
	}
	function get_full_array($type = MYSQLI_ASSOC, $cache = true){ // Devuelve un array con todos los resultados
		global $consultas;
		if($cache && isset($consultas[md5($this->sql)])){
			$array = $consultas[md5($this->sql)]['result'];
			$consultas[md5($this->sql)]['times']++;
		}else{
			$actual = microtime(true);
			$array = array();
			$consulta = $this->query($this->sql);
			if($this->error)
				$this->log_error();
			//$GLOBALS['SQL_TIME'] = $GLOBALS['SQL_TIME']+((microtime(true)-$actual)/60);
			if($consulta){
				while($linea = $consulta->fetch_array($type)){
					$array[] = $linea;
				}
			}else{
				$array = false;
			}
			$consultas[md5($this->sql)]['result'] = $array;
			$consultas[md5($this->sql)]['query'] = $this->sql;
			$consultas[md5($this->sql)]['times'] = 1;
		}
		return $array;
	}

}

?>
