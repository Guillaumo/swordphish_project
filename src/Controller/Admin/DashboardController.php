<?php

namespace App\Controller\Admin;

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
        if(isset($_POST['submit_file']))
        {
            $file = $_FILES['file']['name'];
            $uploadDirectory = dirname(__DIR__,3)."/public/upload/";
            $uploadFile = $uploadDirectory.basename($file);
            if(!move_uploaded_file($_FILES['file']['tmp_name'],$uploadFile))
            {
                $this->addFlash('message','Votre fichier ,n\'a pas pu être importé. Recommencer.');
            }

            move_uploaded_file($_FILES['file']['tmp_name'],$uploadFile);
            $this->addFlash('message','Votre fichier a bien été importé. Importation en BD en cours.');

            $handle = fopen($uploadFile,'r');
            $linecount = true;

            while ($line = fgetcsv($handle,0,';')) 
            {
                if($linecount) 
                {
                    $linecount = false;
                    continue;
                }

                $destinataire = $destinataireRepository->findOneBy(['email' => $line[6]]);
                if(is_null($destinataire))
                {
                    $destinataire = new Destinataire;
                    $destinataire->setEmail($line[6]);
                    $destinataire->setOffice($line[5]);
                    $firstname = strstr($line[0],' ',true);
                    $pos = strpos($line[0],' ');
                    $lastname = substr($line[0],$pos+1);
                    $destinataire->setLastname($lastname);
                    $destinataire->setFirstname($firstname);

                    $em->persist($destinataire);
                    $em->flush();
                }

            }

        }
         return $this->render('admin/importFileForm.html.twig');
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
        yield MenuItem::linkToRoute('Gestion admin','fas fa-user-cog','/')
            ->setCssClass('h5');

    }
}
