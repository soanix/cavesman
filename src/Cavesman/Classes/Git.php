<?php

namespace Cavesman;

class Git {

  public static function version($params, $smarty) {
    exec('git describe --always',$version_mini_hash);
    exec('git rev-list HEAD | wc -l',$version_number);
    exec('git log -1',$line);
    $version['short'] = "v1.".trim($version_number[0]).".".$version_mini_hash[0];
    $version['full'] = "v1.".trim($version_number[0]).".$version_mini_hash[0] (".str_replace('commit ','',$line[0]).")";
    return $version;
  }

}
