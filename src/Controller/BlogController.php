<?php

namespace App\Controller;

use App\Entity\Article;
use App\Entity\Comments;
use App\Form\CommentType;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class BlogController extends AbstractController
{
    /**
     * @Route("/", name="blog")
     */
    public function index()
    {
        // $dial_db => speak with db.
        $dial_db = $this->getDoctrine()->getRepository(Article::class);
        // find all articles in db.
        $articles = $dial_db->findAll();

        return $this->render('blog/index.html.twig', [
            'controller_name' => 'BlogController',
            'articles' => $articles
        ]);
    }


    /**
     * @Route("/Article/create", name="create")
     * @Route("/Article/{id}/edit", name="edit")
     */
    public function create(Article $article = null, Request $request, ObjectManager $manager)
    {
        if (!$article) {
            $article = new Article();
        }

        $formArticle = $this->createFormBuilder($article)
                ->add('title', TextType::class,[
                    'attr' => [
                        'placeholder' => "Titre"
                    ]
                ])
                ->add('article', TextareaType::class, [
                    'attr' => [
                        'placeholder' => "Contenu"
                    ]
                ])
                ->add('illustrate', TextType::class, [
                    'attr' => [
                        'placeholder' => "URL"
                    ]
                ])
                ->getForm();

        $formArticle->handleRequest($request);

        if($formArticle->isSubmitted() && $formArticle->isValid()){
            if (!$article->getId()){
                $article->setCreatedAt(new \DateTime());
            }

            $manager->persist($formArticle);
            $manager->flush();

            return $this->redirectToRoute('article', ['id' => $article->getId()]);
        }

        return $this->render('blog/create.html.twig', [
            'formArticle' => $formArticle->createView(),
            'editMode' => $article->getId() !== null
        ]);
    }

    /**
     * @Route("/Article/{id}", name="article")
     */
    public function article($id, Request $request, ObjectManager $manager)
   {
       // $dial_db => speak with db.
       $dial_db = $this->getDoctrine()->getRepository(Article::class);
       // find one article in db by his id.
       $article = $dial_db->find($id);

       $dial_comm = $this->getDoctrine()->getRepository(Comments::class);
       $comment_id = $dial_comm->find($id);

       $comment = new Comments();
       $form = $this->createForm(CommentType::class, $comment);
       $form->handleRequest($request);

       if ($form->isSubmitted() && $form->isValid()){
               $comment->setCreatedAt(new \DateTime)
                       ->setArticle($article);
               $manager->persist($comment);
               $manager->flush();

               return $this->redirectToRoute('article', ['id' => $article->getId()]);
       }

       return $this->render('blog/article.html.twig', [
           'controller_name' => 'BlogController',
           'article' => $article,
           'comment' => $comment_id,
           'commentForm' => $form->createView()
       ]);
   }

    /**
     * @Route("/Article/{id}/delete", name="deleteArticle")
     */
   public function deleteArticle(Article $article){
       $em = $this->getDoctrine()->getManager();
       $em->remove($article);
       $em->flush();

       return $this->redirectToRoute('blog');
   }

}
