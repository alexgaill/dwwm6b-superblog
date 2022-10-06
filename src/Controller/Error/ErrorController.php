<?php
namespace App\Controller\Error;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ErrorController extends AbstractController {
    
    public function show(Request $request): Response
    {
        dump($request);
        return $this->render("error/show.html.twig", [
            'exception' => $request->get('exception'),
            'logger' => $request->get('logger'),
            'user' => $this->getUser() ?? null
        ]);
    }
}