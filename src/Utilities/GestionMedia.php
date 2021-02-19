<?php


namespace App\Utilities;


use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class GestionMedia
{
    private $mediaSlide;
    private $mediaDomaine;
    private $mediaMetier;

    public function __construct($slideDirectory, $domaineDirectory, $metierDirectory)
    {
        $this->mediaSlide = $slideDirectory;
        $this->mediaDomaine = $domaineDirectory;
        $this->mediaMetier = $metierDirectory;
    }

    /**
     * Enregistrement du fichier dans le repertoire approprié
     *
     * @param UploadedFile $file
     * @param null $media
     * @return string
     */
    public function upload(UploadedFile $file, $media = null)
    {
        // Initialisation du slug
        $slugify = new Slugify();

        $originalFileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $slugify->slugify($originalFileName);
        $newFilename = $safeFilename.'-'.uniqid().'.'.$file->guessExtension();

        // Deplacement du fichier dans le repertoire dedié
        try {
            if ($media === 'slide') $file->move($this->mediaSlide, $newFilename);
            elseif ($media === 'domaine') $file->move($this->mediaDomaine, $newFilename);
            elseif ($media === 'metier') $file->move($this->mediaMetier, $newFilename);
            else $file->move($this->mediaSlide, $newFilename);
        }catch (FileException $e){

        }

        return $newFilename;
    }

    /**
     * Suppression de l'ancien media sur le server
     *
     * @param $ancienMedia
     * @param null $media
     * @return bool
     */
    public function removeUpload($ancienMedia, $media = null)
    {
        if ($media === 'slide') unlink($this->mediaSlide.'/'.$ancienMedia);
        elseif ($media === 'domaine') unlink($this->mediaDomaine.'/'.$ancienMedia);
        elseif ($media === 'metier') unlink($this->mediaMetier.'/'.$ancienMedia);
        else return false;

        return true;
    }
}