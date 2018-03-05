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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;

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
    public function upload(Request $request, ValidatorInterface $validator)
    {
        $file = $request->files->get('file');
        $violations = $validator->validate($file, [
            new File([
                'mimeTypes' => [
                    'image/gif',
                    'image/jpeg',
                    'image/png',
                ],
                'maxSize' => '1Mi',
            ]),
        ]);

        if (0 !== count($violations)) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[] = $violation->getMessage();
            }

            return $this->json(['errors' => $errors], Response::HTTP_BAD_REQUEST);
        }

        $fileName = sha1(uniqid()).'.'.$file->guessExtension();
        $filePath = '/uploads/images/';

        $file->move($this->getParameter('kernel.project_dir').'/public'.$filePath, $fileName);

        return $this->json(['fileName' => $filePath.$fileName]);
    }
}
