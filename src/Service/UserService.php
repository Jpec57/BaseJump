<?php
/**
 * Created by PhpStorm.
 * User: jpbella
 * Date: 06/02/19
 * Time: 14:35
 */

namespace App\Service;
use App\Entity\DTO\UserRequest;
use App\Entity\User;
use PDOException;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use FOS\UserBundle\Model\UserManagerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserService extends AbstractController
{

    private $userManager;

    private $encoder;

    public function __construct(UserManagerInterface $userManager, UserPasswordEncoderInterface $encoder)
    {
        $this->userManager = $userManager;
        $this->encoder = $encoder;
    }

    public function updateUserRole(User $user, string $role, bool $isPromoting){
        $roles = $user->getRoles();
        if ($isPromoting){
            array_push($roles, $role);
        } else {
            if (($key = array_search($role, $roles)) !== false) {
                unset($roles[$key]);
            }
        }
        $user->setRoles($roles);
        return $user;
    }

    public function registerAction(UserRequest $userDTO){
        $user = $this->userManager->findUserByUsername($userDTO->getUsername());
        if (is_null($user)){
            $user = $this->userManager->createUser();
            $user->setUsername($userDTO->getUsername());
            $user->setEmail($userDTO->getUsername());
            $user->setEmailCanonical($userDTO->getUsername());
            $user->setEnabled(1);
            $user->setPlainPassword($userDTO->getPassword());
            try {
                $this->userManager->updateUser($user);
            } catch (Exception $e) {
                throw new \Exception("Une erreur est intervenue lors de l'enregistrement de l'utilisateur");
            }
        }
        return $user;
    }
}