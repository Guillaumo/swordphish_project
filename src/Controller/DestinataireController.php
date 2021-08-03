<?php

namespace App\Controller;

use App\Entity\ResultCampaignUser;
use App\Form\ResultFormType;
use App\Repository\CampagneRepository;
use App\Repository\DestinataireRepository;
use App\Repository\ResultCampaignUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class DestinataireController extends AbstractController
{
    // /**
    //  * @Route("/destinataire", name="destinataire")
    //  */
    // public function index(): Response
    // {
    //     return $this->render('destinataire/index.html.twig', [
    //         'controller_name' => 'DestinataireController',
    //     ]);
    // }

    /**
     * @Route("/destinataire/{id_c}/{id_d}", name="destinataire_user")
     */
    public function landingPage($id_c,$id_d,CampagneRepository $campagneRepository,DestinataireRepository $destinataireRepository,EntityManagerInterface $entityManagerInterface, ResultCampaignUserRepository $resultCampaignUserRepository, Request $request): Response
    {
        // On récupère les objets campagne et destinataire correspondants aux id transmis
        $campagne = $campagneRepository->findOneBy(['id' => $id_c]);
        $destinataire = $destinataireRepository->findOneBy(['id' => $id_d]);

        //******************************************************************************
        // On prend l' ip de la meilleure manière qu'il soit
        //******************************************************************************
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        //******************************************************************************
        // Navigateur
        //******************************************************************************
        $nav = 'Inconnu';
        $browser = array('Internet Explorer' => 'MSIE', 'Microsoft Edge' =>'Edg', 'Firefox', 'Mozilla', 'Netscape', 'Chrome','Safari', 'Konqueror', 'Epiphany', 'Lynx', 'Opera');
        foreach ($browser as $cle => $val) {
            if (strpos($_SERVER['HTTP_USER_AGENT'], $val)) {
                if (is_numeric($cle)) {
                    $nav = $val;
                } else {
                    $nav = $cle;
                }
                break;
            }
        }

        //******************************************************************************
        // Nom d'hôte
        //******************************************************************************
        $host = gethostbyaddr($ip);

        //******************************************************************************
        // Nom de session Windows
        //******************************************************************************
        $username=shell_exec("echo %username%" );

        // On crée l'objet de la class ResultCampaignUser correspondant
        if ($resultCampaignUser = $resultCampaignUserRepository -> findOneBy(['campagne' => $campagne, 'destinataire' => $destinataire])) {
            $resultCampaignUser = $resultCampaignUserRepository -> findOneBy(['campagne' => $campagne, 'destinataire' => $destinataire]);

        } else
        {
            $resultCampaignUser = new ResultCampaignUser;
        }

        $form = $this->createForm(ResultFormType::class, $resultCampaignUser);
        $form->handleRequest($request); 
        
        $resultCampaignUser -> setUserip($ip);
        $resultCampaignUser -> setUsername($username);
        $resultCampaignUser -> setHostname($host);
        $resultCampaignUser -> setNavigator($nav);
        $resultCampaignUser -> setCreatedAt(new \DateTime());
        $resultCampaignUser -> setCampagne($campagne);
        $resultCampaignUser -> setDestinataire($destinataire);
        // Mise en BD
        $entityManagerInterface -> persist($resultCampaignUser);
        $entityManagerInterface -> flush();

        if (isset($_POST['submit'])) {
            $resultCampaignUser->setLastname($_POST['lastname']);
            $resultCampaignUser->setFirstname($_POST['firstname']);
            $resultCampaignUser->setTelephone($_POST['telephone']);
            $resultCampaignUser->setEmail($_POST['email']);
            $tickets = $_POST['tickets'];

            // Mise en BD
            $entityManagerInterface -> persist($resultCampaignUser);
            $entityManagerInterface -> flush();

            return $this->render('welcome/index.html.twig', [
                'result_campaign_user' => $resultCampaignUser,
                'tickets' => $tickets,
            ]);
        }

        return $this->render('destinataire/index.html.twig', [
            'result_campaign_user' => $resultCampaignUser,
            'id_campagne' => $resultCampaignUser->getCampagne()->getId(),
            'id_destinataire' => $resultCampaignUser->getDestinataire()->getId(),
            // 'form_destinataire' => $form->createView(),
        ]);

    }

}
