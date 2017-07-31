<?
class Display {
    function __construct(){
        $this->users = new users();
        $this->modules = new modules();
        $this->lang = new lang();
        $this->db = new db();

    }
    function init(){
        $this->smarty = new SmartyCustom();
        $this->smarty->template_dir =  _THEMES_."/"._THEME_NAME_."/tpl";
    }
    function p($value = '', $default = ''){
        return isset($_POST[$value]) ? $_POST[$value] : $default;
    }
    function g($value = '', $default = ''){
        return isset($_GET[$value]) ? $_GET[$value] : $default;
    }
    function startTheme(){
		self::init();
		foreach($this->modules->list as $module){
			$name = $module['name'];
			if($module['active'])
			    $this->smarty->assign($name, $this->modules->$name->get());
		}
    }
    function theme(){
        $page = $this->g("controller") ? $this->g("controller") : "index";
        $this->smarty->assign("page", $page);
        $lang_list = $this->lang->get_langs();
        $this->smarty->assign("lang_list", $lang_list);
		$this->smarty->assign("iso", $this->lang->iso);
		$this->smarty->assign("base", _PATH_);
        $this->smarty->assign("css", _TEMPLATES_."/"._THEME_NAME_."/css");
        $this->smarty->assign("js", _TEMPLATES_."/"._THEME_NAME_."/js");
        $this->smarty->assign("img", _TEMPLATES_."/"._THEME_NAME_."/img");
		if(file_exists(_THEMES_."/"._THEME_NAME_."/tpl/".$page.".tpl"))
        	include_once(_THEMES_."/"._THEME_NAME_."/index.php");
    }
    function getPageFooter($page = false){
        $ssql = "SELECT p.section_footer, pl.seo_title, pl.title
                FROM pages p
                INNER JOIN pages_lang pl ON p.page_id = pl.page_id
                WHERE lang_id = ':lang_id'";
        $this->db->create($ssql);
        $this->db->safe(":lang_id", $_SESSION['lang']);
        $this->db->safe(":name", $page);
        $array = $this->db->get_full_array();
        $paginas = array("empresa" => array(), "legal" => array());
        foreach($array as $key => $page){
            $section = $page['section_footer'] == "1" ? "empresa" : ($page['section_footer'] == "2" ? "legal" : "none");
            $paginas[$section][] = $page;
        }
        return $paginas;
    }
    function getPage($page = false){
        $ssql = "SELECT p.page_id, p.name, pl.title, pl.description
                FROM pages p
                INNER JOIN pages_lang pl ON p.page_id = pl.page_id
                WHERE lang_id = ':lang_id'
                AND pl.seo_title = ':name'";
        $this->db->create($ssql);
        $this->db->safe(":lang_id", $_SESSION['lang']);
        $this->db->safe(":name", $page);
        return $this->db->get_array();
    }
    function verifyPage($page = false){
        $ssql = "SELECT p.page_id FROM pages p
                INNER JOIN pages_lang pl ON p.page_id = pl.page_id
                WHERE lang_id = ':lang_id'
                AND pl.seo_title = ':name'";
        $this->db->create($ssql);
        $this->db->safe(":lang_id", $_SESSION['lang']);
        $this->db->safe(":name", $page);
        return $this->db->get_array();
    }
	function l($string, $binds = array()){
		return $this->lang->l($string, $binds);
	}
}
