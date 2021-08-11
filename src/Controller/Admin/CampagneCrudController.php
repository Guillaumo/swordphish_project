<?php

namespace App\Controller\Admin;

use App\Entity\Campagne;
use App\Entity\Destinataire;
use App\Repository\CampagneRepository;
use App\Repository\DestinataireRepository;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use Symfony\Component\Validator\Constraints\Date;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
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
            ->setDefaultSort(['date' => 'DESC', 'id' => 'DESC']);
    }

    public function configureActions(Actions $actions): Actions
    {
        // bouton d'envoi des mails pour une campagne donnée - affiché si la campagne n'a pas été envoyée
        $sendEmail = Action::new('sendEmail', 'Envoi Campagne', 'fas fa-mail-bulk')
            ->displayIf(static function (Campagne $campagne) {
                $isDisplayed = true;
                if ($campagne->getIsSent()) {
                    $isDisplayed = false;
                }
                return $isDisplayed;
            })
            ->linkToRoute('admin_email_campagne', function (Campagne $campagne) {
                return [
                    'uid' => $campagne->getId(),
                ];
            })
            ->addCssClass('btn btn-danger btn-block w-30')
            ->setHtmlAttributes([
                'onclick' => "return(confirm('Etes-vous sûr de vouloir envoyer les emails ?'));"
            ]);
        // bouton d'envoi du retour d'infos vers les destinataires pour une campagne donnée - affiché si la campagne a été envoyée
        $sendInfo = Action::new('sendInfo', 'Envoi Infos', 'fas fa-mail-bulk')
            ->displayIf(static function (Campagne $campagne) {
                $isDisplayed = false;
                if ($campagne->getIsSent()) {
                    $isDisplayed = true;
                }
                return $isDisplayed;
            })
            ->linkToRoute('admin_email_infos', function (Campagne $campagne) {
                return [
                    'uid' => $campagne->getId(),
                ];
            })
            ->addCssClass('btn btn-warning btn-block w-30')
            ->setHtmlAttributes([
                'onclick' => "return(confirm('Etes-vous sûr de vouloir envoyer les emails ?'));"
            ]);
        // bouton d'envoi de mail(s) de test pour une campagne donnée
        $sendTest = Action::new('sendTest', 'Envoi test', 'fas fa-envelope')
            ->linkToRoute('admin_email_test', function (Campagne $campagne) {
                return ['uid' => $campagne->getId(),];
            })
            ->addCssClass('btn btn-success btn-block w-30');
        // bouton pour afficher les stats
        $statistic = Action::new('statistic', 'Stat campagne', 'fas fa-chart-line')
            ->displayIf(static function (Campagne $campagne) {
                $isDisplayed = false;
                if ($campagne->getIsSent()) {
                    $isDisplayed = true;
                }
                return $isDisplayed;
            })
            ->linkToRoute('admin_stat', function (Campagne $campagne) {
                return ['uid' => $campagne->getId(),];
            })
            ->addCssClass('btn btn-primary btn-block w-30');

        return $actions
            ->add(Crud::PAGE_INDEX, $sendEmail)
            ->add(Crud::PAGE_INDEX, $sendInfo)
            ->add(Crud::PAGE_INDEX, $sendTest)
            ->add(Crud::PAGE_INDEX, $statistic);
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm();
        yield AssociationField::new('destinataires')
            //    ->setFormTypeOptions([
            //        'multiple' => true,
            //    ])
            ->setHelp('Pas besoin de sélectionner les destinataires, ils sont tous sélectionnés par défaut à la création d\'une campagne. Vous pouvez modifier la liste en enlevant des destinataires en mode edit.');
        yield TextField::new('name', 'Intitulé');
        yield DateField::new('date')
            ->setFormTypeOptions([
                'html5' => true,
                'years' => range(date('Y'), date('Y') + 2),
                'widget' => 'single_text',
            ])
            ->hideOnForm();
        yield BooleanField::new('isSent')
            ->onlyOnForms();
    }
}
