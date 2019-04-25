<?php
/**
 * Created by PhpStorm.
 * User: jpbella
 * Date: 14/02/19
 * Time: 11:18
 */

namespace App\Service;



use App\Entity\DTO\EventRequest;
use App\Entity\Event;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Collection;

class EventService
{
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    const PERSO = "perso";
    const INVITE = "invite";

    /**
     * Méthode utilisée dans l'eventController pour récupérer les évents correspondant à la demande d'un utilisateur $user non admin
     * selon les critères $type et $isUpcoming suivant:
     * @param User $user est l'utilisateur à partir duquel on récupère les événements. Il va donc déterminer le point de vue à partir duquel on saura si les événements considérés sont à considérer comme étant de type "perso" ou "invite"
     * @param string $type (Valeurs possibles : "perso" et "invite") indique si les événements cherchés sont soit "perso" c'est à dire que l'utilisateur a créé l'événement soit "invite" dans ce cas, il y a été simplement invité.
     * @param bool $isUpcoming indique si l'événement est à venir ou est déjà passé
     * @return \Doctrine\Common\Collections\ArrayCollection|\Doctrine\Common\Collections\Collection|mixed|null
     */
    public function getMatchingEvents(User $user, string $type, bool $isUpcoming){
        switch ( $type ){
            case self::PERSO:
                return $user->getPersoEventsWithDeadline($isUpcoming);
            case self::INVITE:
                return $user->getInvitedEventsWithDeadline($isUpcoming);
            default:
                return null;
        }
    }

    public function updateEvent(User $creator, EventRequest $eventRequest){
        $userRepo = $this->em->getRepository(User::class);
        $guests = new ArrayCollection();
        foreach ($eventRequest->getGuests() as $guestId){
            $guests->add($userRepo->find($guestId));
        }

        $event = new Event();
        $event->setEnablesMultipleVotes($eventRequest->getEnablesMultipleVotes());
        $event->setIsAnswerVisible($eventRequest->getisAnswerVisible());
        $event->setDates($eventRequest->getDates());
        $event->setCreator($creator);
        $event->setDeadline($eventRequest->getDeadline());
        $event->setDescription($eventRequest->getDescription());
        $event->setGuests($guests);
        $event->setLocation($eventRequest->getLocation());
        $event->setTitle($eventRequest->getTitle());
        return $event;

    }
}