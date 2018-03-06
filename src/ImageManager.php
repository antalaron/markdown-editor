<?php

/*
 * This file is part of MarkdownEditor.
 *
 * (c) Antal Áron <antalaron@antalaron.hu>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace App;

use App\Entity\Image;
use Doctrine\Common\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Exception\IOException;

/**
 * @author Antal Áron <antalaron@antalaron.hu>
 */
class ImageManager
{
    const UPLOAD_PATH = '/uploads/images/';

    /**
     * @var string
     */
    private $projectDir;

    /**
     * @var int
     */
    private $maxFilesize;

    /**
     * @var ValidatorInterface
     */
    private $validator;

    /**
     * @var ManagerRegistry
     */
    private $managerRegistry;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var array
     */
    private $response = [];

    public function __construct(string $projectDir, int $maxFilesize, ValidatorInterface $validator, ManagerRegistry $managerRegistry, Filesystem $filesystem)
    {
        $this->projectDir = $projectDir;
        $this->maxFilesize = $maxFilesize;
        $this->validator = $validator;
        $this->managerRegistry = $managerRegistry;
        $this->filesystem = $filesystem;
    }

    public function handle(UploadedFile $file): bool
    {
        if (true !== $response = $this->isValid($file)) {
            $this->response = $response;

            return false;
        }

        $this->response = ['fileName' => $this->saveImage($file)];

        return true;
    }

    public function getResponse(): array
    {
        return $this->response;
    }

    public function removeExpiredImages(bool $dryRun = false): int
    {
        $entityManager = $this->managerRegistry->getManager();

        if ($dryRun) {
            return $entityManager->getRepository(Image::class)->expiredImagesCount();
        } else {
            $expiredImages = $entityManager->getRepository(Image::class)->findExpiredImages();
            foreach ($expiredImages as $expiredImage) {
                $expiredFileName = $this->projectDir.'/public'.static::UPLOAD_PATH.
                    ($expiredImage->getName()).'.'.($expiredImage->getType());
                try {
                    $this->filesystem->remove($expiredFileName);
                } catch (IOException $e) {
                    // no-op
                }
            }
        }

        return $entityManager->getRepository(Image::class)->removeExpiredImages();
    }

    /**
     * @return true|array
     */
    private function isValid(UploadedFile $file)
    {
        $violations = $this->validator->validate($file, [
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

            return ['errors' => $errors];
        }

        if ($this->maxFilesize < $this->managerRegistry->getRepository(Image::class)->getSumSize() + $file->getSize()) {
            return ['errors' => ['No more space left on device']];
        }

        return true;
    }

    private function saveImage(UploadedFile $file): string
    {
        $image = new Image();
        $image
            ->setName(sha1(uniqid()))
            ->setType($file->guessExtension())
            ->setCreatedAt(new \DateTime())
            ->setSize($file->getSize())
        ;

        $entityManager = $this->managerRegistry->getManager();
        $entityManager->persist($image);
        $entityManager->flush();

        $fileName = $image->getName().'.'.$image->getType();
        $filePath = static::UPLOAD_PATH;

        $file->move($this->projectDir.'/public'.$filePath, $fileName);

        return $filePath.$fileName;
    }
}
