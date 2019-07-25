<?php

namespace Soanix\Cavesman;

class Cavesman {
  public function run($env = 'dev') {
    require_once dirname(__FILE__) . '/Config/config.inc.php';
    require_once _ROOT_ . '/install.php';
    require_once _ROOT_ . '/controller.php';
  }
}
?>
