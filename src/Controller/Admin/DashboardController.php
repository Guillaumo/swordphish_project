<?php

namespace App\Controller\Admin;

use App\Entity\Admin;
use App\Entity\Campagne;
use App\Entity\Destinataire;
use App\Entity\ResultCampaignUser;
use App\Repository\DestinataireRepository;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

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
    public function importFileDestinataires(DestinataireRepository $destinataireRepository, EntityManagerInterface $em)
    {
        $error='';
        $success='';

        if(isset($_POST['submit_file']))
        {
            $file = $_FILES['file']['name'];
            $uploadDirectory = dirname(__DIR__,3)."/public/upload/";
            $uploadFile = $uploadDirectory.basename($file);
            if(!move_uploaded_file($_FILES['file']['tmp_name'],$uploadFile))
            {
                $error = 'Votre fichier n\'a pas pu être importé. Recommencer.';
            }

            move_uploaded_file($_FILES['file']['tmp_name'],$uploadFile);
            $success = 'Votre fichier a bien été importé. Importation en BD en cours.';

            // lecture du fichier csv
            $handle = fopen($uploadFile,'r');
            // on récupère les éléments du fichier dans un tableau
            $lines = [];
            while ($line = fgetcsv($handle,0,';')) {
                $lines[] = $line;
            }
            // on enlève la ligne des champs
            array_shift($lines);
            // on mélange le tableau
            shuffle($lines);

            // Insertion en BD la liste des destinataires
            foreach($lines as $line) 
            {
                $destinataire = $destinataireRepository->findOneBy(['email' => $line[6]]);
                // si le destinataire n'existe pas déjà
                if(is_null($destinataire))
                {
                    $destinataire = new Destinataire;
                    $destinataire->setEmail($line[6]);
                    $destinataire->setOffice($line[4]);
                    $firstname = strstr($line[0],' ',true);
                    $pos = strpos($line[0],' ');
                    $lastname = substr($line[0],$pos+1);
                    $destinataire->setLastname($lastname);
                    $destinataire->setFirstname($firstname);

                    $em->persist($destinataire);
                    $em->flush();
                }
            }
            
            // On supprime de la BD les destinataires qui ne sont pas présents dans la liste
            
            $destinataires = $destinataireRepository->findAll();
            foreach($destinataires as $destinataire)
            {
                foreach($lines as $line)
                {
                    // on teste si le destinataire est présent dans la liste
                    if($line[6] === $destinataire->getEmail())
                    {
                        $match = true;
                        break;
                    }
                    // tant que l'email entre l'occurrence de la BD et la ligne du tableau issu du fichier csv ne match pas, c'est false
                    $match = false;
                    
                }
                // si pas dans la liste on le supprime de la BD
                if($match == false)
                {
                    $em->remove($destinataire);
                    $em->flush();
                }
            }
        }

         return $this->render('admin/importFileForm.html.twig', [
             'error' => $error,
             'success' => $success,
         ]);
    }

    /**
     * @Route("/admin/gestion", name="gestion_admin")
     */
    public function adminManagement()
    {

        return $this->render('admin/adminManagement.html.twig');
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('<h1><img src="images/swordphish-logo-70x70.png"/> SwordPhish</h1>')
            ->setFaviconPath("images/swordphish-logo-70x70.png")
            ;
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-tachometer-alt')
            ->setCssClass('h5')
        ;
        yield MenuItem::section();
        yield MenuItem::linkToCrud('Les campagnes', 'fas fa-paper-plane', Campagne::class)
            ->setCssClass('h5')
        ;
        yield MenuItem::linkToCrud('Les destinataires', 'fas fa-users', Destinataire::class)
            ->setCssClass('h5')
        ;
        yield MenuItem::linkToCrud('Les résultats', 'fas fa-list-alt', ResultCampaignUser::class)
            ->setCssClass('h5')
        ;
        yield MenuItem::section('Paramétres','fa fa-cog')
            ->setCssClass('h4');
        yield MenuItem::linkToRoute('Import destinataires','fas fa-file-import','import_destinataires')
            ->setCssClass('h5');
        yield MenuItem::linkToRoute('Gestion admin','fas fa-user-cog','gestion_admin')
            ->setCssClass('h5');
        yield MenuItem::linkToCrud('Comptes administrateurs','fas fa-user-cog',Admin::class);

    }
}
