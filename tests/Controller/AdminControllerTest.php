<?php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\CoversNothing;

class AdminControllerTest extends WebTestCase
{
    #[Test]
    #[CoversNothing] 
    public function testAdminPageIsAccessible()
    {
        $client = static::createClient();
        $entityManager = $client->getContainer()->get(EntityManagerInterface::class);
        
        $userRepository = $entityManager->getRepository(User::class);
        $adminUser = $userRepository->findOneBy(['email' => 'admin@test.com']);

        if (!$adminUser) {
            $adminUser = new User();
            $adminUser->setEmail('admin@test.com');
            $adminUser->setRoles(['ROLE_ADMIN']);
            $adminUser->setName('Admin');
            $adminUser->setLastname('Test');

            $passwordHasher = $client->getContainer()->get(UserPasswordHasherInterface::class);
            $adminUser->setPassword($passwordHasher->hashPassword($adminUser, 'password'));

            $entityManager->persist($adminUser);
            $entityManager->flush();
        }

        $client->loginUser($adminUser);
        $crawler = $client->request('GET', '/admin');

        $this->assertResponseStatusCodeSame(200);

        $this->assertMatchesRegularExpression('/Bienvenue sur le panneau d\'administration/', $client->getResponse()->getContent());
    }
}
