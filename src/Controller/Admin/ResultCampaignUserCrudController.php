<?php

namespace App\Controller\Admin;

use App\Entity\ResultCampaignUser;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ResultCampaignUserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ResultCampaignUser::class;
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
