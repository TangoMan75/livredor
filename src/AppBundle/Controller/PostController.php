<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Post;
use AppBundle\Form\PostType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/post")
 */
class PostController extends Controller
{
    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"}, name="post_edit")
     */
    public function editAction(Request $request, Post $post)
    {
        if (!$this->getUser()) {

            $this->get('session')->getFlashBag()->add('error', "Vous devez être connecté pour pouvoir éditer des messages.");
            return $this->redirectToRoute('app_homepage');

        } else {

            if ( $this->getUser() == $post->getUser() || in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ) {

                $form = $this->createForm(PostType::class, $post);
                $form->handleRequest($request);

                if ($form->isSubmitted() && $form->isValid()) {

                    $this->get('em')->save($post);

                    $this->get('session')->getFlashBag()->add('success', "Votre message <strong>&quot;{$post->getTitle()}&quot</strong> à bien été modifié.");

                    return $this->redirectToRoute('app_homepage');

                } else {

                    return $this->render('post/edit.html.twig', [
                        "form_post" => $form->createView()
                    ]);
                }

            } else {

                $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à éditer ce message.");
                return $this->redirectToRoute('app_homepage');

            }
        }
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"}, name="post_delete")
     */
    public function deleteAction(Request $request, Post $post)
    {
        if ( $this->getUser() == $post->getUser() || in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ) {

            // Deletes specified post
            $this->get('em')->remove($post);
            $this->get('em')->flush();

            $this->get('session')->getFlashBag()->add('success', "Le message <strong>&quot;{$post->getTitle()}&quot;</strong> à été supprimé.");

        } else {

            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à supprimer ce message.");
            return $this->redirectToRoute('app_homepage');

        }

        return $this->redirectToRoute('app_homepage');
    }

}
