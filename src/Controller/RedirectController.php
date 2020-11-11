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
     * @Route("/r/{slug}", name="redirect")
     */
    public function index(string $slug): Response
    {
        $repo = $this->getDoctrine()->getRepository(Redirect::class);
        /** @var Redirect $redirect */
        $redirect = $repo->findOneBy(['shortUrl' => $slug]);
        return $this->redirect($redirect->getLongUrl());
    }

    /**
     * @Route("shortenUrl", name="shortenUrl")
     */
    public function create(Request $request): Response
    {
        $longUrl = $request->get('longUrl');
        try {
            $this->checkUnique($longUrl);
        } catch (\InvalidArgumentException $e) {
            return $this->json(['error' => $e->getMessage()]);
        }

        $entityManager = $this->getDoctrine()->getManager();
        $shortUrl = $this->getRandomString();

        $redirect = new Redirect();
        $redirect->setLongUrl($longUrl);
        $redirect->setShortUrl($shortUrl);

        $entityManager->persist($redirect);
        $entityManager->flush();

        return $this->render('redirect/index.html.twig', [
            'longUrl' => $longUrl,
            'shortUrl' => $shortUrl
        ]);
    }

    /**
     * @Route("view/{link}", name="view")
     */
    public function view(string $link)
    {
        $repo = $this->getDoctrine()->getRepository(Redirect::class);
        /** @var Redirect $redirect */
        $redirect = $repo->findOneBy(['shortUrl' => $link]);

        return $this->render('info.html.twig', [
            'longUrl' => $redirect->getLongUrl(),
            'shortUrl' => $redirect->getShortUrl(),
            'page' => true
        ]);
    }

    private function getRandomString(int $len = 9) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
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
