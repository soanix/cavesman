<?
class translates extends modules{
    function __construct(){
        $this->db = new db();
		$this->lang = new lang();
        $this->config = array(
            "active" => 1,
            "name" => "translates",
            "directory" => dirname(__FILE__)."/tpl",
            "title" => "Translates",
            "description" => "Modulo que permite traducir textos"
        );
		$lang = isset($_GET['language']) ? $_GET['language'] : '';
		$this->lang_id = $this->get_id_from_iso($lang);
        self::install();
        parent::loadSmarty();
    }
	function install(){

	}
	function get(){
		$lang = isset($_GET['language']) ? $_GET['language'] : '';
		$translates["all"] = $this->getTranslates($this->get_id_from_iso($lang));
		$translates["lang_id"] = $this->lang_id;
		$translates["iso"] = $lang;
		return $translates;
	}
	function getTranslates($lang_id = 1){
		$ssql = "SELECT
					t.translate_id,
					(SELECT translate FROM translates_lang WHERE translate_id = t.translate_id AND lang_id = 1) as string,
					tl.translate,
					tl.traducido
				FROM translates t
				INNER JOIN translates_lang tl ON t.translate_id = tl.translate_id
				WHERE lang_id = ':lang'
				ORDER BY translate_id ASC";

		$this->db->create($ssql);
		$this->db->safe(":lang", $lang_id);
		$array = $this->db->get_full_array();
		return $array;
	}
	function change_traducido(){
		$lang_id = isset($_POST['tlang']) ? $_POST['tlang'] : 0;
		$translate_id = isset($_POST['tid']) ? $_POST['tid'] : 0;
		$traducido = isset($_POST['traducido']) ? $_POST['traducido'] : 0;
		$ssql = "
					UPDATE
						translates_lang
					SET
						traducido = ':traducido'
					WHERE
						lang_id = ':lang_id'
					AND
						translate_id = ':translate_id'";
		$this->db->create($ssql);
		$this->db->safe(":traducido", $traducido);
		$this->db->safe(":translate_id", $translate_id);
		$this->db->safe(":lang_id", $lang_id);

		$this->db->execute();
		$return['traducido'] = $traducido;
		return $return;
	}
	function translate(){
		$lang_id = isset($_POST['tlang']) ? $_POST['tlang'] : 0;
		$translate_id = isset($_POST['tid']) ? $_POST['tid'] : 0;
		$translate = isset($_POST['translate']) ? $_POST['translate'] : '';
		$ssql = "
					UPDATE
						translates_lang
					SET
						translate = ':translate_text'
					WHERE
						lang_id = ':lang_id'
					AND
						translate_id = ':translate_id'";
		$this->db->create($ssql);
		$this->db->safeLiteral(":translate_text", $translate);
		$this->db->safe(":translate_id", $translate_id);
		$this->db->safe(":lang_id", $lang_id);
		$this->db->execute();
		return true;
	}

	function get_id_from_iso($iso){
		$ssql = "SELECT lang_id
				FROM lang
				WHERE iso = ':iso'
				AND active = '1'";
		$this->db->create($ssql);
		$this->db->safe(":iso", $iso);
		$linea = $this->db->get_array();
		if($linea)
			return $linea['lang_id'];
		return false;
	}
}
