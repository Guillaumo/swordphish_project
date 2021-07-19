<?php

namespace App\Controller\Admin;

use App\Entity\Campagne;
use App\Entity\Destinataire;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class CampagneCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Campagne::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('une campagne')
            ->setEntityLabelInPlural('Les Campagnes')
            ->setDefaultSort(['id' => 'ASC']);
    }


    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('destinataires');
        yield TextField::new('name','Intitulé');
        yield DateTimeField::new('date')
            ->setFormTypeOptions([
                'html5' => true,
                'years' => range(date('Y'), date('Y')+2),
                'widget' => 'single_text',
            ]);


        // return [
        //     IdField::new('id'),
        //     TextField::new('title'),
        //     TextEditorField::new('description'),
        // ];
    }
    
}
