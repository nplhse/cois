<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Message\NewUserMessage;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\FormField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\CrudUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserCrudController extends AbstractCrudController
{
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /**
     * UserCrudController constructor.
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        $sendWelcomeEmail = Action::new('sendWelcomeEmail', 'Send welcome Email', 'fa fa-send')
            ->linkToCrudAction('sendWelcomeEmail');

        return $crud
            ->setPageTitle(Crud::PAGE_INDEX, 'Users')
            ->setSearchFields(['id', 'username', 'roles', 'email', 'hospital.name']);
    }

    public function configureActions(Actions $actions): Actions
    {
        $sendWelcomeEmail = Action::new('sendWelcomeEmail', 'Send welcome Email', 'fa fa-send')
            ->linkToCrudAction('sendWelcomeEmail');

        $actions
            ->add(Crud::PAGE_EDIT, $sendWelcomeEmail)
        ;

        return parent::configureActions($actions); // TODO: Change the autogenerated stub
    }

    public function configureFields(string $pageName): iterable
    {
        $id = IntegerField::new('id', 'Id');
        $panel1 = FormField::addPanel('Basics');
        $username = TextField::new('username', 'Username');
        $email = EmailField::new('email', 'Email');
        $plainPassword = TextField::new('plainPassword', 'Password')->setFormTypeOptions(['empty_data' => '']);
        $panel2 = FormField::addPanel('Properties');
        $roles = ArrayField::new('roles');
        $isVerified = BooleanField::new('isVerified', 'Is verified?');
        $isCredentialsExpired = BooleanField::new('isCredentialsExpired', 'Credentials are expired?');
        $panel3 = FormField::addPanel('Set new password');
        $panel4 = FormField::addPanel('Hospitals');
        $hospital = AssociationField::new('hospital', 'Hospital');

        $fields = [];

        if (Crud::PAGE_INDEX === $pageName) {
            $fields = [$id, $username, $email, $hospital];
        } elseif (Crud::PAGE_DETAIL === $pageName) {
            $fields = [$panel1, $id, $username, $email, $panel2, $roles, $isVerified, $isCredentialsExpired, $panel4, $hospital];
        } elseif (Crud::PAGE_NEW === $pageName) {
            $fields = [$panel1, $username, $plainPassword, $email, $panel2, $roles, $isVerified, $isCredentialsExpired];
        } elseif (Crud::PAGE_EDIT === $pageName) {
            $fields = [$panel1, $id, $username, $email, $panel2, $roles, $isVerified, $isCredentialsExpired, $panel3, $plainPassword, $panel4, $hospital];
        }

        return $fields;
    }

    public function sendWelcomeEmail(AdminContext $context): Response
    {
        $id = $context->getRequest()->query->get('entityId');
        $entity = $this->getDoctrine()->getRepository(User::class)->findOneBy(['id' => $id]);
        $hospital = $entity->getHospital();

        $this->dispatchMessage(new NewUserMessage($entity, $hospital));

        $this->addFlash('success', 'Welcome E-Mail was successfully sent to '.$entity->getUsername().'.');

        return $this->redirect($this->get(CrudUrlGenerator::class)->build()->setAction(Action::INDEX)->generateUrl());
    }
}
