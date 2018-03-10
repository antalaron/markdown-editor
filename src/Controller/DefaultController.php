<?php

/*
 * This file is part of MarkdownEditor.
 *
 * (c) Antal Áron <antalaron@antalaron.hu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App\Controller;

use App\Entity\Image;
use App\ImageManager;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @author Antal Áron <antalaron@antalaron.hu>
 */
class DefaultController extends Controller
{
    /**
     * @Route("/", name="index")
     */
    public function index()
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/upload", name="upload")
     */
    public function upload(Request $request, ImageManager $imageManager)
    {
        $file = $request->files->get('file');
        if ($imageManager->handle($file)) {
            return $this->json($imageManager->getResponse());
        }

        return $this->json($imageManager->getResponse(), Response::HTTP_BAD_REQUEST);
    }
}
