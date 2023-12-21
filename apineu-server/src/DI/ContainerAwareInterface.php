<?php

namespace Neuedev\Apineu\DI;

interface ContainerAwareInterface
{
    public function created(): void;

    public function container(Container $container): void;
}
