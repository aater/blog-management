<?php
namespace App\Controller;

use App\Document\User;
use App\Service\JWTService;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use App\Controller\ApiTrait;

#[Route('/api', name: 'api_')]
class UserController extends AbstractController
{
    use ApiTrait;

    private $dm; 
    private $jwt; 
    private $validator;

    public function __construct(
        DocumentManager $dm, 
        JWTService $jwt,
        ValidatorInterface $validator
    )  { 
        $this->dm=$dm;
        $this->jwt=$jwt;
        $this->validator=$validator; 
    }

    #[Route('/me', name: 'me_get', methods: ['GET'])]
    public function me(Request $req)
    {
        $user = $this->getUserFromRequest($req, $this->jwt, $this->dm);

        if (!$user) {
            return new JsonResponse(['message'=>'unauthorized'], 401);
        }

        return $this->json([
            'id'=>$user->getId(),
            'email'=>$user->getEmail(),
            'name'=>$user->getName()
        ]);
    }

    #[Route('/me', name: 'me_put', methods: ['PUT'])]
    public function update(Request $req)
    {
        $user = $this->getUserFromRequest($req, $this->jwt, $this->dm);
        if (!$user) {
            return new JsonResponse(['message'=>'unauthorized'], 401);
        }

        $data = json_decode($req->getContent(), true);

        if (isset($data['name'])) {
            $user->setName($data['name']);
        }

        if (isset($data['email'])) {
            $user->setEmail($data['email']);
        }

        if (isset($data['password'])) {
            $user->setPassword(password_hash($data['password'], PASSWORD_DEFAULT));
        }

        $errors = $this->validator->validate($user);

        if (count($errors)>0) {
            $errs=[]; 

            foreach($errors as $e) {
                $errs[]=(string)$e;
            }

            return new JsonResponse([
                'message'=>'validation_failed',
                'errors'=>$errs],
                400
            );
        }

        $this->dm->flush();
        return new JsonResponse(
            ['message'=>'updated']
        );
    }
}