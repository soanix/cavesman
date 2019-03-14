<?php

if(file_exists(dirname(__FILE__)."/../app/Config/settings.dev.inc.php"))
   include_once dirname(__FILE__)."/../app/Config/settings.dev.inc.php";
elseif(file_exists(dirname(__FILE__)."/../app/Config/settings.inc.php"))
   include_once dirname(__FILE__)."/../app/Config/settings.inc.php";
require_once _ROOT_."/Config/config.inc.php";

use Doctrine\ORM\Tools\Console\ConsoleRunner;

$entityManager = Cavesman\DB::getManager();
return ConsoleRunner::createHelperSet($entityManager);

?>
