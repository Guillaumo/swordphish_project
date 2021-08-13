<?php

namespace App\Controller;

use App\Form\ResultFormType;
use App\Entity\ResultCampaignUser;
use Symfony\Component\Mime\Address;
use App\Repository\CampagneRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DestinataireRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\ResultCampaignUserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

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
    public function landingPage($id_c, $id_d, CampagneRepository $campagneRepository, DestinataireRepository $destinataireRepository, EntityManagerInterface $entityManagerInterface, ResultCampaignUserRepository $resultCampaignUserRepository, Request $request): Response
    {

        //******************************************************************************
        // Traitement des infos part 1
        //******************************************************************************

        // On récupère les objets campagne et destinataire correspondants aux id transmis
        $campagne = $campagneRepository->findOneBy(['id' => $id_c]);
        $destinataire = $destinataireRepository->findOneBy(['id' => $id_d]);

        // On crée l'objet de la class ResultCampaignUser correspondant
        if ($resultCampaignUser = $resultCampaignUserRepository->findOneBy(['campagne' => $campagne, 'destinataire' => $destinataire])) {
            $resultCampaignUser = $resultCampaignUserRepository->findOneBy(['campagne' => $campagne, 'destinataire' => $destinataire]);
        } else {
            $resultCampaignUser = new ResultCampaignUser;
        }

        // $form = $this->createForm(ResultFormType::class, $resultCampaignUser);
        // $form->handleRequest($request);

        // Si soumission du formulaire
        if (isset($_POST['submit'])) {
            // Mise à jour de l'objet de la class ResultCampaignUser avec les infos du formulaire
            $resultCampaignUser->setLastname($_POST['lastname']);
            $resultCampaignUser->setFirstname($_POST['firstname']);
            $resultCampaignUser->setTelephone($_POST['telephone']);
            $resultCampaignUser->setEmail($_POST['email']);
            $tickets = $_POST['tickets'];

            // Envoi de la page pour validation du formulaire
            return $this->render('destinataire/validationForm.html.twig', [
                'firstname' => $resultCampaignUser->getFirstname(),
                'lastname' => $resultCampaignUser->getFirstname(),
                'tickets' => $tickets,
            ]);
        }


        //******************************************************************************
        // Récupération les infos d'environnement du destinataire qui a cliqué
        //******************************************************************************

        // On prend l'ip de la meilleure manière qu'il soit
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        // Navigateur
        $nav = 'Inconnu';
        $browser = array('Internet Explorer' => 'MSIE', 'Microsoft Edge' => 'Edg', 'Firefox', 'Mozilla', 'Netscape', 'Chrome', 'Safari', 'Konqueror', 'Epiphany', 'Lynx', 'Opera');
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

        // Nom de la machine et nom de domaine
        $host = gethostbyaddr($ip);

        // Nom de session Windows
        $username = shell_exec("echo %username%");
        //******************************************************************************
        //******************************************************************************


        //******************************************************************************
        // Traitement des infos part 2
        //******************************************************************************

        // Mise à jour de l'objet de la class ResultCampaignUser avec les infos d'environnement
        $resultCampaignUser->setUserip($ip);
        $resultCampaignUser->setUsername($username);
        $resultCampaignUser->setHostname($host);
        $resultCampaignUser->setNavigator($nav);
        $resultCampaignUser->setCreatedAt(new \DateTime());
        $resultCampaignUser->setCampagne($campagne);
        $resultCampaignUser->setDestinataire($destinataire);
        // Mise en BD
        $entityManagerInterface->persist($resultCampaignUser);
        $entityManagerInterface->flush();
        // }

        return $this->render('destinataire/index.html.twig', [
            'id_campagne' => $id_c,
            'id_destinataire' => $id_d,
            // 'form_destinataire' => $form->createView(),
        ]);
    }

    /**
     * @Route("/destinataire/test/{id_c}/{id_d}", name="destinataire_test")
     */
    public function landingTest($id_c, $id_d, MailerInterface $mailer)
    {

        //******************************************************************************
        // Traitement des infos
        //******************************************************************************

        // Si soumission du formulaire
        if (isset($_POST['submit'])) {

            //******************************************************************************
            // Récupération les infos d'environnement du destinataire qui a cliqué
            //******************************************************************************

            // On prend l'ip de la meilleure manière qu'il soit
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $ip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $ip = $_SERVER['REMOTE_ADDR'];
            }

            // Navigateur
            $nav = 'Inconnu';
            $browser = array('Internet Explorer' => 'MSIE', 'Microsoft Edge' => 'Edg', 'Firefox', 'Mozilla', 'Netscape', 'Chrome', 'Safari', 'Konqueror', 'Epiphany', 'Lynx', 'Opera');
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

            // Nom de la machine et nom de domaine
            $host = gethostbyaddr($ip);

            // Nom de session Windows
            $username = shell_exec("echo %username%");
            //******************************************************************************
            //******************************************************************************

            $result = [
                'userip'=> $ip,
                'username' => $username,
                'hostname' => $host,
                'navigator' => $nav,
                'lastname' => $_POST['lastname'],
                'firstname' => $_POST['firstname'],
                'telephone' => $_POST['telephone'],
                'email' => $_POST['email'],
            ];
            $result = (object) $result;

            // Envoi du mail d'infos
            // création d'un nouvel email pour l'envoi
            $email = (new TemplatedEmail())
                ->from(Address::create('Geoffrey VIEMONT <Geoffrey.VIEMONT@impro-solutions.fr>'))
                ->to($id_d)
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                ->priority(TemplatedEmail::PRIORITY_HIGH)
                ->subject('Information phishing')
                // ->text('Sending emails is fun again!')
                // ->html('<p>Message from '.$destinataire.' pour la campagne '.$campagne -> getName().'</p>')
                ->htmlTemplate('email/infos.html.twig')
                ->context([
                    'result_campaign_user' => $result,
                ]);

            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $mailerException) {
                throw $mailerException;
            }

            // Envoi de la page de validation du formulaire
            return
                $this->render('destinataire/validationForm.html.twig', [
                    'firstname' => $_POST['firstname'],
                    'lastname' => $_POST['lastname'],
                    'tickets' => $_POST['tickets'],
                ]);
        }

        return $this->render('destinataire/index.html.twig', [
            'id_campagne' => $id_c,
            'id_destinataire' => $id_d,
        ]);
    }
}
