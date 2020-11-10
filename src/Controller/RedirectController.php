<?php

namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Redirect;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Exception\InvalidArgumentException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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

        try {
            $this->checkUnique($longUrl);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()]);
        }

        $redirect = new Redirect();
        $redirect->setLongUrl($longUrl);
        $redirect->setShortUrl($this->getRandomString());

        $entityManager->persist($redirect);
        $entityManager->flush();

        return $this->json(['shortUrl' => $longUrl]);
    }

    private function getRandomString(int $len = 9) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $len; $i++) {
            $randomString .= $characters[ rand(0, $charactersLength - 1) ];
        }
        return $randomString;
    }

    private function checkUnique(string $longUrl)
    {
        $repo = $this->getDoctrine()->getRepository(Redirect::class);
        if ($repo->findOneBy(['longUrl' => $longUrl])) {
            throw new InvalidArgumentException('That url has already been shortened. Please choose another.');
        }
    }

}
