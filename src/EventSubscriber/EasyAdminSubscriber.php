<?php

namespace App\EventSubscriber;

use App\Entity\Campagne;
use App\Repository\DestinataireRepository;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
// use Symfony\Component\HttpKernel\Event\ControllerEvent;

class EasyAdminSubscriber implements EventSubscriberInterface
{

    private $destinataireRepository;

    /**
     * fonction constructeur: initialiser le repository de Destinataire à l'appel d'une instance de la classe
     *
     * @param DestinataireRepository $destinataireRepository
     */
    public function __construct(DestinataireRepository $destinataireRepository)
    {
        $this->destinataireRepository = $destinataireRepository;
    }

    /**
     * Fonction qui définit un tableau d'événements
     *
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return [
            // ControllerEvent::class => 'onControllerEvent',
            BeforeEntityPersistedEvent::class => 'setCampagneDate',
        ];
    }

    /**
     * Fonction permettant de fixer les valeurs de date et destinataires de l'entité campagne
     * avant l'événement de création en BD
     *
     * @param BeforeEntityPersistedEvent $event
     * @return void
     */
    public function setCampagneDate(BeforeEntityPersistedEvent $event)
    {
        $entity = $event->getEntityInstance();

        if(!($entity instanceof Campagne))
        {
            return;
        }
        
        $destinataires = $this->destinataireRepository->findAll();
        $now = new \DateTime();
        $entity->setDate($now);
        foreach ($destinataires as $destinataire)
        {
            $entity->addDestinataire($destinataire);
        }
        
    }
}
