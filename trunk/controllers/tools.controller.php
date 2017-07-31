<?
class tools extends display{
    function __construct(){
        $this->db = new db();
    }
    function getRegions(){
        $ssql = "SELECT region_id, name FROM regiones WHERE pais_id = '1'";
        $this->db->create($ssql);
        return $this->db->get_full_array();
    }
	function getRegionsByPais(){
        $ssql = "SELECT region_id, name FROM regiones WHERE pais_id = ':pais_id' ORDER BY name ASC";
        $this->db->create($ssql);
		$this->db->safe(":pais_id", $this->p("pais_id"));
        return $this->db->get_full_array();
    }
    function getProvinciasByRegion(){
        $ssql = "SELECT provincia_id, name FROM provincias WHERE region_id = ':region_id' ORDER BY name ASC";
        $this->db->create($ssql);
        $this->db->safe(":region_id", $this->p("region_id"));
        return $this->db->get_full_array();
    }
    function getComarcasByProvincia(){
        $ssql = "SELECT comarca_id, name FROM comarcas WHERE provincia_id = ':provincia_id' ORDER BY name ASC";
        $this->db->create($ssql);
        $this->db->safe(":provincia_id", $this->p("provincia_id"));
        return $this->db->get_full_array();
    }
    function getLocalidadesByComarca(){
        $ssql = "SELECT localidad_id, name FROM localidades WHERE comarca_id = ':comarca_id' ORDER BY name ASC";
        $this->db->create($ssql);
        $this->db->safe(":comarca_id", $this->p("comarca_id"));
        return $this->db->get_full_array();
    }
    function getParentsByLocalidad($localidad_id = ''){
        $ssql = "SELECT l.localidad_id, r.region_id, p.provincia_id, c.comarca_id
                FROM localidades l
                INNER JOIN comarcas c ON c.comarca_id = l.comarca_id
                INNER JOIN provincias p ON p.provincia_id = c.provincia_id
                INNER JOIN regiones r ON r.region_id = p.region_id
                WHERE l.localidad_id = ':localidad_id'";
        $this->db->create($ssql);
        $this->db->safe(":localidad_id", $this->p("localidad_id", $localidad_id));
        return $this->db->get_array();
    }
	function getParentsByProvincia($provincia_id = ''){
        $ssql = "SELECT p.provincia_id, c.comarca_id
                FROM provincias p
                INNER JOIN regiones r ON r.region_id = p.region_id
                WHERE p.provincia_id = ':provincia_id'";
        $this->db->create($ssql);
        $this->db->safe(":provincia_id", $this->p("provincia_id", $provincia_id));
        return $this->db->get_array();
    }
	function addLocation(){
		$section = isset($_POST['section']) ? $_POST['section'] : '';
		$valor = isset($_POST['value']) ? $_POST['value'] : '';
		$parent_id = isset($_POST['parent']) ? $_POST['parent'] : '';
		if($section == 'region'){
			$ssql = "INSERT INTO regiones (pais_id, name) VALUES (':parent_id', ':valor')";
	        $this->db->create($ssql);
			$this->db->safe(":parent_id", $parent_id);
			$this->db->safe(":valor", $valor);
	        $this->db->execute();
		}elseif($section == 'provincia'){
			$ssql = "INSERT INTO provincias (region_id, name) VALUES (':parent_id', ':valor')";
	        $this->db->create($ssql);
			$this->db->safe(":parent_id", $parent_id);
			$this->db->safe(":valor", $valor);
	        $this->db->execute();
		}elseif($section == 'comarca'){
			$ssql = "INSERT INTO comarcas (provincia_id, name) VALUES (':parent_id', ':valor')";
	        $this->db->create($ssql);
			$this->db->safe(":parent_id", $parent_id);
			$this->db->safe(":valor", $valor);
	        $this->db->execute();
		}elseif($section == 'localidad'){
			$ssql = "INSERT INTO localidades (comarca_id, name) VALUES (':parent_id', ':valor')";
	        $this->db->create($ssql);
			$this->db->safe(":parent_id", $parent_id);
			$this->db->safe(":valor", $valor);
	        $this->db->execute();
		}
		$return['report']['correct'] = 'Todo correcto';
		return $return;
	}
}
