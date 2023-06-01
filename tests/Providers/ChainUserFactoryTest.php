<?php

declare(strict_types=1);

namespace WayOfDev\Auth\Tests\Providers;

use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\MockObject\MockObject;
use WayOfDev\Auth\Contracts\Authenticatable;
use WayOfDev\Auth\Contracts\UserFactory;
use WayOfDev\Auth\Exceptions\UserNotCreatableException;
use WayOfDev\Auth\Providers\ChainUserFactory;
use WayOfDev\Auth\Tests\TestCase;

class ChainUserFactoryTest extends TestCase
{
    private ChainUserFactory $chainUserFactory;

    private MockObject $factoryOne;

    private MockObject $factoryTwo;

    private MockObject $user;

    private string $identifier = 'testIdentifier';

    /**
     * @throws Exception
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->user = $this->createMock(Authenticatable::class);
        $this->factoryOne = $this->createMock(UserFactory::class);
        $this->factoryTwo = $this->createMock(UserFactory::class);

        $this->chainUserFactory = new ChainUserFactory([
            $this->factoryOne,
            $this->factoryTwo,
        ]);
    }

    /**
     * @test
     */
    public function supports(): void
    {
        $this->factoryOne
            ->expects($this::once())
            ->method('supports')
            ->with($this->identifier)
            ->willReturn(false);

        $this->factoryTwo
            ->expects($this::once())
            ->method('supports')
            ->with($this->identifier)
            ->willReturn(true);

        $this::assertTrue($this->chainUserFactory->supports($this->identifier));
    }

    /**
     * @test
     */
    public function does_not_support(): void
    {
        $this->factoryOne
            ->expects($this::once())
            ->method('supports')
            ->with($this->identifier)
            ->willReturn(false);

        $this->factoryTwo
            ->expects($this::once())
            ->method('supports')
            ->with($this->identifier)
            ->willReturn(false);

        $this::assertFalse($this->chainUserFactory->supports($this->identifier));
    }

    /**
     * @test
     */
    public function create_user(): void
    {
        $this->factoryOne
            ->expects($this::once())
            ->method('supports')
            ->with($this->identifier)
            ->willReturn(false);

        $this->factoryOne
            ->expects($this::never())
            ->method('createUser');

        $this->factoryTwo
            ->expects($this::once())
            ->method('supports')
            ->with($this->identifier)
            ->willReturn(true);

        $this->factoryTwo
            ->expects($this::once())
            ->method('createUser')
            ->with($this->identifier)
            ->willReturn($this->user);

        $user = $this->chainUserFactory->createUser($this->identifier);

        $this::assertEquals($this->user, $user);
    }

    /**
     * @test
     */
    public function create_user_throws_exception(): void
    {
        $this->factoryOne->method('supports')->willReturn(false);

        $this->expectException(UserNotCreatableException::class);
        $this->chainUserFactory->createUser($this->identifier);
    }
}
