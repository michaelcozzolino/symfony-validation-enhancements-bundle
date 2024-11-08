<?php declare(strict_types=1);

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services->defaults()
             ->autowire()
             ->autoconfigure()
             ->private();

    $services->load('MichaelCozzolino\SymfonyValidationEnhancementsBundle\\', '../src/{Validator}/*');
};
