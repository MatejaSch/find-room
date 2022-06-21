<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImageUploader
{
    private SluggerInterface $slugger;
    private string $targetDirectory;

    public function __construct(SluggerInterface $slugger, string $targetDirectory)
    {
        $this->slugger = $slugger;
        $this->targetDirectory = $targetDirectory;
    }

    public function uploadImage(UploadedFile $imageFile) : string
    {
        $originalFilename = pathinfo($imageFile->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $newFilename = $safeFilename . '-' . uniqid('', true) . '.' . $imageFile->guessExtension();
        $imageFile->move(
            $this->getTargetDirectory(),
            $newFilename
        );
        return $newFilename;
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

}