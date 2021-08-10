<?php

namespace App\Controller;

use App\Entity\ResultCampaignUser;
use App\Repository\CampagneRepository;
use App\Repository\ResultCampaignUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatController extends AbstractController
{
    // /**
    //  * @Route("/stat", name="stat")
    //  */
    // public function index(): Response
    // {
    //     return $this->render('stat/index.html.twig', [
    //         'controller_name' => 'StatController',
    //     ]);
    // }

    /**
     * @Route("/admin/stat/{uid}", name="admin_stat")
     */
    public function stat($uid, CampagneRepository $campagneRepository, ResultCampaignUserRepository $resultCampaignUser): Response
    {
        // On récupère l'objet campagne correspondant à l'id transmis
        $campagne = $campagneRepository->find($uid);
        // On récupère les objets result_campaign_user correspondant à la campagne
        $result_clicks = $resultCampaignUser->findBy(['campagne' => $campagne]);

        // Nombre d'envois d'emails
        if (!$campagne->getIsSent()) {
            $this->addFlash('error', "Pas de statistiques possibles. La campagne ".$campagne->getName()." n'a pas encore été envoyée.");
            return $this->redirectToRoute('admin');
        }
        $num_emails = count($campagne->getDestinataires());

        // Nombre de liens cliqués
        $num_clicks = count($result_clicks);

        // Parmi les liens cliqués, ceux dont le formulaire n'a pas été soumis
        $result_notSubmit = $resultCampaignUser->findBy(['campagne' => $campagne, 'lastname' => null]);
        // Nombre de formulaires soumis pari les liens cliqués
        $num_submits = $num_clicks - count($result_notSubmit);

        // Envoi des data à la vue
        return $this->render('admin/index.html.twig',[
            'stat' => true,
            'test_envoi' => false,
            'campaign' => $campagne,
            'data_clicks' => json_encode([$num_emails,$num_clicks]),
            'data_submits' => json_encode([$num_clicks, $num_submits]),
        ]);
        // return $this->redirectToRoute('admin');
    }
}
