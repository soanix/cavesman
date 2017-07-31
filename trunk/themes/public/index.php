<?
$permisos = isLogged ? $this->users->getPermisos() : array();
$this->smarty->assign("permisos", $permisos);
$this->smarty->assign("is_logged", isLogged);
$this->smarty->display("index.tpl");
?>
