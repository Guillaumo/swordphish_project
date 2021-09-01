<?php

namespace App\Controller\Admin;

use App\Entity\ResultCampaignUser;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TelephoneField;

class ResultCampaignUserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return ResultCampaignUser::class;
    }

    
    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm()
        ;
        yield AssociationField::new('destinataire');
        yield AssociationField::new('campagne');
        yield TextField::new('userip','IP');
        yield TextField::new('hostname','Nom machine et domaine');
        yield TextField::new('navigator','Navigateur');
        yield TextField::new('lastname','Nom');
        yield TextField::new('firstname','Prénom');
        yield TelephoneField::new('telephone','Téléphone');
        yield EmailField::new('email');
        yield DateTimeField::new('created_at','date')
            ->setFormTypeOptions([
                'html5' => true,
                'years' => range(date('Y'), date('Y')+2),
                'widget' => 'single_text',
            ])
            ->hideOnForm()
            ;

    //     return [
    //         IdField::new('id'),
    //         TextField::new('title'),
    //         TextEditorField::new('description'),
    //     ];
    }
    
}
