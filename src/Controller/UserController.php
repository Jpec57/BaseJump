<?php
/**
 * Created by PhpStorm.
 * User: jpbella
 * Date: 06/02/19
 * Time: 14:43
 */

namespace App\Controller;


use App\Entity\DTO\UserRequest;
use App\Entity\DTO\UserResponse;
use App\Entity\User;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use App\Service\UserService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations as Rest;


class UserController extends FOSRestController
{
    private $signUpUser;

    private $validator;


    public function __construct(UserService $signUpUser, ValidatorInterface $validator)
    {
        $this->signUpUser = $signUpUser;
        $this->validator = $validator;
    }

    /**
     * @Rest\Get("/users/{email}")
     *
     * @SWG\Get(
     *     produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="Renvoie l'utilisateur voulu",
     *         ),
     *     ),
     * )
     */
    public function getUserAction(Request $request)
    {
        $email = $request->get("email", null);
        if (is_null($email)){
            return new JsonResponse(array("message"=> array("message" => "L'email est obligatoire")), 400);
        }
        $user = $this->getDoctrine()->getRepository(User::class)->findBy(array("email"=>$email));
        $view = $this->view($user, 200);
        return $this->handleView($view);
    }

    /**
     *
     * @Rest\Post("/register")
     *
     * @ParamConverter("userRequest", converter="fos_rest.request_body")
     * @SWG\Post(
     *     consumes={"application/json"},
     *     produces={"App\Entity\User"},
     *     @SWG\Parameter(
     *          name="user",
     *          in="body",
     *          description="user",
     *          required=true,
     *          @Model(type=App\Entity\DTO\UserRequest::class),
     *          @SWG\Schema(ref="#\App\Entity\DTO\UserRequest")
     *      ),
     *     @SWG\Response(
     *          response=200,
     *          description="L'utilisateur a bien été créé",
     *          @Model(type=App\Entity\User::class),
     *          @SWG\Schema(ref="#\App\Entity\User")
     *         ),
     *     ),
     * )
     * @throws \Exception
     */
    public function registerUserAction(UserRequest $userRequest){
        if (count($this->validator->validate($userRequest)) > 0){
            return new JsonResponse(array("message" => "L'email et le mot de passe ne doivent pas être nuls"), 400);
        }
        $user = $this->signUpUser->registerAction($userRequest);
        if (is_null($user)){
            return new JsonResponse(array("message" => "L'utilisateur existe déjà"), 400);
        }
        $view = $this->view($user, 200);
        return $this->handleView($view);
    }

}