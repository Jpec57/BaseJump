<?php

namespace App\Controller;

use App\Entity\DTO\EventRequest;
use App\Entity\Event;
use App\Entity\User;
use App\Service\EventService;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\UserBundle\Model\UserManagerInterface;
use OAuth2\OAuth2RedirectException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Config\Tests\Util\Validator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\GetSetMethodNormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Nelmio\ApiDocBundle\Annotation\Model;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations\View;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use FOS\RestBundle\Controller\Annotations as Rest;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class EventController extends FOSRestController
{
    const TYPE = 'type';
    const PERSO = 'perso';
    private $validator;
    private $mailer;
    private $token;
    private $userManager;
    private $eventService;

    public function __construct(ValidatorInterface $validator, \Swift_Mailer $mailer, Security $token,
                                UserManagerInterface $userManager, EventService $eventService)
    {
        $this->eventService = $eventService;
        $this->validator = $validator;
        $this->mailer = $mailer;
        $this->token = $token;
        $this->userManager = $userManager;

    }

    /**
     * @Rest\Post("/events")
     * @SWG\Post(
     *     consumes={"application/json"},
     *     produces={"App\Entity\Event"},
     *     @SWG\Parameter(
     *          name="event",
     *          in="body",
     *          description="event",
     *          required=true,
     *          @Model(type=App\Entity\DTO\EventRequest::class),
     *          @SWG\Schema(ref="#\App\Entity\DTO\EventRequest")
     *      ),
     *     @SWG\Response(
     *          response=200,
     *          description="L'event a bien été créé",
     *          @Model(type=App\Entity\Event::class),
     *          @SWG\Schema(ref="#\App\Entity\Event")
     *         ),
     *     ),
     * )
     * @ParamConverter("eventRequest", converter="fos_rest.request_body")
     */
    public function createEventAction(EventRequest $eventRequest)
    {
        $event = $this->eventService->updateEvent($this->getUser(), $eventRequest);
        $errors = $this->validator->validate($event);
        if (count($errors) > 0) {
            return new JsonResponse(array("message"=> array("message" => "Tous les champs obligatoires n'ont pas été remplis.")), 312);
        }else{
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($event);
            $entityManager->flush();
            $this->sendEmail();
            $view = $this->view($event, 200);
            return $this->handleView($view);
        }
    }

    /**
     * @Rest\Get(
     *     "/events",
     *      name="get_matching_events",
     *     defaults={
     *     "type": "perso",
     *     "upcoming": "true"
     *     },
     *     requirements={"type"="perso|invite", "upcoming"="true|false"})
     * @SWG\Get(
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="type",
     *          in="query",
     *          description="type",
     *          required=true,
     *          type="string"
     *      ),
     *     @SWG\Parameter(
     *          name="upcoming",
     *          in="query",
     *          description="upcoming",
     *          required=true,
     *          type="boolean"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Donne les events correspondants à la recherche",
     *          @Model(type=App\Entity\Event::class),
     *          @SWG\Schema(ref="#\App\Entity\Event")
     *         ),
     *     ),
     * )
     * @throws \Exception
     */
    public function getEventsAction(Request $request){
        $type = $request->query->get(self::TYPE, self::PERSO);
        $upcoming = $request->query->getBoolean('upcoming', true);
        $events = $this->eventService->getMatchingEvents($this->getUser(), $type, $upcoming);
        $view = $this->view($events, 200);
        return $this->handleView($view);
    }

    /**
     * @Rest\Get("/admin/events/{id}", name="get_event_as_admin", requirements={"id"="\d+"})

     * @SWG\Get(
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="id",
     *          in="query",
     *          description="id",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Donne l'event correspondant à la recherche",
     *          @Model(type=App\Entity\Event::class),
     *          @SWG\Schema(ref="#\App\Entity\Event")
     *         ),
     *     ),
     * )
     *
     */
    public function getEventAsAdminAction($id){
        $event = $this->getDoctrine()
            ->getRepository(Event::class)
            ->find($id);
        if (is_null($event)){
            return new JsonResponse($event, 404);
        }
        $view = $this->view($event, 200);
        return $this->handleView($view);

    }

    /**
     * @Rest\Get("/admin/events", name="get_all_events")

     * @SWG\Get(
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="type",
     *          in="query",
     *          description="type",
     *          required=true,
     *          type="string"
     *      ),
     *     @SWG\Parameter(
     *          name="upcoming",
     *          in="query",
     *          description="upcoming",
     *          required=true,
     *          type="string"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Donne les events correspondants à la recherche",
     *          @Model(type=App\Entity\Event::class),
     *          @SWG\Schema(ref="#\App\Entity\Event")
     *         ),
     *     ),
     * )
     */
    public function getAllEventsAction(){
        $events = $this->getDoctrine()
            ->getRepository(Event::class)
            ->findAll();
        $view = $this->view($events, 200);
        return $this->handleView($view);
    }

    /**
     * @Rest\Post("/admin/events/{id}", name="update_event", methods={"POST"}, requirements={"id"="\d+"})
     * @SWG\Post(
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="type",
     *          in="body",
     *          description="type",
     *          required=true,
     *          @Model(type=App\Entity\EventRequest::class),
     *          @SWG\Schema(ref="#\App\Entity\EventRequest")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Donne l'event mis à jour",
     *          @Model(type=App\Entity\Event::class),
     *          @SWG\Schema(ref="#\App\Entity\Event")
     *         ),
     *     ),
     * )
     * @ParamConverter("eventRequest", converter="fos_rest.request_body")
     */
    public function updateEventAction(EventRequest $eventRequest){
        if (is_null($eventRequest->getCreator())){
            return new JsonResponse(array("message"=> array("message" => "Il manque un créateur.")), 400);
        }
        $creator = $this->getDoctrine()->getRepository(User::class)->find($eventRequest->getCreator());
        $event = $this->eventService->updateEvent($creator, $eventRequest);
        $errors = $this->validator->validate($event);
        if (count($errors) > 0) {
            return new JsonResponse(array("message"=> array("message" => "Tous les champs obligatoires n'ont pas été remplis.")), 400);
        }else{
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->merge($event);
            $entityManager->flush();

            $this->sendEmail();
            $view = $this->view($event, 200);
            return $this->handleView($view);
        }
    }

    /**
     * @Rest\Post("/events/{id}", name="update_perso_event", methods={"POST"}, requirements={"id"="\d+"})
     * @ParamConverter("eventRequest", converter="fos_rest.request_body")
     * @SWG\Post(
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="type",
     *          in="body",
     *          description="type",
     *          required=true,
     *          @Model(type=App\Entity\DTO\EventRequest::class),
     *          @SWG\Schema(ref="#\App\Entity\DTO\EventRequest")
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Donne l'événément mis à jour ",
     *          @Model(type=App\Entity\Event::class),
     *          @SWG\Schema(ref="#\App\Entity\Event")
     *         ),
     *     ),
     * )
     */
    public function updatePersoEventAction(EventRequest $eventRequest){
        $user = $this->getUser();
        if (!is_null($eventRequest->getCreator()) && $eventRequest->getCreator() == $user->getUsername()){
            $event = $this->eventService->updateEvent($user, $eventRequest);
            $errors = $this->validator->validate($event);
            if (count($errors) > 0) {
                return new JsonResponse(array("message"=> array("message" => "Tous les champs obligatoires n'ont pas été remplis.")), 400);
            } else {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->merge($event);
                $entityManager->flush();

                $this->sendEmail();
                $view = $this->view($event, 200);
                return $this->handleView($view);
            }
        } else {
            throw new UnauthorizedHttpException("Vous n'avez pas les droits pour modifier cet événement.");
        }
    }

    /**
     * @Rest\Get("/events/{id}", name="get_event", methods={"GET"}, requirements={"id"="\d+"})
     * @SWG\Get(
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *          name="id",
     *          in="query",
     *          description="id",
     *          required=true,
     *          type="integer"
     *      ),
     *      @SWG\Response(
     *          response=200,
     *          description="Donne l'event portant le même id",
     *          @Model(type=App\Entity\Event::class),
     *          @SWG\Schema(ref="#\App\Entity\Event")
     *         ),
     *     ),
     * )
     *
     */
    public function getEventAction($id){
        $event = $this->getDoctrine()
            ->getRepository(Event::class)
            ->find($id);
        $view = $this->view($event, 200);
        return $this->handleView($view);
    }

    public function sendEmail(){
        //TODO
    }

}