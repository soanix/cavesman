<?php

namespace Cavesman\Model;

use Cavesman\Db\Doctrine\Model\Model;

class Module extends Base
{
    public string $name;
    public string $version;
    public bool $active;
    public string $path;

}
