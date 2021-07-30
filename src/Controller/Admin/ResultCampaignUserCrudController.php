<?php

namespace App\Controller\Admin;

use App\Entity\ResultCampaignUser;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;

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
        yield AssociationField::new('destinataire')
           ->setFormTypeOptions([
               'multiple' => true,
           ])
        ;
        yield AssociationField::new('campagne')
           ->setFormTypeOptions([
               'multiple' => true,
           ])
        ;
        yield TextField::new('userip','IP');
        yield TextField::new('username','User session');
        yield TextField::new('hostname','Nom machine et domaine');
        yield TextField::new('navigator','Navigateur');
        yield DateTimeField::new('date')
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
