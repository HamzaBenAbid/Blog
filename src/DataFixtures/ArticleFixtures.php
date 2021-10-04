<?php

namespace App\DataFixtures;

use App\Entity\Article;
use App\Entity\Category;
use App\Entity\Comment;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;

class ArticleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        // $product = new Product();
        $faker = Factory::create();
        for($i=1;$i<5;$i++){
            $category = new Category();
            $category->setTitle("title $i" );
            $category->setDescription("description $i");

            $manager->persist($category);

            for ($j=1;$j <=2;$j++){
                $article=new Article();
                $article->setTitle("title $j");
                $article->setContent("this is a big text ");
                $article->setCreatedAt($faker->dateTimeBetween('-6 months'));
                $article->setImage("https://picsum.photos/seed/picsum/300/150");
                $article->setCategory($category);

                $manager->persist($article);

                $today =new \DateTime();
                $difference = $today->diff($article->getCreatedAt());

                $days=$difference->days;
                $comment_max = '_'.$days.'days';

                for ($k=1;$k <= mt_rand(4,6);$k++){
                    $comment =new Comment();
                    $comment ->setAutor($faker->name)
                             ->setContent('this is a comment')
                            ->setCreatAt($faker->dateTimeBetween($comment_max))
                            ->setArticle($article);
                    $manager->persist($comment);
                }
            }

        }

        $manager->flush();
    }
}
