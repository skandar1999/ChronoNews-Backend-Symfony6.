<?php

namespace App\Controller;

use App\Entity\Article;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LikesController extends AbstractController
{
    #[Route('/likes/{articleId}', name: 'article_like', methods: ['POST'])]
    public function likeArticle(int $articleId, EntityManagerInterface $entityManager): Response
{
    $article = $entityManager->getRepository(Article::class)->find($articleId);

    if (!$article) {
        throw $this->createNotFoundException('Article not found');
    }

    // Increment the number of likes in the associated article
    $article->setNbLikes($article->getNbLikes() + 1);

    // Flush changes to the database
    $entityManager->flush();

    return $this->json([
        'message' => 'Article liked ',
        'data' => [
            'articleId' => $article->getId(),
            'NbLikes' => $article->getNbLikes(),
        ],
    ], Response::HTTP_OK);
}


#[Route('/Unlikes/{articleId}', name: 'article_unlike', methods: ['POST'])]
    public function UnlikeArticle(int $articleId, EntityManagerInterface $entityManager): Response
{
    $article = $entityManager->getRepository(Article::class)->find($articleId);

    if (!$article) {
        throw $this->createNotFoundException('Article not found');
    }

    // Increment the number of likes in the associated article
    $article->setNbDisLikes($article->getNbDisLikes() + 1);

    // Flush changes to the database
    $entityManager->flush();

    return $this->json([
        'message' => 'Article unliked ',
        'data' => [
            'articleId' => $article->getId(),
            'NbLikes' => $article->getNbDisLikes(),
        ],
    ], Response::HTTP_OK);
}
}
