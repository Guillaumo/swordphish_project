<?php

namespace App\Controller\Admin;

use App\Entity\Campagne;
use App\Entity\Destinataire;
use App\Entity\ResultCampaignUser;
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

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('Swordphish');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linktoDashboard('Dashboard', 'fa fa-tachometer-alt');
        yield MenuItem::section();
        yield MenuItem::linkToCrud('Les campagnes', 'fas fa-paper-plane', Campagne::class);
        yield MenuItem::linkToCrud('Les destinataires', 'fas fa-users', Destinataire::class);
        yield MenuItem::linkToCrud('Les résultats', 'fas fa-list-alt', ResultCampaignUser::class);

    }
}
