<?php

declare(strict_types=1);

namespace Weather\Handler;

use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class MainFactory
{
    public function __invoke(ContainerInterface $container) : Main
    {
        return new Main($container->get(TemplateRendererInterface::class));
    }
}
