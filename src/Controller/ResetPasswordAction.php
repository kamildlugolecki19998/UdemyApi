<?php

namespace App\Controller;

use ApiPlatform\Core\Validator\Exception\ValidationException;
use App\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class ResetPasswordAction extends AbstractController
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function __invoke(User $data)
    {
        var_dump($data->getNewPassword(), $data->getOldPassword(), $data->getNewRetypedPassword());


        // Validator is only called after  we return

        $this->checkIfPassedPasswordsAreTheSame($data->getNewPassword(), $data->getNewRetypedPassword());
        $this->checkIfOldPasswordIsCorrect($data->getPassword(), $data->getOldPassword());
    }

    private function checkIfPassedPasswordsAreTheSame(string $newPassword, string $newRetypedPassword)
    {
        if($newPassword !== $newRetypedPassword)
        {
            throw new ValidationException("Passed password are not the same");
        }

        return true;
    }

    private function checkIfOldPasswordIsCorrect(string $password, string $oldPassword)
    {
        var_dump($this->passwordHasher->needsRehash($password));
        exit();


        if($this->passwordHasher->needsRehash($this->userPa, $oldPassword)){
            throw new ValidationException("Given old user password is wrong");
        }

        return true;
    }
}
