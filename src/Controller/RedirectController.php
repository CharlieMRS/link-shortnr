<?php

namespace App\Controller;

use App\Entity\Redirect;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class RedirectController extends AbstractController
{
    /**
     * @Route("/redirect", name="redirect")
     */
    public function index(): Response
    {
        return $this->render('redirect/index.html.twig', [
            'controller_name' => 'RedirectController',
        ]);
    }

    /**
     * @Route("shortenUrl", name="shortenUrl")
     */
    public function createRedirect(Request $request): JsonResponse
    {
        $longUrl = $request->get('longUrl');
        $entityManager = $this->getDoctrine()->getManager();

        $redirect = new Redirect();
        $redirect->setLongUrl($longUrl);
        $redirect->setShortUrl($this->getRandomString());

        $entityManager->persist($redirect);
        $entityManager->flush();

        return $this->json(['shortUrl' => $longUrl]);
    }

    function getRandomString(int $len = 9) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $len; $i++) {
            $randomString .= $characters[ rand(0, $charactersLength - 1) ];
        }
        return $randomString;
    }

}
