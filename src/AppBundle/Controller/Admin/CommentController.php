<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Comment;
use AppBundle\Form\AdminEditCommentType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CommentController
 * @Route("/admin/comments")
 *
 * @package AppBundle\Controller
 */
class CommentController extends Controller
{
    /**
     * Lists all comments.
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated page list
        $em = $this->get('doctrine')->getManager();
        $comments = $em->getRepository('AppBundle:Comment')->sortedSearchPaged($request->query);

        return $this->render(
            'admin/comment/index.html.twig',
            [
                'currentUser' => $this->getUser(),
                'comments'    => $comments,
            ]
        );
    }

    /**
     * @Route("/new")
     */
    public function newAction(Request $request)
    {
        $comment = new Comment();
        $form = $this->createForm(AdminEditCommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists new page
            $em = $this->get('doctrine')->getManager();
            $em->persist($comment);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Le commentaire a bien été ajouté.');

            // User is redirected to referrer page
            return $this->redirectToRoute('app_admin_comment_index');
        }

        return $this->render(
            'admin/comment/new.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"})
     */
    public function editAction(Request $request, Comment $comment)
    {
        $form = $this->createForm(AdminEditCommentType::class, $comment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists edited page
            $em = $this->get('doctrine')->getManager();
            $em->persist($comment);
            $em->flush();
            // Displays success message
            $this->get('session')->getFlashBag()->add('success', 'Le commentaire a bien été modifié.');

            return $this->redirectToRoute('app_admin_comment_index');
        }

        return $this->render(
            'admin/comment/edit.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
                'comment'     => $comment,
            ]
        );
    }

    /**
     * Finds and deletes a Comment.
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, Comment $comment)
    {
        // Deletes specified post
        $em = $this->get('doctrine')->getManager();
        $em->remove($comment);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'Le commentaire <strong>&quot;'.$comment.'&quot;</strong> a bien été supprimé.'
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }
}
