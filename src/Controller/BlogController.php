<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comment;
use App\Form\ArticleType;
use App\Form\CommentType;
use App\Repository\ArticleRepository;
use Knp\Component\Pager\PaginatorInterface;
use MercurySeries\FlashyBundle\FlashyNotifier;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class BlogController extends AbstractController
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security =$security;
    }


    /**
     * @Route("/", name="blog")
     */
    public function index(ArticleRepository $articleRepository , PaginatorInterface $paginator,Request $request): Response
    {
        $articles =$paginator->paginate($articleRepository->findAll(),
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('blog/index.html.twig', [
            'articles' => $articles
        ]);
    }

    /**
     * @Route ("/article/new", name="article_new")
     */
    public function new(Request $request ,FlashyNotifier $flashy): Response
    {
        $article = new Article();
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $article->setCreatedAt(new \DateTime());
            $article->setImage("https://picsum.photos/seed/picsum/300/150");
            $article->setUser($this->getUser());
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            $flashy->success('Article created!');
            return $this->redirectToRoute("article_show" ,['id' => $article->getId()]);
        }

        return $this->render('blog/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route ("/article/{id}/edit", name="article_edit")
     */
    public function edit(Request $request ,Article $article ,FlashyNotifier $flashy): Response
    {
        $form = $this->createForm(ArticleType::class, $article);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($article);
            $entityManager->flush();
            $flashy->success('Article modified!');

            return $this->redirectToRoute("article_show" ,['id' => $article->getId()]);
        }

        return $this->render('blog/edit.html.twig', [
            'editform' => $form->createView()
        ]);
    }


    /**
     * @Route ("/article/{id}", name="article_show" ,methods={"GET","POST"})
     */
    public function show(Article $article ,Request $request ,FlashyNotifier $flashyNotifier): Response
    {
        $comment =new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $comment->setCreatAt(new \DateTime());
            $comment->setArticle($article);
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($comment);
            $entityManager->flush();
            $flashyNotifier->success('comment  created!');

            return $this->redirectToRoute("article_show" ,['id' => $article->getId()]);
        }
        return $this->render('blog/show.html.twig', [
            'article' => $article ,
            'comment_form' => $form->createView()
        ]);
    }

}
