<?php

namespace App\Controller;

use DateTime;
use App\Entity\Article;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;

class ArticleController extends AbstractController
{
    private $manager;
    private $article;

    public function __construct(EntityManagerInterface $manager, EntityManagerInterface $entityManager)
    {

        $this->manager = $manager;
        $this->article = new Article();
    }

    #[Route('/addnewpost', name: 'add_new_post', methods: 'POST')]
    public function addnewpost(Request $request): Response
    {
        $titre = $request->request->get('titre');
        $contenu = $request->request->get('contenu');
        $categorie = $request->request->get('categorie');

        // Handle file upload
        $imageFile = $request->files->get('image');

        if ($imageFile instanceof UploadedFile) {
            $newFilename = md5(uniqid()) . '.' . $imageFile->guessExtension();
            $imageFile->move(
                $this->getParameter('uploads_directory'),
                $newFilename
            );

            // Set the image property if file is uploaded
            $this->article->setImage($newFilename);
        }

        // Validate required fields
        if (!$titre || !$contenu) {
            return new JsonResponse([
                'status' => false,
                'message' => 'Title and content are required.',
            ], Response::HTTP_BAD_REQUEST);
        }

        try {
            $this->article->setTitre($titre)
                ->setContenu($contenu)
                ->setPublishDate(new \DateTime())
                ->setCatégorie($categorie);

            $this->manager->persist($this->article);
            $this->manager->flush();

            // Include the uploaded file in the response
            $fileUrl = $request->getSchemeAndHttpHost() . '/ArticleImages/' . $newFilename;

            return $this->json([
                'status' => true,
                'message' => 'Article created successfully.',
                'file_url' => $fileUrl,
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => false,
                'message' => 'An error occurred while creating the article. ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }



    #[Route('/updateArticle/{id}', name: 'article_update', methods: ['PUT', 'PATCH'])]
    public function update(Request $request, ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $article = $entityManager->getRepository(Article::class)->findOneBy(['id' => $id]);
    
        if (!$article) {
            return $this->json(['message' => 'Article not found'], 404);
        }
    
        $data = json_decode($request->getContent(), true);
    
        if (isset($data['titre'])) {
            $article->setTitre($data['titre']);
        }
    
        if (isset($data['contenu'])) {
            $article->setContenu($data['contenu']);
        }
    
        if (isset($data['categorie'])) {
            $article->setCatégorie($data['categorie']);
        }
    
        $entityManager->flush();
    
        return $this->json(['message' => 'Article updated with success', 'data' => [
            'id' => $article->getId(),
            'titre' => $article->getTitre(),
            'contenu' => $article->getContenu(),
            'categorie' => $article->getCatégorie(),
        ]]);
    }


    #[Route('/deleteArticle/{id}', name: 'article_delete', methods: ['DELETE'])]
    public function deleteArticle(ManagerRegistry $doctrine, int $id): JsonResponse
    {
        $entityManager = $doctrine->getManager();
        $articleRepository = $entityManager->getRepository(Article::class);
        $article = $articleRepository->find($id);

        if (!$article) {
            return $this->json(['message' => 'Article not found'], 404);
        }

        try {
            $entityManager->remove($article);
            $entityManager->flush();

            return $this->json(['message' => 'Article deleted successfully']);
        } catch (\Exception $e) {
            return $this->json(['message' => 'An error occurred while deleting the article', 'error' => $e->getMessage()], 500);
        }
    }
    
}



