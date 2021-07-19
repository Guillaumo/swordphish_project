<?php

namespace App\Controller\Admin;

use App\Entity\Campagne;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CampagneCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Campagne::class;
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
