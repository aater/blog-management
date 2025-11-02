<?php
namespace App\Controller;

use App\Document\Article;
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
class ArticleController extends AbstractController
{
    use ApiTrait;

    private $dm; 
    private $jwt; 
    private $validator;

    public function __construct(
        DocumentManager $dm, 
        JWTService $jwt, 
        ValidatorInterface $validator
    ) { 
        $this->dm=$dm;
        $this->jwt=$jwt;
        $this->validator=$validator; 
    }

    #[Route('/articles', name: 'article_list', methods: ['GET'])]
    public function list()
    {
        $articles = $this->dm->getRepository(Article::class)->findBy([], ['publishedAt' => -1]);

        $data = array_map(function(Article $a){
            return [
                'id' => $a->getId(),
                'title' => $a->getTitle(),
                'content' => $a->getContent(),
                'author' => $a->getAuthor() ? $a->getAuthor()->getName() : null,
                'authorId' => $a->getAuthor() ? $a->getAuthor()->getId() : null,
                'publishedAt' => $a->getPublishedAt()->format(\DateTime::ATOM),
            ];
        }, $articles);

        return $this->json($data);
    }

    
    #[Route('/articles/{id}', name: 'article_get', methods: ['GET'])]
    public function getArticle($id)
    {
        $article = $this->dm->getRepository(Article::class)->find($id);

        if (!$article) {
            return new JsonResponse(['message'=>'not found'],404);
        }

        return $this->json([
            'id'=>$article->getId(),
            'title'=>$article->getTitle(),
            'content'=>$article->getContent(),
            'authorId'=>$article->getAuthor() ? $article->getAuthor()->getId() : null,
            'publishedAt'=>$article->getPublishedAt()->format(\DateTime::ATOM),
        ]);
    }

     #[Route('/articles', name: 'article_create', methods: ['POST'])]
    public function create(Request $req)
    {
        $user = $this->getUserFromRequest($req, $this->jwt, $this->dm);
        if (!$user) {
            return new JsonResponse(['message'=>'unauthorized'],401);
        }

        $data = json_decode($req->getContent(), true);
        $title = $data['title'] ?? null;
        $content = $data['content'] ?? null;

        if (!$title || !$content) {
            return new JsonResponse(
                ['message'=>'title & content required'],
                400
            );
        }

        $article = new Article();
        $article->setTitle($title)->setContent($content)->setAuthor($user);
        $errors = $this->validator->validate($article);

        if (count($errors)>0) {
            return new JsonResponse(
                ['message'=>'validation_failed'],
                400
            );
        }

        $this->dm->persist($article);
        $this->dm->flush();

        return new JsonResponse(['id'=>$article->getId()],201);
    }

    #[Route('/articles/{id}', name: 'article_update', methods: ['PUT'])]
    public function update($id, Request $req)
    {
        $user = $this->getUserFromRequest($req, $this->jwt, $this->dm);

        if (!$user) {
            return new JsonResponse(['message'=>'unauthorized'],401);
        }

        $article = $this->dm->getRepository(Article::class)->find($id);

        if (!$article) {
            return new JsonResponse(['message'=>'not found'],404);
        }

        if ($article->getAuthor()->getId() !== $user->getId()) {
            return new JsonResponse(['message'=>'forbidden'],403);
        }

        $data = json_decode($req->getContent(), true);

        if (isset($data['title'])) {
            $article->setTitle($data['title']);
        }

        if (isset($data['content'])) {
            $article->setContent($data['content']);
        }

        $this->dm->flush();
        return new JsonResponse(['message'=>'updated']);
    }

     #[Route('/articles/{id}', name: 'article_delete', methods: ['DELETE'])]
    public function delete($id, Request $req)
    {
        $user = $this->getUserFromRequest($req, $this->jwt, $this->dm);

        if (!$user) {
            return new JsonResponse(['message'=>'unauthorized'],401);
        }

        $article = $this->dm->getRepository(Article::class)->find($id);

        if (!$article) {
            return new JsonResponse(['message'=>'not found'],404);
        }

        if ($article->getAuthor()->getId() !== $user->getId()) {
            return new JsonResponse(['message'=>'forbidden'],403);
        }

        $this->dm->remove($article);
        $this->dm->flush();

        return new JsonResponse(['message'=>'deleted']);
    }
}