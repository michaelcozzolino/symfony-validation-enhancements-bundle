<?php declare(strict_types=1);

namespace MichaelCozzolino\SymfonyValidationEnhancementsBundle;

use MichaelCozzolino\SymfonyValidationEnhancementsBundle\DependencyInjection\MichaelCozzolinoSymfonyValidationEnhancementsBundleExtension;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;
use Symfony\Component\HttpKernel\Bundle\AbstractBundle;

class SymfonyValidationEnhancementsBundle extends AbstractBundle
{
    public function getContainerExtension(): ?ExtensionInterface
    {
        return new MichaelCozzolinoSymfonyValidationEnhancementsBundleExtension();
    }
}
