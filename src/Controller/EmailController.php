<?php

namespace App\Controller;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use App\Repository\CampagneRepository;
use App\Repository\DestinataireRepository;
use App\Repository\ResultCampaignUserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;

class EmailController extends AbstractController
{
    // /**
    //  * @Route("/email", name="email")
    //  */
    // public function index(): Response
    // {
    //     return $this->render('email/index.html.twig', [
    //         'controller_name' => 'EmailController',
    //     ]);
    // }

    /**
     * @Route("/admin/email/test/{uid}", name="admin_email_test")
     */
    public function sendEmailTest($uid, CampagneRepository $campagneRepository, MailerInterface $mailer): Response
    {
        // On récupère l'objet campagne correspondant à l'id transmis
        $campagne = $campagneRepository->findOneBy(['id' => $uid]);
        if (isset($_POST['submit'])) {
            // récupération des adresses emails pour le test d'envoi
            $addresses = explode(',', $_POST['email_address']);
            foreach ($addresses as $address) {
                // création d'un nouvel email pour le test d'envoi
                $email = (new TemplatedEmail())
                    ->from(Address::create('Catherine Frot <catherine.frot@abalone.com>'))
                    ->to($address)
                    //->cc('cc@example.com')
                    //->bcc('bcc@example.com')
                    //->replyTo('fabien@example.com')
                    ->priority(TemplatedEmail::PRIORITY_HIGH)
                    ->subject('Gagner des tickets de cinéma !')
                    // ->text('Sending emails is fun again!')
                    // ->html('<p>Message from '.$destinataire.' pour la campagne '.$campagne -> getName().'</p>')
                    ->htmlTemplate('email/index.html.twig')
                    ->context([
                        'id_campagne' => $uid,
                        'id_destinataire' => $address,
                        'isTest' => true,
                    ]);

                $mailer->send($email);
            }

            return $this->redirectToRoute('admin');
        }

        return $this->render('admin/index.html.twig', [
            'campagne' => $campagne,
            'test_envoi' => true,
        ]);
    }

    /**
     * @Route("/admin/email/campagne/{uid}", name="admin_email_campagne")
     */
    public function sendEmailCampagne($uid, CampagneRepository $campagneRepository, DestinataireRepository $destinataireRepository, MailerInterface $mailer, EntityManagerInterface $em): Response
    {
        
        // on récupère l'objet de la campagne sélectionnée pour l'envoi
        $campagne = $campagneRepository->findOneBy(['id' => $uid]);
        // on récupère les destinataires de la campagne sélectionnée
        $destinataires = $destinataireRepository->findByCampagneField([$campagne]);
        // Nombre de destinataires à envoyer dans le même interval de temps
        $nb_destinataires = $campagne->getNumberRecipientsPerGroup();
        // Interval de temps en minutes entre chaque envoi de groupes
        $interval = $campagne->getTempoMinutes();
        // Longueur du tableau des destinataires
        $count = count($destinataires);
        // On boucle sur l'ensemble des destinataires pour former les groupes d'envoi
        $groups = [];
        while ($count > 0) {
            // on sélectionne un groupe de destinataires
            $group_dest = array_slice($destinataires, 0, $nb_destinataires);
            array_push($groups, $group_dest);
            if (count($group_dest) >= $count) {
                break;
            }
            // On récupére le tableau de destinataires restants
            $destinataires = array_values(array_diff($destinataires, $group_dest));
            // on vérifie la longueur du tableau de destinataires restants
            $count = count($destinataires);
        }

        // on récupère l'index du groupe de destinataires pour l'envoi à partir du script js
        if (isset($_POST['index'])) {
            $index = $_POST['index'];
        } else {
            $index = 0;
        }

        // Envoi email au groupe de destinataires
        foreach ($groups[$index] as $destinataire) {
            // on récupère l'adresse email du destinataire
            $address = $destinataire->getEmail();

            // création d'un nouvel email pour l'envoi vers chaque destinataire
            $email = (new TemplatedEmail())
                ->from(Address::create('Catherine Frot <catherine.frot@abalone.com>'))
                ->to($address)
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                ->priority(TemplatedEmail::PRIORITY_HIGH)
                ->subject('Gagner des tickets de cinéma !')
                // ->text('Sending emails is fun again!')
                // ->html('<p>Message from '.$destinataire.' pour la campagne '.$campagne -> getName().'</p>')
                ->htmlTemplate('email/index.html.twig')
                ->context([
                    'id_campagne' => $uid,
                    'id_destinataire' => $destinataire->getId(),
                    'isTest' => false,
                ]);

            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $mailerException) {
                throw $mailerException;
            }
        }

        // si tous les groupes de destinataires sont envoyés
        if ($index == count($groups[$index]) - 1) {

            // on met à jour le champ isSent de la campagne à true
            $campagne->setIsSent(true);
            $em->persist($campagne);
            $em->flush();
        }

        return $this->render('admin/index.html.twig', [
            'campagne' => $campagne,
            'index' => $index + 1,
            'index_max' => count($groups) - 1,
            'counter' => $interval*60, // pour voir le décompte en seconde avant l'envoi suivant
        ]);
    }

    /**
     * @Route("/admin/email/infos/{uid}", name="admin_email_infos")
     */
    public function sendEmailInfos($uid, CampagneRepository $campagneRepository, MailerInterface $mailer, EntityManagerInterface $em): Response
    {
        // on récupère l'objet de la campagne sélectionnée pour l'envoi
        $campagne = $campagneRepository->findOneBy(['id' => $uid]);
        // on récupère les résultats de la campagne lancée
        $results = $campagne->getResults();

        // Envoi email aux destinataires ayant au moins cliqué sur le lien
        foreach ($results as $result) {
            // on récupère l'adresse email du destinataire
            $address = $result->getDestinataire()->getEmail();

            // création d'un nouvel email pour l'envoi vers chaque destinataire
            $email = (new TemplatedEmail())
                ->from(Address::create('Fabrice COUPRIE <Fabrice.COUPRIE@abalone-group.com>'))
                ->to($address)
                //->cc('cc@example.com')
                //->bcc('bcc@example.com')
                //->replyTo('fabien@example.com')
                ->priority(TemplatedEmail::PRIORITY_HIGH)
                ->subject('Vos places de cinéma')
                ->htmlTemplate('email/infos.html.twig')
                ->context([
                    'result_campaign_user' => $result,
                ]);

            try {
                $mailer->send($email);
            } catch (TransportExceptionInterface $mailerException) {
                throw $mailerException;
            }
        }


        // on met à jour le champ isInfoSent de la campagne à true
        $campagne->setIsInfoSent(true);
        $em->persist($campagne);
        $em->flush();

        return $this->render('admin/index.html.twig', [
            'campagne' => $campagne,
            'results' => $results,
        ]);
    }

    /**
     * @Route("/admin/test/infos/{uid}", name="admin_test_infos")
     */
    public function sendTestInfos($uid, CampagneRepository $campagneRepository, MailerInterface $mailer, EntityManagerInterface $em): Response
    {
        return $this->redirectToRoute('admin');
    }
}
