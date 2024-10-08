<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    public function configureFields(string $pageName): iterable
    {
        $roles = ['Admin' => User::ROLE_ADMIN, 'User' => User::ROLE_USER];

        return [
            IdField::new('id')->hideOnForm(),
            TextField::new('name'),
            EmailField::new('email'),
            TextField::new('phone'),
            ChoiceField::new('roles')->setChoices($roles)->allowMultipleChoices()->renderAsBadges(),
        ];
    }
}
