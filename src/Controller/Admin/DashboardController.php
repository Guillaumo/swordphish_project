<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Entity\Campagne;
use App\Entity\Destinataire;
use App\Entity\ResultCampaignUser;
use App\Repository\AdminRepository;
use App\Repository\CampagneRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\DestinataireRepository;
use App\Repository\ResultCampaignUserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig');
    }

    /**
     * @Route("/admin/import/destinataires", name="import_destinataires")
     */
    public function importFileDestinataires(DestinataireRepository $destinataireRepository, ResultCampaignUserRepository $resultCampaignUserRepository, EntityManagerInterface $em)
    {
        $error = '';
        $success = '';
        $old_destinataire_result = [];

        if (isset($_POST['submit_file'])) {
            $file = $_FILES['file']['name'];
            $uploadDirectory = dirname(__DIR__, 3) . "/public/upload/";
            $uploadFile = $uploadDirectory . basename($file);
            if (!move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile)) {
                $error = 'Votre fichier n\'a pas pu être importé. Recommencer.';
            }

            move_uploaded_file($_FILES['file']['tmp_name'], $uploadFile);
            $success = 'Votre fichier a bien été importé. La BD a été mise à jour.';

            // lecture du fichier csv
            $handle = fopen($uploadFile, 'r');
            // on récupère les éléments du fichier dans un tableau
            $lines = [];
            while ($line = fgetcsv($handle, 0, ';')) {
                $lines[] = $line;
            }
            // on enlève la ligne des champs
            array_shift($lines);
            // on mélange le tableau
            shuffle($lines);

            // Insertion en BD la liste des destinataires
            foreach ($lines as $line) {
                $destinataire = $destinataireRepository->findOneBy(['email' => $line[6]]);
                // si le destinataire n'existe pas déjà
                if (is_null($destinataire)) {
                    $destinataire = new Destinataire;
                    $destinataire->setEmail($line[6]);
                    $destinataire->setOffice($line[4]);
                    $firstname = strstr($line[0], ' ', true);
                    $pos = strpos($line[0], ' ');
                    $lastname = substr($line[0], $pos + 1);
                    $destinataire->setLastname($lastname);
                    $destinataire->setFirstname($firstname);

                    $em->persist($destinataire);
                    $em->flush();
                }
            }

            // On supprime de la BD les destinataires qui ne sont pas présents dans la liste

            $destinataires = $destinataireRepository->findAll();
            foreach ($destinataires as $destinataire) {
                foreach ($lines as $line) {
                    // on teste si le destinataire est présent dans la liste
                    if ($line[6] === $destinataire->getEmail()) {
                        $match = true;
                        break;
                    }
                    // tant que l'email entre l'occurrence de la BD et la ligne du tableau issu du fichier csv ne match pas, c'est false
                    $match = false;
                }
                // on teste si le destinataire qui ne fait plus parti de la liste,
                    // fait parti d'anciens résultats de campagne
                    if (($resultCampaignUserRepository->findOneBy(['destinataire' => $destinataire])) && (!$match) ) {
                        $old_destinataire_result[] = $destinataire;
                        $match = true;
                    }

                // si pas dans la liste on le supprime de la BD
                if ($match == false) {
                    $em->remove($destinataire);
                    $em->flush();
                }
            }
        }

        return $this->render('admin/importFileForm.html.twig', [
            'error' => $error,
            'success' => $success,
            'old_destinataire_result' => $old_destinataire_result,
        ]);
    }

    /**
     * @Route("/admin/gestion", name="gestion_admin")
     */
    public function adminManagement(AdminRepository $adminRepository, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em)
    {
        // on récupère l'admin connecté
        $user = $this->getUser();
        // on récupère les autres utilisateurs admin existants
        $other_users = $adminRepository->findByNotCurrentUser($user->getId());

        // si modification du compte admin
        if (isset($_POST['submit_update_admin'])) {
            // on vérifie si l'email est unique
            if ((count($adminRepository->findBy(['email' => $_POST['email']]))) && ($_POST['email'] !== $user->getEmail())) {
                $error = "L'email est déjà utilisé par un autre user. Vous devez en saisir un qui est unique.";
                return $this->render('admin/adminManagement.html.twig', [
                    'error' => $error,
                    'other_users' => $other_users,
                ]);
            }
            // sinon
            $user->setEmail($_POST['email']);

            // on vérifie si on veut modifier le mot de passe
            if ((isset($_POST['pwd1'])) && (isset($_POST['pwd2']))) {
                $user->setPassword($passwordHasher->hashPassword($user, $_POST['pwd1']));
            }

             // on update la BD
             $em->persist($user);
             $em->flush();

            $success = "Votre compte a bien été modifié.";
            return $this->render('admin/adminManagement.html.twig',[
                'success' => $success,
                'other_users' => $other_users,
            ]);
        }
        // si création d'un compte admin
        if (isset($_POST['submit_create_admin'])) {
            // on crée un nouvel objet
            $new_user = new Admin;
            // on vérifie si l'identifiant et l'email est unique
            if ((count($adminRepository->findBy(['email' => $_POST['email']])) || (count($adminRepository->findBy(['username' => $_POST['username']])))))
            {
                $error = "L'identifiant et/ou l'email existent déjà. Vous devez saisir un autre identifiant et/ou un autre email.";
                return $this->render('admin/adminManagement.html.twig', [
                    'error' => $error,
                    'other_users' => $other_users,
                ]);
            }

            // sinon on update les propriétés de new_user
            $new_user->setUsername($_POST['username']);
            $new_user->setEmail($_POST['email']);
            $new_user->setRoles(['ROLE_ADMIN']);
            $new_user->setPassword($passwordHasher->hashPassword($new_user, $_POST['pwd1']));
            // on update la BD
            $em->persist($new_user);
            $em->flush();

            $success = "Le compte a bien été créé.";
            return $this->render('admin/adminManagement.html.twig',[
                'success' => $success,
                'other_users' => $other_users,
            ]);
        }
        // si suppression d'un compte admin
        if (isset($_POST['submit_delete_admin'])) {
            // on recherche le compte à supprimer dans la BD
            $delete_user = $adminRepository->find($_POST['user']);
            // on update la BD
            $em->remove($delete_user);
            $em->flush();

            $success = "Le compte a bien été supprimé.";
            return $this->render('admin/adminManagement.html.twig',[
                'success' => $success,
                'other_users' => $other_users,
            ]);
        }

        return $this->render('admin/adminManagement.html.twig', [
            'other_users' => $other_users,
        ]);
    }

    /**
     * @Route("/admin/readme", name="readme")
     */
    public function readme()
    {
        return $this->render('README.MD.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<h1><img src="images/swordphish-logo-70x70.png"/> SwordPhish</h1>')
            ->setFaviconPath("images/swordphish-logo-70x70.png");
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-tachometer-alt')
            ->setCssClass('h5');
        yield MenuItem::section();
        yield MenuItem::linkToCrud('Les campagnes', 'fas fa-paper-plane', Campagne::class)
            ->setCssClass('h5');
        yield MenuItem::linkToCrud('Les destinataires', 'fas fa-users', Destinataire::class)
            ->setCssClass('h5');
        yield MenuItem::linkToCrud('Les résultats', 'fas fa-list-alt', ResultCampaignUser::class)
            ->setCssClass('h5');
        yield MenuItem::section('Paramétres', 'fa fa-cog')
            ->setCssClass('h4');
        yield MenuItem::linkToRoute('Import destinataires', 'fas fa-file-import', 'import_destinataires')
            ->setCssClass('h5');
        yield MenuItem::linkToRoute('Gestion admin', 'fas fa-user-cog', 'gestion_admin')
            ->setCssClass('h5');
        yield MenuItem::section();
        yield MenuItem::linkToRoute('Documentation', 'fas fa-file', 'readme')
            ->setCssClass('h5');
    }
}
