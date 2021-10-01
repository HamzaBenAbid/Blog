<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();

        for($i=1;$i<5;$i++){
            $category = new Category();
            $category->setTitle("tile $i" );
            $category->setDescription("description $i");

            $manager->persist($category);

            for ($j=1;$j <=2;$j++){
                $article=new Article();
                $article->setTitle("title $j");
                $article->setContent("this is a big text ");
                $article->setCreatedAt(new \DateTime());
                $article->setImage("https://picsum.photos/seed/picsum/300/150");
                $article->setCategory($category);
                $manager->persist($article);
            }

        }

        $manager->flush();
    }
}
