<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Entity\Article;
use App\Entity\Comment;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


class CommentaireController extends AbstractController
{
    #[Route('/addComment/{articleId}', name: 'add_comment', methods: ['POST'])]
    public function addComment(Request $request, int $articleId, EntityManagerInterface $entityManager): Response
    {
        $article = $entityManager->getRepository(Article::class)->find($articleId);
    
        if (!$article) {
            throw $this->createNotFoundException('Article not found');
        }
    
        $data = json_decode($request->getContent(), true);
    
        // Validate required fields
        if (!isset($data['content']) || !isset($data['author'])) {
            return $this->json(['message' => 'Content and author are required'], Response::HTTP_BAD_REQUEST);
        }
    
        $content = $data['content'];
        $author = $data['author'];
    
        // Create a new comment
        $comment = new Comment();
        $comment->setContent($content)
            ->setAuthor($author)
            ->setDateCommentaire(new \DateTime())
            ->setArticle($article);
    
        // Persist the comment to the database
        $entityManager->persist($comment);
    
        // Increment the number of comments in the associated article
        $article->setNbComments($article->getNbComments() + 1);
    
        // Flush changes to the database
        $entityManager->flush();
    
        return $this->json([
            'message' => 'Comment added successfully',
            'data' => [
                'id' => $comment->getId(),
                'content' => $comment->getContent(),
                'author' => $comment->getAuthor(),
                'dateCommentaire' => $comment->getDateCommentaire()->format('Y-m-d H:i:s'),
                'articleId' => $article->getId(),
                'NbComments' => $article->getNbComments(),
            ],
        ], Response::HTTP_CREATED);
    }
}
