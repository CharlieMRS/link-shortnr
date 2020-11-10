<?php


namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class IndexController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('base.html.twig', [

        ]);
    }

    /**
     * @Route("shortenUrl", name="shortenUrl")
     */
    public function shortenUrl(Request $request)
    {
        return $this->json(['shortUrl' => $request->get('longUrl')]);

    }

}