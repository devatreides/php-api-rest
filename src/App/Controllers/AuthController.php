<?php

namespace Api\App\Controllers;

use Api\App\Core\Middlewares\Authenticate;
use Api\App\Core\Middlewares\VerifyMfaCode;
use Api\App\Core\ORM\Doctrine;
use Api\App\Core\Request\Request;
use Api\App\Core\Routing\Attributes\Get;
use Api\App\Core\Routing\Attributes\Post;
use Api\App\Entities\User;
use Exception;
use Firebase\JWT\JWT;
use RobThree\Auth\TwoFactorAuth;

class AuthController
{
    #[Post(uri: '/login', successCode: 200)]
    public function authenticate(Request $request): array
    {
        $queryBuilder = (new Doctrine)->getQueryBuilder();

        $user = $queryBuilder
            ->select('*')
            ->from('users', 'u')
            ->where('u.email = :email')
            ->setParameter('email', $request->get('email'))
            ->fetchAssociative();

        if (!$user) {
            throw new Exception('Email or password are incorrect.', 401);
        }

        $check = password_verify($request->get('password'), $user['password'] ?? '');

        if (!$check) {
            return  new Exception('Email or password are incorrect.', 401);
        }

        if($user['mfa_secret']) {
            $tfa = new TwoFactorAuth;

            $secret = $user['mfa_secret'];

            $check = $tfa->verifyCode($secret, $request->get('code'));

            if(!$check) {
                throw new Exception('Invalid MFA code.', 401);
            }
        }

        $payload = [
            'exp' => time() + 3600,
            'iat' => time(),
            'email' => $user['email'],
            'isAdmin' => $user['is_admin']
        ];

        $encode = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

        return [
            'token' => $encode,
        ];

    }

    #[Post(uri: '/register', successCode: 201)]
    public function register(Request $request): array
    {
        $newUser = (new User)
            ->setName($request->get('name'))
            ->setEmail($request->get('email'))
            ->setPassword(password_hash($request->get('password'), PASSWORD_ARGON2I))
            ->setCreatedAt(new \DateTime);
            
        $entityManager = (new Doctrine)->getEntityManager();
        $entityManager->persist($newUser);
        $entityManager->flush();

        return ['message'=>'User created successfully.'];
    }

    #[Get(uri: '/enable-mfa', middlewares:[Authenticate::class], successCode: 200)]
    public function enableMfa(Request $request): array
    {
        $entityManager = (new Doctrine)->getEntityManager();

        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $request->get('loggedUserEmail')]);
        $tfa = new TwoFactorAuth('API App');

        $secret = $tfa->createSecret();

        $user->setTmpSecret($secret);
        $entityManager->flush();

        return [
            'secret' => $secret,
            'qrCode' => $tfa->getQRCodeImageAsDataUri('API App', $secret)
        ];
    }

    #[Post(uri: '/verify-mfa', middlewares:[Authenticate::class] , successCode: 200)]
    public function verifyMfa(Request $request): array
    {
        $entityManager = (new Doctrine)->getEntityManager();
        $user = $entityManager->getRepository(User::class)->findOneBy(['email' => $request->get('loggedUserEmail')]);

        $tfa = new TwoFactorAuth;

        $secret = $user->getTmpSecret();

        $check = $tfa->verifyCode($secret, $request->get('code'));

        if(!$check) {
            throw new Exception('Invalid MFA code.', 401);
        }

        $user->setMfaSecret($secret);
        $user->setTmpSecret(null);
        $entityManager->flush();

        return ['message'=>'MFA code is valid. You can now login with MFA enabled.'];
    }
}
