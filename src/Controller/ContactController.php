<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Repository\ContactRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ContactController extends AbstractController
{
    private $entityManager;
    private $manager;
    private $contact;

    public function __construct(EntityManagerInterface $manager, EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
        $this->manager = $manager;

    }

    #[Route('/contactadmin', name: 'contact', methods: ['POST'])]
    public function contact(Request $request): Response
    {
        $data = json_decode($request->getContent(), true);

        // Check if the required fields are provided
        if (!isset($data['emailUser']) || !isset($data['userPhone']) || !isset($data['description'])) {
            return new JsonResponse([
                'status' => false,
                'message' => 'Email, userPhone, and description are required.',
            ], Response::HTTP_BAD_REQUEST);
        }

        // Extract data from request
        $emailUser = $data['emailUser'];
        $userPhone = $data['userPhone'];
        $description = $data['description'];

        try {
            // Create a new Contact object
            $contact = new Contact();
            $contact->setEmailUser($emailUser)
                ->setUserPhone($userPhone)
                ->setDescription($description)
                ->setDate(new \DateTime());

            // Persist the Contact object
            $this->manager->persist($this->contact);
            $this->manager->flush();

            return $this->json([
                'status' => true,
                'message' => 'Article created successfully.',
            ]);
        } catch (\Exception $e) {
            return $this->json([
                'status' => false,
                'message' => 'An error occurred while creating the article. ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
    #[Route('/getallmessages', name: 'get_all_messages', methods: ['GET'])]
    public function getAllMessages(ContactRepository $contactRepository): JsonResponse
    {
        $contacts = $contactRepository->findAll();
        $response = [];
        foreach ($contacts as $contact) {
            $response[] = [
                'id' => $contact->getId(),
                'emailUser' => $contact->getEmailUser(),
                'description' => $contact->getDescription(),
                'phoneUser' => $contact->getUserPhone(),
                'date' => $contact->getDate() ? $contact->getDate()->format('d-m-y H:i') : null,
                'status' => $contact->getStatus(),
            ];
        }
    
        return new JsonResponse($response, 200);
    }




    #[Route('/deleteMessage/{id}', name: 'delete-message', methods:'DELETE')]
    public function delete( int $id , EntityManagerInterface $entityManager): Response
    {
        $message  = $entityManager->getRepository(Contact::class)->find($id);
    
        if (!$message) {
            return $this->json('No message found for id' . $id, 404);
        }
    
        $entityManager->remove($message);
        $entityManager->flush();
    
        return $this->json('Deleted a messages successfully with id ' . $id);
    }




#[Route('/updateStatut/{id}', name: 'updateStatut-status', methods:'PUT')]
public function update(int $id , EntityManagerInterface $entityManager, Contact $contact): Response
{   
    $message  = $entityManager->getRepository(Contact::class)->find($id);

    if (!$message) {
        return $this->json('No message found for id' . $id, 404);
    }
    $contact->setStatus(true);

    $entityManager->flush();

    return $this->json($contact);
}



}

        
    

