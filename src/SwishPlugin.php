<?php

declare(strict_types=1);

namespace Commerz\SwishPlugin;

use Sylius\Bundle\CoreBundle\Application\SyliusPluginTrait;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class SwishPlugin extends Bundle
{
    use SyliusPluginTrait;
}