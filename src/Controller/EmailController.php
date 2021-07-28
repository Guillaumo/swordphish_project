<?php

namespace App\Controller;

use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Address;
use App\Repository\CampagneRepository;
use App\Repository\DestinataireRepository;
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
     * @Route("/admin/email/test", name="admin_email_test")
     */
    public function sendEmailTest(MailerInterface $mailer): Response
    {
        // création d'un nouvel email pour le test d'envoi
        $email = (new Email())
            ->from(Address::create('Catherine Frot <catherine.frot@abalone.com>'))
            ->to('wangtseming@gmail.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Time for Symfony Mailer!')
            ->text('Sending emails is fun again!')
            ->html('<p>See Twig integration for better HTML integration!</p>');

        $mailer->send($email);

        return $this->redirectToRoute('admin');
    }

    /**
     * @Route("/admin/email/envoi/{uid}", name="admin_email_envoi")
     */
    public function sendEmail($uid, CampagneRepository $campagneRepository, DestinataireRepository $destinataireRepository, MailerInterface $mailer): Response
    {
        // on récupère l'objet de la campagne sélectionnée pour l'envoi
        $campagne = $campagneRepository->findOneBy(['id' => $uid]);
        // on récupère les destinataires de la campagne sélectionnée rangés aléatoirement
        $destinataires = $destinataireRepository->findByCampagneFieldRandom([$campagne]);
        // Nombre de destinataires à envoyer dans le même interval de temps
        $nb_destinataires = 2;
        // Interval de temps en minutes
        $interval = 1;
        // Longueur du tableau des destinataires
        $count = count($destinataires);
        // On boucle sur l'ensemble des destinataires pour former les groupes d'envoi
        while ($count>0) {
            // on sélectionne un groupe de destinataires
            $group_dest = array_slice($destinataires,0,$nb_destinataires);
            
            // pour test
            $tab_address =[];

            // echo '<pre>adresses groupe : ';
            // var_dump($tab_address);
            // echo '</pre>';
            // die;
            
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

                try
                {
                    $mailer->send($email);
                }
                catch (TransportExceptionInterface $mailerException)
                {
                    throw $mailerException;
                }
                
                // pour test
                array_push($tab_address,$address);
            
            }

            if(count($group_dest)>=$count)
            {
                break;
            }
            // On récupére le tableau de destinataires restants
            $destinataires = array_values(array_diff($destinataires,$group_dest));
            // on vérifie la longueur du tableau de destinataires restants
            $count = count($destinataires);

            // On suspend le script avant de passer au groupe suivant, en nombre de secondes
            sleep($interval*60);

            // pour test
            // echo '<pre>adresses groupe : ';
            // var_dump($tab_address);
            // echo '</pre>';
            // die;
        }


        return $this->redirectToRoute('admin');
    }
}
