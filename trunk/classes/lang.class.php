<?
class lang {
    function __construct(){
        $this->db = new db();
		$iso = isset($_GET['lang']) ? $_GET['lang'] : '';
		if($iso){
			$lang = $iso ? $this->getIdFromIso($iso) : false;
			if($lang)
				$_SESSION['lang'] = $lang;
		}elseif(!isset($_SESSION['lang'])){
			$iso = explode(";", $_SERVER['HTTP_ACCEPT_LANGUAGE']);
			$iso = explode(",", $iso[0]);
			$lang = isset($iso[1]) ? $this->getIdFromIso($iso[1]) : false;
			if($lang)
				$_SESSION['lang'] = $lang;
			else
            	$_SESSION['lang'] = $this->getDefaultLanguage();
        }
		$iso = $this->get_iso($_SESSION['lang']);
		if(!$iso){
			$iso = $this->get_iso($this->getDefaultLanguage());
		}
		if($_SERVER['REQUEST_URI'] == _PATH_){
			header("Location: "._PATH_.$iso);
			exit();
		}
		$this->t = $this->get_translates();
		$this->iso = $iso;
		setlocale(LC_ALL, $this->iso.'_ES.utf8');
    }
    function get_langs(){
        $this->db->create("SELECT * FROM lang");
        return $this->db->get_full_array();
    }
	function get_iso($lang_id){
		include_once(_ROOT_.'/classes/db.class.php');
        $db = new db();
		$ssql = "SELECT iso
				FROM lang
				WHERE lang_id = ':lang_id'
				AND active = '1'";
		$db->create($ssql);
		$db->safe(":lang_id", $lang_id);
		$linea = $db->get_array();
		return $linea['iso'];
	}
	function getIdFromIso($iso){
		include_once(_ROOT_.'/classes/db.class.php');
        $db = new db();
		$ssql = "SELECT lang_id
				FROM lang
				WHERE iso = ':iso'
				AND active = '1'";
		$db->create($ssql);
		$db->safe(":iso", $iso);
		$linea = $db->get_array();
		return $linea['lang_id'];
	}
    function getDefaultLanguage($lang = false){
        $this->db->create("SELECT lang_id FROM lang WHERE lang = ':lang'");
        $this->db->safe(":lang", $lang);
        $lang = $this->db->get_array();
        if(!$lang){
            $this->db->create("SELECT lang_id FROM lang WHERE iso = ':lang'");
            $this->db->safe(":lang", DEFAULT_LANG);
            $lang = $this->db->get_array();
        }
        return $lang['lang_id'];
    }

	function get_translates($idioma = FALSE){
			$idioma = $idioma ? $idioma : $_SESSION['lang'];
			$ssql = "SELECT t.string, tl.translate
					FROM translates t
					INNER JOIN translates_lang tl ON t.translate_id = tl.translate_id
					WHERE tl.lang_id = ':idioma'";
			$this->db->create($ssql);
			$this->db->safe(":idioma", $idioma);
			$result = $this->db->get_full_array();
			$array = array();
			foreach($result as $key => $value){
				$array[$value['string']] = $value['translate'];
			}
			return $array;
	}
	function translate_add($string){
		$ssql = "INSERT INTO translates
				(string, md5)
				VALUES
				(':string', MD5(':string'))";
		$this->db->create($ssql);
		$this->db->safeLiteral(":string", $string);
		$this->db->execute();
	}
	function l($string, $binds = array()){
		$string = str_replace("  ", " ", preg_replace('/\s+/', ' ', $string));
		if(!isset($this->t[$string])){
			$this->translate_add($string);
			$binded = $string;
			foreach($binds as $key => $value){
				$binded = str_replace($key, $value, $binded);
			}
			$this->t[$string] = $binded;
		} else {
			$binded = $this->t[$string];
			foreach($binds as $key => $value){
				$binded = str_replace($key, $value, $binded);
			}
		}
		return nl2br($binded);
	}
}
