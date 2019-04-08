<?php

namespace App\Controller;

use App\Entity\Comments;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class CommentsController extends AbstractController
{
    /**
     * @Route("/comments", name="comments")
     */
    public function index()
    {
        return $this->render('comments/index.html.twig', [
            'controller_name' => 'CommentsController',
        ]);
    }

    /**
     * @Route("/comments/{id}/delete", name="deleteCom")
     */
    public function delete(Comments $comments) {
        $em = $this->getDoctrine()->getManager();
        $em->remove($comments);
        $em->flush();

        return $this->redirectToRoute('article', ['id' => $comments->getRelation()->getId()]);
    }
}
