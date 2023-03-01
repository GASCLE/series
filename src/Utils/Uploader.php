<?php

namespace App\Utils;


use Symfony\Component\HttpFoundation\File\UploadedFile;

class Uploader
{

    public function upload(UploadedFile $file, String $directory,String $name = ""){

        //creation d'un nouveau nom
        $newFileName = $name . "-" . uniqid() . "." . $file->guessExtension();
        //copie du fichier dans le répértoire de sauvegarde en le renommant
        $file->move($directory, $newFileName);

        return $newFileName;
    }

}