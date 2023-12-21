<?php

use Afeefa\Component\Package\Package\Package;
use Symfony\Component\Filesystem\Path;

return [
    Package::composer()
        ->path(Path::join(getcwd(), 'apineu-server'))
        ->split('git@github.com:neuedev/apineu-server.git'),

    Package::npm()
        ->path(Path::join(getcwd(), 'apineu-client'))
        ->split('git@github.com:neuedev/apineu-client.git')
];
