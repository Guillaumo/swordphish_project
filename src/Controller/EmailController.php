<?php

namespace App\Controller;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use App\Repository\CampagneRepository;
use App\Repository\DestinataireRepository;
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
                    ->subject('Test d\'envoi mailing')
                    ->text('Sending emails is fun again!')
                    ->htmlTemplate('email/index.html.twig')
                    // ->context([
                    //     'id_campagne' => $uid,
                    // ])
                    ;

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
    public function sendEmail($uid, CampagneRepository $campagneRepository, DestinataireRepository $destinataireRepository, MailerInterface $mailer, EntityManagerInterface $em): Response
    {
        ini_set('max_execution_time', 0);
        // on récupère l'objet de la campagne sélectionnée pour l'envoi
        $campagne = $campagneRepository->findOneBy(['id' => $uid]);
        // on récupère les destinataires de la campagne sélectionnée rangés aléatoirement
        $destinataires = $destinataireRepository->findByCampagneFieldRandom([$campagne]);
        // Nombre de destinataires à envoyer dans le même interval de temps
        $nb_destinataires = 2;
        // Interval de temps en minutes
        $interval = 2;
        // Longueur du tableau des destinataires
        $count = count($destinataires);
        // On boucle sur l'ensemble des destinataires pour former les groupes d'envoi
        while ($count > 0) {
            // on sélectionne un groupe de destinataires
            $group_dest = array_slice($destinataires, 0, $nb_destinataires);

            // Envoi email au groupe de destinataires
            foreach ($group_dest as $destinataire) {
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
                    ]);

                try {
                    $mailer->send($email);
                } catch (TransportExceptionInterface $mailerException) {
                    throw $mailerException;
                }
            }

            if (count($group_dest) >= $count) {
                break;
            }
            // On récupére le tableau de destinataires restants
            $destinataires = array_values(array_diff($destinataires, $group_dest));
            // on vérifie la longueur du tableau de destinataires restants
            $count = count($destinataires);

            // On suspend le script avant de passer au groupe suivant, en nombre de secondes
            sleep($interval * 60);

        }

        // on met à jour le champ isSent de la campagne à true
        $campagne->setIsSent(true);
        $em->persist($campagne);
        $em->flush();

        return $this->render('admin/index.html.twig', [
            'test_envoi' => false,
            'stat' => false,
            'campagne' => $campagne,
            'num_dest' => count($destinataireRepository->findByCampagneField([$campagne])),
        ]);
    }
}
