<?php

namespace App\Controller;

use App\Repository\RedirectRepository;
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
        /** @var Redirect $redirect */
        $redirect = $this->getRepo()->findOneBy(['shortUrl' => $slug]);
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
            'linkId' => $redirect->getId(),
            'longUrl' => $longUrl,
            'shortUrl' => $shortUrl
        ]);
    }

    /**
     * @Route("view/{link}", name="view")
     */
    public function view(string $link)
    {
        $redirect = $this->getRepo()->findOneBy(['shortUrl' => $link]);

        return $this->render('info.html.twig', [
            'linkId' => $redirect->getId(),
            'longUrl' => $redirect->getLongUrl(),
            'shortUrl' => $redirect->getShortUrl()
        ]);
    }

    /**
     * @Route("deleteLink", name="deleteLink")
     */
    public function delete(Request $request): Response
    {
        $id = $request->get('id');
        $link = $this->getRepo()->find($id);

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($link);
        $entityManager->flush();

        $this->addFlash(
            'notice',
            'Your short link was deleted.'
        );

        return $this->redirect('/');
    }

    private function getRandomString(int $len = 9): string
    {
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
        if ($this->getRepo()->findOneBy(['longUrl' => $longUrl])) {
            throw new InvalidArgumentException('That url has already been shortened. Please choose another.');
        }
    }

    private function getRepo(): RedirectRepository
    {
        return $this->getDoctrine()->getRepository(Redirect::class);
    }

}
