<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SocialAuthenticationController extends AbstractController
{
    #[Route('/connect/github', name: 'connect_github_start')]
    public function connectGithubAction(ClientRegistry $clientRegistry)
    {
        return $clientRegistry
            ->getClient('github_main')
            ->redirect([
                'user:email'
            ]);
    }

    public function connectGithubCheckAction(Request $request, ClientRegistry $clientRegistry)
    {
        return $this->redirectToRoute('pages_homepage');
    }
}
