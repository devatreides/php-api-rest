<?php

namespace Api\App\Controllers;

use Api\App\Core\Middlewares\Authenticate;
use Api\App\Core\Middlewares\AuthorizeIfAdmin;
use Api\App\Core\ORM\Doctrine;
use Api\App\Core\Request\Request;
use Api\App\Core\Routing\Attributes\Post;
use Api\App\Core\Routing\Attributes\Put;
use Api\App\Entities\User;

class UserController
{
    #[Post(uri: '/users', middlewares:[Authenticate::class], successCode: 201)]
    public function create(Request $request): User
    {
        $newUser = (new User)
            ->setName($request->get('name'))
            ->setEmail($request->get('email'))
            ->setIsAdmin($request->get('isAdmin'))
            ->setPassword(password_hash($request->get('password'), PASSWORD_ARGON2I))
            ->setCreatedAt(new \DateTime);
            
        $entityManager = (new Doctrine)->getEntityManager();
        $entityManager->persist($newUser);
        $entityManager->flush();

        return $newUser;
    }

    #[Put(uri: '/users/{id}', middlewares:[Authenticate::class], successCode: 200)]
    public function update(Request $request, int $id): User
    {
        $entityManager = (new Doctrine)->getEntityManager();
        $user = $entityManager->find(User::class, $id);

        if ($user === null) {
            throw new \Exception('User not found', 404);
        }

        $user->setName($request->get('name'));
        $user->setUpdatedAt(new \DateTime);

        $entityManager->flush();

        return $user;
    }

    #[Post(uri: '/users/{id}/deactivate', middlewares:[Authenticate::class, AuthorizeIfAdmin::class], successCode: 200)]
    public function deactivate(Request $request, int $id): array
    {
        $entityManager = (new Doctrine)->getEntityManager();
        $user = $entityManager->find(User::class, $id);

        if ($user === null) {
            throw new \Exception('User not found', 404);
        }

        if (!$user->getIsActive()) {
            return 'User is already deactivated';
        }

        $user->setIsActive(false);
        $user->setUpdatedAt(new \DateTime);

        $entityManager->flush();

        return ['message' => 'User deactivated successfully.'];
    }

    #[Post(uri: '/users/{id}/activate', middlewares:[Authenticate::class, AuthorizeIfAdmin::class], successCode: 200)]
    public function activate(Request $request, int $id): array
    {
        $entityManager = (new Doctrine)->getEntityManager();
        $user = $entityManager->find(User::class, $id);

        if ($user === null) {
            throw new \Exception('User not found', 404);
        }

        if ($user->getIsActive()) {
            return 'User is already active';
        }

        $user->setIsActive(true);
        $user->setUpdatedAt(new \DateTime);

        $entityManager->flush();

        return ['message' => 'User activated successfully.'];
    }
}
