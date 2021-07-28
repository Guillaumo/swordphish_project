<?php

namespace App\Controller\Admin;

use App\Entity\Destinataire;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class DestinataireCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Destinataire::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('un destinataire')
            ->setEntityLabelInPlural('Les Destinataires')
            ->setDefaultSort(['id' => 'ASC']);
    }


    public function configureFields(string $pageName): iterable
    {
        yield AssociationField::new('campagnes');
        yield TextField::new('lastname', 'Nom');
        yield TextField::new('firstname', 'Prénom');
        yield EmailField::new('email', 'Email');
        yield TextField::new('office', 'Agence'); 


        // return [
        //     IdField::new('id'),
        //     TextField::new('title'),
        //     TextEditorField::new('description'),
        // ];
    }
}
