<?php
namespace App\Controller;

use App\Document\User;
use App\Service\JWTService;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api', name: 'api_')]
class AuthController extends AbstractController
{
    private $dm; 
    private $jwt; 
    private $hasher; 
    private $validator;

    public function __construct(
        DocumentManager $dm, 
        JWTService $jwt, 
        UserPasswordHasherInterface $hasher, 
        ValidatorInterface $validator
    ) {
        $this->dm = $dm;
        $this->jwt = $jwt;
        $this->hasher = $hasher;
        $this->validator = $validator;
    }

    #[Route('/register', name: 'register', methods: ['POST'])]
    public function register(Request $req)
    {
        $data = json_decode($req->getContent(), true);
        $email = $data['email'] ?? null;
        $name = $data['name'] ?? null;
        $password = $data['password'] ?? null;
        if (!$email || !$name || !$password) return new JsonResponse(['message'=>'Missing fields'], 400);

        if ($this->dm->getRepository(User::class)->findOneBy(['email'=>$email])) {
            return new JsonResponse(['message'=>'Email exists'], 400);
        }

        $user = new User();
        $user->setEmail($email)->setName($name);

        $hashed = password_hash($password, PASSWORD_DEFAULT);
  
        $user->setPassword($hashed);

        $errors = $this->validator->validate($user);
        if (count($errors) > 0) {
            $errs = [];
            foreach ($errors as $e) $errs[] = $e->getPropertyPath().': '.$e->getMessage();
            return new JsonResponse(['message'=>'validation_failed','errors'=>$errs], 400);
        }

        $this->dm->persist($user);
        $this->dm->flush();

        return new JsonResponse(['id'=>$user->getId()], 201);
    }

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(Request $req)
    {
        $data = json_decode($req->getContent(), true);
        $email = $data['email'] ?? null;
        $password = $data['password'] ?? null;
        if (!$email || !$password) {
            return new JsonResponse(['message'=>'Missing fields'], 400);
        }

        $user = $this->dm->getRepository(User::class)->findOneBy(['email'=>$email]);

        if (!$user) {
            return new JsonResponse(['message'=>'invalid credentials'], 401);
        }

        if (!password_verify($password, $user->getPassword())) {
            return new JsonResponse(['message'=>'invalid credentials'], 401);
        }

        $token = $this->jwt->generate(['sub' => $user->getId(), 'email'=>$user->getEmail()]);
        return new JsonResponse(['token' => $token]);
    }
}