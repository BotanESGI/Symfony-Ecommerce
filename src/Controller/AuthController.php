<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\ResetPasswordType;
use App\Form\RegistrationType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AuthController extends AbstractController
{
    private UserPasswordHasherInterface $passwordHasher;
    private EntityManagerInterface $entityManager;

    public function __construct(UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager)
    {
        $this->passwordHasher = $passwordHasher;
        $this->entityManager = $entityManager;
    }

    #[Route(path: '/login', name: 'login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        $error = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('auth/login.html.twig', [
            'last_username' => $lastUsername,
            'error' => $error,
        ]);
    }

    #[Route(path: '/logout', name: 'logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route('/forgot-password', name: 'app_forgot_password')]
    public function forgotPassword(Request $request, UserRepository $userRepository, MailerInterface $mailer): Response
    {
        if ($request->isMethod('POST')) {
            $email = $request->request->get('email');
            $user = $userRepository->findOneBy(['email' => $email]);

            if ($user) {
                $resetToken = bin2hex(random_bytes(32));
                $user->setResetToken($resetToken);
                $this->entityManager->flush();

                $emailMessage = (new TemplatedEmail())
                    ->from(new Address('noreply@example.com', 'App Mail Bot'))
                    ->to($user->getEmail())
                    ->subject('Réinitialisation de votre mot de passe')
                    ->htmlTemplate('auth/email/reset.html.twig')
                    ->context([
                        'resetToken' => $resetToken,
                        'resetUrl' => $this->generateUrl('reset_password_token', ['token' => $resetToken], UrlGeneratorInterface::ABSOLUTE_URL),
                    ]);

                $mailer->send($emailMessage);

                $this->addFlash('success', 'Un lien de réinitialisation a été envoyé à votre adresse email.');
            } else {
                $this->addFlash('error', 'Aucun utilisateur trouvé avec cet email.');
            }
        }

        return $this->render('auth/forgot_password.html.twig');
    }

    #[Route('/post-login-check', name: 'post_login_check')]
    public function postLoginCheck(): Response
    {
        $user = $this->getUser();

        if (!$user instanceof User) {
            throw $this->createAccessDeniedException('Utilisateur non trouvé.');
        }

        if (!$user->getIsVerified()) {
            $this->addFlash('error', 'Votre compte n\'est pas encore activé. Veuillez vérifier votre email pour l\'activer.');

            return $this->redirectToRoute('logout');
        }

        return $this->redirectToRoute('default');
    }


    #[Route('/reset-password/{token}', name: 'reset_password_token')]
    public function reset(Request $request, string $token, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(['resetToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Le token est invalide ou expiré.');
            return $this->redirectToRoute('app_forgot_password');
        }

        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('plainPassword')->getData();
            $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);

            $user->setPassword($hashedPassword);
            $user->setResetToken(null);
            $this->entityManager->flush();

            $this->addFlash('success', 'Votre mot de passe a été changé avec succès.');

            return $this->redirectToRoute('default');
        }

        return $this->render('auth/reset_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/register', name: 'register')]
    public function register(Request $request, UserRepository $userRepository, MailerInterface $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

            $hashedPassword = $this->passwordHasher->hashPassword($user, $user->getPassword());
            $user->setPassword($hashedPassword);


            $confirmationToken = bin2hex(random_bytes(32));
            $user->setConfirmationToken($confirmationToken);


            $this->entityManager->persist($user);
            $this->entityManager->flush();


            $emailMessage = (new TemplatedEmail())
                ->from(new Address('noreply@example.com', 'App Mail Bot'))
                ->to($user->getEmail())
                ->subject('Confirm Your Account')
                ->htmlTemplate('auth/email/confirmation.html.twig')
                ->context([
                    'confirmationToken' => $confirmationToken,
                    'confirmationUrl' => $this->generateUrl('confirm_account', ['token' => $confirmationToken], UrlGeneratorInterface::ABSOLUTE_URL),
                ]);

            $mailer->send($emailMessage);

            $this->addFlash('success', 'Votre compte a été créé avec succès. Veuillez vérifier votre email pour confirmer votre compte.');
            return $this->redirectToRoute('login');
        }

        return $this->render('auth/register.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/confirm/{token}', name: 'confirm_account')]
    public function confirmAccount(string $token, UserRepository $userRepository): Response
    {
        $user = $userRepository->findOneBy(['confirmationToken' => $token]);

        if (!$user) {
            $this->addFlash('error', 'Token de confirmation invalide.');
            return $this->redirectToRoute('login');
        }

        $user->setIsVerified(true);
        $user->setConfirmationToken(null);
        $this->entityManager->flush();

        $this->addFlash('success', 'Votre compte a été confirmé avec succès. Vous pouvez maintenant vous connecter.');
        return $this->redirectToRoute('login');
    }

}
