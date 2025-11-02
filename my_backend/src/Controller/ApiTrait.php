<?php
namespace App\Controller;

use App\Document\User;
use App\Service\JWTService;
use Doctrine\ODM\MongoDB\DocumentManager;
use Symfony\Component\HttpFoundation\Request;

trait ApiTrait
{
    private function getUserFromRequest(Request $req, JWTService $jwt, DocumentManager $dm): ?User
    {
        $auth = $req->headers->get('Authorization');

        if (!$auth || !preg_match('/Bearer\s+(.*)$/i', $auth, $m)) {
            return null;
        }

        $token = $m[1];
        $data = $jwt->decode($token);

        if (!$data || !isset($data['sub'])) {
            return null;
        }

        return $dm->getRepository(User::class)->find($data['sub']);
    }
}