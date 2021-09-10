<?php

namespace App\Controller\Admin;

use App\Entity\Campagne;
use App\Repository\CampagneRepository;
use App\Repository\DestinataireRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Symfony\Component\DomCrawler\Field\ChoiceFormField;

class CampagneCrudController extends AbstractCrudController
{
    private $adminUrlGenerator;
    protected $campagneRepository;
    protected $destinataireRepository;

    public function __construct(AdminUrlGenerator $adminUrlGenerator, CampagneRepository $campagneRepository, DestinataireRepository $destinataireRepository)
    {
        $this->adminUrlGenerator = $adminUrlGenerator;
        $this->campagneRepository = $campagneRepository;
        $this->destinataireRepository = $destinataireRepository;
    }

    /**
     * @Route("/admin/campaign/toggle/{uid}", name="admin_campaign_toggle")
     */
    public function toggleCampaign($uid, EntityManagerInterface $em)
    {
        // on récupère l'objet de la campagne sélectionnée pour l'envoi
        $campagne = $this->campagneRepository->findOneBy(['id' => $uid]);
        if ($campagne->getIsEnable()) {
            $campagne->setIsEnable(false);
        } else {
            $campagne->setIsEnable(true);
            $campagne->setIsSent(false);
            $campagne->setIsInfoSent(false);
        }
        // on met à jour le champ isEnable de la campagne selon le cas
        $em->persist($campagne);
        $em->flush();

        return $this->redirect(
            $this->adminUrlGenerator
                ->setController(CampagneCrudController::class)
                ->setAction(Action::INDEX)
                ->generateUrl()
        );
    }

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

    /**
     * Configuration des boutons d'actions possibles pour une campagne donnée
     *
     * @param Actions $actions
     * @return Actions
     */
    public function configureActions(Actions $actions): Actions
    {
        // bouton d'envoi des mails pour une campagne donnée - affiché si la campagne n'a pas été envoyée
        $sendEmail = Action::new('sendEmail', 'Envoi Campagne', 'fas fa-mail-bulk')
            ->displayIf(static function (Campagne $campagne) {
                $isDisplayed = false;
                if ((!$campagne->getIsSent()) && ($campagne->getIsEnable())) {
                    $isDisplayed = true;
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
                'onclick' => "return(confirm('Etes-vous sûr de vouloir envoyer les emails de la campagne ?'));"
            ]);

        // bouton d'envoi du retour d'infos vers les destinataires pour une campagne donnée - affiché si la campagne a été envoyée
        $sendInfo = Action::new('sendInfo', 'Envoi Infos', 'fas fa-mail-bulk')
            ->displayIf(static function (Campagne $campagne) {
                $isDisplayed = false;
                if (($campagne->getIsSent()) && ($campagne->getIsEnable())) {
                    $isDisplayed = true;
                }
                if($campagne->getIsInfoSent()) {
                    $isDisplayed = false;
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
                'onclick' => "return(confirm('Etes-vous sûr de vouloir envoyer les emails d\'infos ?'));"
            ]);

        // bouton d'envoi de mail(s) de test pour une campagne donnée
        $sendTest = Action::new('sendTest', 'Envoi test', 'fas fa-envelope')
            ->displayIf(static function (Campagne $campagne) {
                $isDisplayed = false;
                if ($campagne->getIsEnable()) {
                    $isDisplayed = true;
                }
                return $isDisplayed;
            })
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

        // bouton de désactivaton de la campagne
        $disenable = Action::new('disenable', 'Désactiver', 'fa fa-times-circle-o')
            ->addCssClass('btn bg-dark text-white')
            ->displayIf(static function (Campagne $campagne) {
                $isDisplayed = false;
                if ($campagne->getIsEnable()) {
                    $isDisplayed = true;
                }
                return $isDisplayed;
            })
            ->linkToRoute('admin_campaign_toggle', function (Campagne $campagne) {
                return ['uid' => $campagne->getId()];
            });

        // bouton d'activaton de la campagne
        $enable = Action::new('enable', 'Activer', 'fa fa-check-circle-o')
            ->addCssClass('btn bg-dark text-white')
            ->displayIf(static function (Campagne $campagne) {
                $isDisplayed = false;
                if (!$campagne->getIsEnable()) {
                    $isDisplayed = true;
                }
                return $isDisplayed;
            })
            ->linkToRoute('admin_campaign_toggle', function (Campagne $campagne) {
                return ['uid' => $campagne->getId()];
            });


        return $actions
            ->add(Crud::PAGE_INDEX, $statistic)
            ->add(Crud::PAGE_INDEX, $sendEmail)
            ->add(Crud::PAGE_INDEX, $sendInfo)
            ->add(Crud::PAGE_INDEX, $sendTest)
            ->add(crud::PAGE_INDEX, $disenable)
            ->add(crud::PAGE_INDEX, $enable)
            ->reorder(Crud::PAGE_INDEX, ['enable', 'disenable', 'sendTest', 'sendInfo', 'sendEmail', 'statistic'])
            ;
    }

    public function configureFields(string $pageName): iterable
    {
        yield IdField::new('id')
            ->hideOnForm();
        yield AssociationField::new('destinataires')
            ->setHelp('Pas besoin de sélectionner les destinataires, ils sont tous sélectionnés par défaut à la création d\'une campagne. Vous pouvez modifier la liste en enlevant des destinataires en mode edit.');
        yield TextField::new('name', 'Intitulé');
        yield DateField::new('date')
            ->setFormTypeOptions([
                'html5' => true,
                'years' => range(date('Y'), date('Y') + 2),
                'widget' => 'single_text',
            ])
            ->hideOnForm();
        yield IntegerField::new('number_recipients_per_group','Nombre de destinataires par groupe d\'envoi')
            ->onlyOnForms()
        ;
        yield IntegerField::new('tempo_minutes','Temporisation des envois en minutes')
            ->onlyOnForms()
        ;
        yield IntegerField::new('duration_sending','Durée de l\'envoi')
            ->hideOnForm()
            ->setTextAlign('center')
            ->formatValue(function ($value) {
                if($value>=60)
                {
                    $heures = intdiv($value,60);
                    $minutes = $value - $heures*60;
                    return $heures.' heure(s) et '.$minutes.' minute(s)';
                }
                else
                {
                    return $value.' minute(s)';
                }
            })
        ;
        yield BooleanField::new('isEnable', 'Campagne activée')
            ->onlyWhenUpdating();
        yield BooleanField::new('isSent', 'Campagne envoyée')
            ->onlyWhenUpdating();
        yield BooleanField::new('isInfoSent', 'Infos envoyés')
            ->onlyWhenUpdating();   
    }
}
