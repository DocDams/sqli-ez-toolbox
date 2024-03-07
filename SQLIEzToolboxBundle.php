<?php

namespace SQLI\EzToolboxBundle;

use SQLI\EzToolboxBundle\DependencyInjection\Compiler\ParameterHandlerTagCompilerPass;
use SQLI\EzToolboxBundle\DependencyInjection\PolicyProvider\SQLIEzToolboxPolicyProvider;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class SQLIEzToolboxBundle extends Bundle
{
    /**
     * Builds the bundle.
     *
     * It is only ever called once when the cache is empty.
     *
     * @param ContainerBuilder $container A ContainerBuilder instance
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $eZExtension = $container->getExtension('ibexa');
        /** @phpstan-ignore-next-line */
        $eZExtension->addPolicyProvider(new SQLIEzToolboxPolicyProvider());
        $container->addCompilerPass(new ParameterHandlerTagCompilerPass());
    }
}
