<?php
/**
 * Created by PhpStorm.
 * User: jpbella
 * Date: 15/02/19
 * Time: 16:45
 */

namespace App\Controller;


use App\Entity\DTO\ChangeUserRequest;
use App\Entity\User;
use App\Service\UserService;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\UserBundle\Model\UserManagerInterface;
use OAuth2\OAuth2RedirectException;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations as Rest;
use Swagger\Annotations as SWG;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Nelmio\ApiDocBundle\Annotation\Model;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;


/**
 * Class AdminController
 * @package App\Controller
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends FOSRestController
{
    const BLOCK= "block";
    private $userManager;
    private $userService;

    public function __construct(UserManagerInterface $userManager, UserService $userService)
    {
        $this->userManager = $userManager;
        $this->userService = $userService;
    }
    /**
     * @Rest\Get("/admin/users/{email}")
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
     * @Rest\Get("/admin/users")
     *
     * @SWG\Get(
     *     produces={"application/json"},
     *      @SWG\Response(
     *          response=200,
     *          description="Renvoie les utilisateurs",
     *          @Model(type=App\Entity\User::class),
     *          @SWG\Schema(ref="#\App\Entity\User")
     *     ),
     * )
     */
    public function getUsersAction(){
        $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $view = $this->view($users, 200);
        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/admin/users")
     *
     * @SWG\Post(
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="user",
     *          in="body",
     *          description="user",
     *          required=true,
     *          @Model(type=App\Entity\DTO\ChangeUserRequest::class),
     *          @SWG\Schema(ref="#\App\Entity\User")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Renvoie l'utilisateur modifié",
     *          @Model(type=App\Entity\User::class),
     *          @SWG\Schema(ref="#\App\Entity\User")
     *     ),
     * )
     * @ParamConverter("request", converter="fos_rest.request_body")
     */
    public function updateUsersAction(ChangeUserRequest $request){
        $user = $this->getDoctrine()->getRepository(User::class)->find($request->getId());
        if ($request->getRole() == self::BLOCK){
            $user->setEnabled($request->getIsPromoting());
        } else {
            $user = $this->userService->updateUserRole($user, $request->getRole(), $request->getIsPromoting());
        }
        $this->userManager->updateUser($user);
        $view = $this->view($user, 200);
        return $this->handleView($view);
    }
}