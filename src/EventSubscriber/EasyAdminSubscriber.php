<?php

namespace App\EventSubscriber;

use App\Entity\Campagne;
use App\Repository\DestinataireRepository;
use EasyCorp\Bundle\EasyAdminBundle\Event\BeforeEntityPersistedEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
// use Symfony\Component\HttpKernel\Event\ControllerEvent;

class EasyAdminSubscriber implements EventSubscriberInterface
{
    // public function onControllerEvent(ControllerEvent $event)
    // {
    //     // ...
    // }

    public static function getSubscribedEvents()
    {
        return [
            // ControllerEvent::class => 'onControllerEvent',
            BeforeEntityPersistedEvent::class => 'setCampagneDate',
        ];
    }

    public function setCampagneDate(BeforeEntityPersistedEvent $event, DestinataireRepository $destinataireRepository)
    {
        $entity = $event->getEntityInstance();

        if(!($entity instanceof Campagne))
        {
            return;
        }
        
        $destinataires = $destinataireRepository->findAll();
        $now = new \DateTime();
        $entity->setDate($now);
        foreach ($destinataires as $destinataire)
        {
            $entity->addDestinataire($destinataire);
        }
        
    }
}
