<?php

namespace App\Controller\Admin;

use App\Entity\Campagne;
use App\Entity\Destinataire;
use App\Repository\DestinataireRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use Symfony\Component\Validator\Constraints\Date;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\CollectionField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

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
            ->setDefaultSort(['date' => 'DESC','id' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        $sendEmail = Action::new('sendEmail','Envoi','fas fa-mail-bulk')
            ->linkToRoute('admin_email_envoi',function(Campagne $campagne) {
                return ['uid' => $campagne->getId(),];
            })
            ->addCssClass('btn btn-danger')
        ;
        $sendTest = Action::new('sendTest','Envoi test','fas fa-envelope')
        ->linkToRoute('admin_email_test',[])
            ->addCssClass('btn btn-success')
        ;
        $statistic =Action::new('statistic','Stat campagne','fas fa-chart-line')
            ->linkToRoute('admin',[])
            ->addCssClass('btn btn-primary')
        ;
        return $actions
            ->add(Crud::PAGE_INDEX,$sendEmail)
            ->add(Crud::PAGE_INDEX,$sendTest)
            ->add(Crud::PAGE_INDEX,$statistic)
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm()
        ;
        yield AssociationField::new('destinataires')
           ->setFormTypeOptions([
               'multiple' => true,
           ])
           ->setHelp('Pas besoin de sélectionner les destinataires, ils sont tous sélectionnés par défaut à la création d\'une campagne. Vous pouvez modifier la liste en enlevant des destinataires en mode edit.')
        ;
        
        yield TextField::new('name','Intitulé');
        yield DateField::new('date')
        // yield DateTimeField::new('date')
            ->setFormTypeOptions([
                'html5' => true,
                'years' => range(date('Y'), date('Y')+2),
                'widget' => 'single_text',
            ])
            // ->setValue(new \DateTime())
            ->hideOnForm()
            ;

    }

    
    
}
