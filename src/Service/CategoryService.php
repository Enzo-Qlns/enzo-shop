<?php

namespace App\Service;

use App\Repository\CategoryRepository;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class CategoryService
{
    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getAllCategories()
    {
        return $this->categoryRepository->findAll();
    }
} 