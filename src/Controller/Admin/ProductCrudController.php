<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\SlugField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class ProductCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Product::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud

            // the labels used to refer to this entity in titles, buttons, etc.
            ->setEntityLabelInSingular('Produit')
            ->setEntityLabelInPlural('Produits');
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('name', "Nom du produit")->setHelp("Produit"),
            SlugField::new('slug','URL')->setTargetFieldName('name'),
            TextEditorField::new('description','Description'),
            ImageField::new('illustration', 'Image')
                ->setUploadDir('public/uploads/products')
                ->setBasePath('uploads/products')
                ->setUploadedFileNamePattern('[year][month][day]-[contenthash].[extension]')    ,
            NumberField::new('price', 'Prix'),
            ChoiceField::new('tva', 'TVA')->setChoices([
                '5,5%' =>'5,5',
                '10%' =>'10',
                '20%' =>'20',
            ]),
            AssociationField::new('category', 'choisir une catégorie')

        ];
    }

}