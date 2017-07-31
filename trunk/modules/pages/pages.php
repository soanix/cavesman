<?
class pages extends modules{
    function __construct(){
        $this->db = new db();
        $this->config = array(
            "active" => 1,
            "name" => "pages",
            "directory" => dirname(__FILE__)."/tpl",
            "title" => "Páginas",
            "description" => "Modulo que permite insertar páginas"
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
        $id = isset($_GET['section']) ? $_GET['section'] : '';
        $comercios = new comercios();
        $return['promocion'] = $this->getFromIdRowTable($id);
        $return['comercio'] = $comercios->getFromIdRowTable($return['promocion']['comercio_id']);
        return $return;
    }
    function get(){
        $promociones['all'] = $this->getList();
        $promociones['big'] = $this->getList(2);
        $promociones['small'] = $this->getList(1);
        $promociones['config'] = $this->config;
        return $promociones;
    }
    function getFromIdRowTable($page_id = 0){
        $ssql = "SELECT
                p.page_id,
                pl.title,
                pl.seo_title,
                pl.description
            FROM pages p
            INNER JOIN pages_lang pl ON p.page_id = pl.page_id
            WHERE p.page_id = ':page_id'
            AND lang_id = ':lang_id'";
        $this->db->create($ssql);
        $this->db->safe(":page_id", $page_id);
        $this->db->safe(":lang_id", $_SESSION['lang']);
        return $this->db->get_array();
    }
    function getList(){
        $ssql = "SELECT p.page_id, p.name, pl.seo_title, pl.title, pl.description FROM pages p
                INNER JOIN pages_lang pl ON p.page_id = pl.page_id
                WHERE lang_id = '1'";
        $this->db->create($ssql);
		$this->db->safe(":lang_id", $_SESSION['lang']);
        return $this->db->get_full_array();
    }
    function edit(){
        $page_id = isset($_POST['page_id']) ? $_POST['page_id'] : 0;
        $ssql = "SELECT
                p.page_id,
                p.name,
                p.section_footer

            FROM pages p
            WHERE p.page_id = ':page_id'";
        $this->db->create($ssql);
        $this->db->safe(":page_id", $page_id);
        $return = $this->db->get_array();
        $ssql = "SELECT
                pl.lang_id,
                pl.title,
                pl.seo_title,
                pl.description
            FROM pages p
            INNER JOIN pages_lang pl ON p.page_id = pl.page_id
            WHERE p.page_id = ':page_id'";
        $this->db->create($ssql);
        $this->db->safe(":page_id", $page_id);
        $array = $this->db->get_full_array();
        foreach($array as $line){
            foreach($line as $clave => $valor){
                $line[$clave] = $this->db->unsafe($valor);
            }
            $return['idiomas'][$line['lang_id']] = $line;
        }
        return $return;
    }
    function create($name = ''){
        $ssql = "INSERT INTO pages (name) VALUES (':name')";
        $this->db->create($ssql);
        $this->db->safe(":name", $name);
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
        $page_id = isset($_POST['page_id']) ? $_POST['page_id'] : 0;
        $name = isset($_POST['name']) ? $_POST['name'] : "";
        $section_footer = isset($_POST['section_footer']) ? $_POST['section_footer'] : "";
        $title = isset($_POST['title']) ? $_POST['title'] : "";
        $seo_title = isset($_POST['seo_title']) ? $_POST['seo_title'] : "";
        $description = isset($_POST['description']) ? $_POST['description'] : "";
        if(!$page_id)
            $page_id = $this->create($name);

        $ssql = "
            UPDATE pages
            SET
                name = ':name',
                section_footer = ':section_footer'
            WHERE page_id = ':page_id'
                ";
        $this->db->create($ssql);
        $this->db->safe(":page_id", $page_id);
        $this->db->safe(":section_footer", $section_footer);
        $this->db->safe(":name", $name);
        $this->db->execute();

        foreach($title as $lang_id => $value){
            $ssql = "
            INSERT INTO pages_lang (page_id, lang_id, title, seo_title, description) VALUES (':page_id', ':lang_id', ':title', ':seo_title', ':description')
            ON DUPLICATE KEY
                UPDATE
                    title = ':title',
                    seo_title = ':seo_title',
                    description = ':description'
                    ";
            $this->db->create($ssql);
            $this->db->safe(":page_id", $page_id);
            $this->db->safe(":lang_id", $lang_id);
            $this->db->safe(":title", $title[$lang_id]);
            $this->db->safe(":seo_title", $seo_title[$lang_id]);
            $this->db->safeLiteral(":description", $description[$lang_id]);
           $this->db->execute();
        }
        $page = $this->getFromIdRowTable($page_id);
        $this->smarty->assign("page", $page);
        $return["page_id"] = $page_id;
        $return["html"] = $this->smarty->fetch("page.tpl");
        return $return;
    }
}
?>
