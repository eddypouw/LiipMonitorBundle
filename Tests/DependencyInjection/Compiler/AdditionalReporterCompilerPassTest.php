<?php

namespace Liip\MonitorBundle\Tests\DependencyInjection\Compiler;

use Liip\MonitorBundle\DependencyInjection\Compiler\AdditionalReporterCompilerPass;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractCompilerPassTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
class AdditionalReporterCompilerPassTest extends AbstractCompilerPassTestCase
{
    public function testProcess()
    {
        $runner = new Definition();
        $this->setDefinition('liip_monitor.runner', $runner);

        $reporter = new Definition();
        $reporter->addTag('liip_monitor.additional_reporter', array('alias' => 'foo'));
        $this->setDefinition('foo_reporter', $reporter);

        $this->compile();

        $this->assertContainerBuilderHasServiceDefinitionWithMethodCall(
            'liip_monitor.runner',
            'addAdditionalReporter',
            array(
                'foo',
                new Reference('foo_reporter')
            )
        );
    }

    /**
     * @expectedException \Symfony\Component\DependencyInjection\Exception\InvalidArgumentException
     */
    public function testProcessWithoutNameAttribute()
    {
        $runner = new Definition();
        $this->setDefinition('liip_monitor.runner', $runner);

        $reporter = new Definition();
        $reporter->addTag('liip_monitor.additional_reporter');
        $this->setDefinition('foo_reporter', $reporter);

        $this->compile();
    }

    protected function registerCompilerPass(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AdditionalReporterCompilerPass());
    }
}
