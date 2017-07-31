<?
class search extends display{
    function __construct(){
        $this->db = new db();
        $this->config = array(
            "smartyList" => "comercios,promociones"
        );
		$this->find = '';
        parent::__construct();
    }
    function init(){
        return $this->getResults();
    }
    function getResults(){
		$string_search = $this->g("q");
		$stringArray = explode(" ", $string_search);
		foreach($stringArray as $key1 => $word1) {
			if(in_array($word1, array(
				"A",
				"ANTE",
				"BAJO",
				"CABE",
				"CON",
				"CONTRA",
				"DE",
				"DESDE",
				"EN",
				"ENTRE",
				"HACIA",
				"HASTA",
				"PARA",
				"POR",
				"SEGÚN",
				"SIN",
				"SO",
				"SOBRE",
				"TRAS",
				"cerca", "lejos", "quiero", "busco", "necesito", "encontrar"
			))){
				unset($stringArray[$key1]);
			}
		}
		if($this->find == 'comercios'){
	        $ssql = "
	            SELECT
	                c.seo_name,
	                c.comercio_id,
	                cl.title as comercio,
	                cl.description_short,
	                cl.description,
	                l.name as localidad,
	                co.name as comarca,
	                p.name as provincia
	            FROM comercios c
				INNER JOIN comercios_lang cl ON c.comercio_id = cl.comercio_id
	            INNER JOIN localidades l ON l.localidad_id = c.localidad_id
	            INNER JOIN comarcas co ON co.comarca_id = l.comarca_id
	            INNER JOIN provincias p ON p.provincia_id = co.provincia_id
	            WHERE cl.lang_id = ':lang_id'";
	        foreach($stringArray as $key => $word){
	            $ssql .= "
	                AND (
	                    cl.title LIKE '%:".$key."string%'
	                    OR
	                    cl.description_short LIKE '%:".$key."string%'
	                    OR
	                    cl.description LIKE '%:".$key."string%'
	                    OR
	                    l.name LIKE '%:".$key."string%'
	                    OR
	                    p.name LIKE '%:".$key."string%'
	                    OR
	                    c.name LIKE '%:".$key."string%'
	                )";
	        }
			$ssql .= "LIMIT 30";

	        $this->db->create($ssql);
			$this->db->safe(":lang_id", $_SESSION['lang']);
	        foreach($stringArray as $key => $word)
	            $this->db->safe(":".$key."string", $word);
	        $this->db->safe(":lang_id", 1);
			$array['all'] = $this->db->get_full_array();
			foreach($array['all'] as $key => $comercio)
				$array['all'][$key]['caracteristicas'] = $this->modules->comercios->getCaracteristicas($comercio['comercio_id'], false);

	        $array['config'] = $this->modules->comercios->config;
		}elseif($this->find == 'promociones'){
			$ssql = "SELECT
	                p.promocion_id,
	                p.discount,
	                p.price,
	                p.final_price,
	                p.show_price,
	                p.show_discount,
	                pl.title,
	                pl.description,
					pl.description_short,
					pl.description_extended,
	                c.name as comercio,
	                l.name as localidad
	            FROM promociones p
	            INNER JOIN promociones_lang pl ON p.promocion_id = pl.promocion_id
	            LEFT JOIN comercios c ON c.comercio_id = p.comercio_id
				INNER JOIN localidades l ON l.localidad_id = c.localidad_id
	            INNER JOIN comarcas co ON co.comarca_id = l.comarca_id
	            INNER JOIN provincias pr ON pr.provincia_id = co.provincia_id
	            WHERE pl.lang_id = '1'
				AND p.type_id = ':type'
				AND ";
			foreach($stringArray as $key => $word){
	            if($key > 0)
	                $ssql .= " AND ";
	            $ssql .= "
	                (
	                    pl.title LIKE '%:".$key."string%'
	                    OR
	                    pl.description_short LIKE '%:".$key."string%'
	                    OR
	                    pl.description LIKE '%:".$key."string%'
	                    OR
	                    l.name LIKE '%:".$key."string%'
	                    OR
	                    pr.name LIKE '%:".$key."string%'
	                    OR
	                    c.name LIKE '%:".$key."string%'
	                )";
	        }
			$ssql .= "LIMIT 30";
			$this->db->create($ssql);
	        foreach($stringArray as $key => $word)
	            $this->db->safe(":".$key."string", $word);
	        $this->db->safe(":lang_id", 1);
			$this->db->safe(":type", 2);

	        $array['big'] = $this->db->get_full_array();
			$this->db->create($ssql);
	        foreach($stringArray as $key => $word)
	            $this->db->safe(":".$key."string", $word);
	        $this->db->safe(":lang_id", 1);
			$this->db->safe(":type", 1);

	        $array['small'] = $this->db->get_full_array();
			$array['config'] = $this->modules->promociones->config;
		}
		$array['isSearch'] = true;
        return $array;
    }
}
