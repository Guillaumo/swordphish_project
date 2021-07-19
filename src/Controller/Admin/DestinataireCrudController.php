<?php

namespace App\Controller\Admin;

use App\Entity\Destinataire;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DestinataireCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Destinataire::class;
    }

    /*
    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            TextField::new('title'),
            TextEditorField::new('description'),
        ];
    }
    */
}
