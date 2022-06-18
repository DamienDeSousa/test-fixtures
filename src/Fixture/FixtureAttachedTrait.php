<?php

/**
 * File that defines the Fixture attached trait.
 * This trait is used to automatically load fixtures associated to the test.
 *
 * @author Damien DE SOUSA <dades@gmail.com>
 * @copyright 2021 Damien DE SOUSA
 */

declare(strict_types=1);

namespace Dades\TestFixtures\Fixture;

use Doctrine\Persistence\ManagerRegistry;
use LogicException;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

trait FixtureAttachedTrait
{
    use FixtureLoaderTrait;

    protected function setUp(): void
    {
        if (!$this instanceof KernelTestCase) {
            throw new LogicException(
                'The "FixtureAttachedTrait" should only be used on objects extending the '
                . 'symfony/framework-bundle KernelTestCase.'
            );
        }

        self::bootKernel();

        if (!self::$container->has(ManagerRegistry::class)) {
            throw new LogicException(
                'No Doctrine ManagerRegistry service has been found in the service container. Please provide'
                . 'an implementation.'
            );
        }

        /** @var ManagerRegistry $registry */
        $registry = self::$container->get(ManagerRegistry::class);
        $fixtureName = static::getFixtureNameForTestCase(get_class($this));
        $this->loadFixture(
            $registry->getManager(),
            new $fixtureName()
        );
    }

    private static function getFixtureNameForTestCase(string $testCaseName): string
    {
        return $testCaseName . 'Fixture';
    }
}
