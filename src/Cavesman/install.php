<?php
$install = false;
if(!file_exists(_APP_."/config/main.json"))
    $install = true;
if(!is_dir(_SRC_))
    mkdir(_SRC_);
if(!is_dir(_APP_))
    mkdir(_APP_);
if(!is_dir(_WEB_))
    mkdir(_WEB_);
if(!is_dir(_SRC_."/themes"))
    mkdir(_SRC_."/themes");
if(!is_dir(_SRC_."/themes/public"))
    mkdir(_SRC_."/themes/public");
if(!is_dir(_SRC_."/themes/public/tpl"))
    mkdir(_SRC_."/themes/public/tpl");
if(!is_dir(_APP_."/cache"))
    mkdir(_APP_."/cache");
if(!is_dir(_APP_."/config"))
    mkdir(_APP_."/config");
if(!is_dir(_SRC_."/modules"))
    mkdir(_SRC_."/modules");
if(!file_exists(_WEB_."/.htaccess")){
    $fp = fopen(_WEB_."/.htaccess", "w+");
    $htaccess = 'RewriteEngine On'.PHP_EOL
    .'RewriteCond %{REQUEST_FILENAME} !-f'.PHP_EOL
    .'RewriteCond %{REQUEST_FILENAME} !-d'.PHP_EOL
    .'RewriteRule . index.php [L]';
    fwrite($fp, $htaccess);
    fclose($fp);
}
if(!file_exists(_APP_."/config/main.json")){
    $fp = fopen(_APP_."/config/main.json", "w+");
    fwrite($fp, json_encode(["env" => "dev"], JSON_PRETTY_PRINT));
    fclose($fp);
}
if(!file_exists(_APP_."/config/params.json")){
    $fp = fopen(_APP_."/config/params.json", "w+");
    fwrite($fp, json_encode(["debug" => true, "theme" => "public"], JSON_PRETTY_PRINT));
    fclose($fp);
}
if(!file_exists(_APP_."/config/params.dev.json")){
    $fp = fopen(_APP_."/config/params.dev.json", "w+");
    fwrite($fp, json_encode([], JSON_PRETTY_PRINT));
    fclose($fp);
}
if(!file_exists(_THEMES_."/public/index.php")){
    $fp = fopen(_THEMES_."/public/index.php", "w+");
    $indexphp = '<?php'.PHP_EOL
    .'require _THEMES_."/"._THEME_NAME_."/routes.php";'.PHP_EOL
    .'Cavesman\Cavesman::$router->run(function(){'.PHP_EOL
    .'    Cavesman\Cavesman::$smarty->display("index.tpl");'.PHP_EOL
    .'});';
    fwrite($fp, $indexphp);
    fclose($fp);
}
if(!file_exists(_THEMES_."/public/routes.php")){
    $fp = fopen(_THEMES_."/public/routes.php", "w+");
    $routesphp = '<?php'.PHP_EOL;
    fwrite($fp, $routesphp);
    fclose($fp);
}
if(!file_exists(_APP_."/routes.php")){
    $fp = fopen(_APP_."/routes.php", "w+");
    $routesphp = '<?php'.PHP_EOL
            .'Cavesman\Cavesman::$router->get("/", function(){'.PHP_EOL
            .'  //Something to /'.PHP_EOL
            .'});';
    fwrite($fp, $routesphp);
    fclose($fp);
}

if(!file_exists(_WEB_."/index.php")){
    $fp = fopen(_WEB_."/index.php", "w+");
    $routesphp = '<?php'.PHP_EOL
        .'require \'../vendor/autoload.php\';'.PHP_EOL
        .'Cavesman\Cavesman::run();'.PHP_EOL
    .'?>';
    fwrite($fp, $routesphp);
    fclose($fp);
}

if(!file_exists(_THEMES_."/public/tpl/index.tpl")){
    $fp = fopen(_THEMES_."/public/tpl/index.tpl", "w+");
    $indextpl = '<link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">'.PHP_EOL
    .'<div class="container">'.PHP_EOL
    .'	<div class="row">'.PHP_EOL
    .'		<div class="col-xs-12 text-center">'.PHP_EOL
    .'			<img src="https://github.com/soanix/cavesman/raw/master/cavesman.jpg?raw=true">'.PHP_EOL
    .'			<h1>Cave\'s Man</h1>'.PHP_EOL
    .'			<p>PHP modular framework</p>'.PHP_EOL
    .'			<p>Version: {git_version}</p>'.PHP_EOL
    .'		</div>'.PHP_EOL
    .'	</div>'.PHP_EOL
    .'</div>';
    fwrite($fp, $indextpl);
    fclose($fp);
}
if(!is_dir(_THEMES_))
    throw new \Exception("Imposible crear el directorio de temas", 1);
if(!is_dir(_THEMES_."/public"))
    throw new \Exception("Imposible crear el directorio de temas default", 1);
if(!is_dir(_THEMES_."/public/tpl"))
    throw new \Exception("Imposible crear el directorio de temas tpl", 1);
if(!is_dir(_APP_."/cache"))
    throw new \Exception("Imposible crear el directorio cache", 1);
if(!is_dir(_APP_."/config"))
    throw new \Exception("Imposible crear el directorio de configuracion", 1);
if(!is_dir(_SRC_."/modules"))
    throw new \Exception("Imposible crear el directorio de modulos", 1);

if($install){
    ?>
    <h1>Instalación correcta</h1>
    <p>Todos los archivos y directorios creados</p>
    <a href="/">Continuar</a>
    <?php

    exit();
};
