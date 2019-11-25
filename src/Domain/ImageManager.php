<?php


namespace App\Domain;


use App\Entity\Image;
use App\Entity\LearningModule;
use App\Entity\User;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class ImageManager
{
    public function createImage(UploadedFile $uploadedImage, User $user, string $uploads_directory, string $type): Image
    {
        $filename = md5(uniqid('', true)) . '.' . $uploadedImage->guessExtension();
        $newImage = new Image($uploadedImage->getClientOriginalName(), $filename, $user, $type);

        $uploadedImage->move(
            $uploads_directory,
            $filename
        );

        return $newImage;
    }

    public function changeModuleImage(UploadedFile $uploadedImage, Image $image, LearningModule $module, User $user, string $uploads_directory): LearningModule
    {
        if ($module->getImage() !== '') {
            unlink($uploads_directory . '/' . $module->getImage());
        }

        $filename = md5(uniqid('', true)) . '.' . $uploadedImage->guessExtension();

        $image->setSrc($filename);
        $image->setName($uploadedImage->getClientOriginalName());
        $user->addImage($image); // not sure if this line is needed
        $module->setImage($filename);

        $uploadedImage->move(
            $uploads_directory,
            $filename
        );

        return $module;
    }

    public function changeUserAvatarImage(UploadedFile $uploadedImage, Image $image, User $user, string $uploads_directory): User
    {
        if ($user->getAvatar() !== '') {
            unlink($uploads_directory . '/' . $user->getAvatar());
        }

        $filename = md5(uniqid('', true)) . '.' . $uploadedImage->guessExtension();

        $image->setName($uploadedImage->getClientOriginalName());
        $image->setType('avatar');
        $image->setSrc($filename);

        $user->addImage($image); // not sure if needed

        //put new avatar in upload dir
        $uploadedImage->move(
            $uploads_directory,
            $filename
        );
        //put filename in database
        $user->setAvatar($filename);

        return $user;
    }
}