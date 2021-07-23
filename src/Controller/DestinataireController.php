<?php

namespace App\Controller;

use App\Entity\ResultCampaignUser;
use App\Repository\CampagneRepository;
use App\Repository\DestinataireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class DestinataireController extends AbstractController
{
    /**
     * @Route("/destinataire", name="destinataire")
     */
    public function index(): Response
    {
        return $this->render('destinataire/index.html.twig', [
            'controller_name' => 'DestinataireController',
        ]);
    }

    /**
     * @Route("/destinataire/{id_c}/{id_d}", name="destinataire_user")
     */
    public function landingPage($id_c,$id_d,CampagneRepository $campagneRepository,DestinataireRepository $destinataireRepository,EntityManagerInterface $entityManagerInterface): Response
    {
        // On récupère les objets camapagne et destinataire correspondants aux id transmis
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
        $resultCampaignUser = new ResultCampaignUser;
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

        return $this->render('destinataire/index.html.twig', [
            'controller_name' => 'DestinataireController',
            'id_destinataire' => $id_d,
            'ip' => $ip,
            'host' => $host,
            'nav' => $nav,
            'username' => $username,
        ]);

    }

}
