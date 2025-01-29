<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(User::class)]
class UserTest extends TestCase
{
    public function testFullNameGeneration(): void
    {
        // Arrange
        $user = new User();
        $user->setName('John');
        $user->setLastname('Doe');

        // Act
        $fullName = $user->getName() . ' ' . $user->getLastname();

        // Assert
        $this->assertEquals('John Doe', $fullName, 'Le nom complet n’est pas généré correctement.');
    }

    public function testDefaultRoleIsUser(): void
    {
        // Arrange
        $user = new User();

        // Act
        $roles = $user->getRoles();

        // Assert
        $this->assertContains('ROLE_USER', $roles, 'Le rôle par défaut devrait être ROLE_USER.');
    }
}
