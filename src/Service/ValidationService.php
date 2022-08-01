<?php

namespace App\Service;

use App\Repository\CustomerRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidationService
{
    private $request;
    private $validator;
    private $customerRepository;

    public function __construct(RequestStack $request, ValidatorInterface $validator, CustomerRepository $customerRepository)
    {
        $this->request = $request->getCurrentRequest();
        $this->validator = $validator;
        $this->customerRepository = $customerRepository;
    }

    public function isValid($entity): ?Response
    {

        // On check les assert (non vide, taille min/max)
        $errors = $this->validator->validate($entity);

        // ON retourne un erreur si il y a minimum 1 erreur
        if (count($errors) > 0) {
            
            $errorsString = (string) $errors;

            return new Response($errorsString);
        }

        // On recherche un customer par username (unique)
        $checkCustomer = $this->customerRepository->findByUsername(['id' => $entity->getId()]);

        // Si l'on trouve un resultat, on renvoie un message disant que le customer existe 
        // et on retourne une réponse 
        if($checkCustomer){ 
            $response = new Response("Nom d'utilisateur déjà existant", 200, [
                "Content-Type' => 'application/json"
            ]);

            return $response;
        }

        return null;
    }
}