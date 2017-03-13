<?php

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Page;
use AppBundle\Form\AdminNewPageType;
use AppBundle\Form\AdminEditPageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class PageController
 * @Route("/admin/pages")
 *
 * @package AppBundle\Controller
 */
class PageController extends Controller
{
    /**
     * Lists all pages.
     * @Route("/")
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated page list
        $em = $this->get('doctrine')->getManager();
        $pages = $em->getRepository('AppBundle:Page')->sortedSearchPaged($request->query);

        return $this->render(
            'admin/page/index.html.twig',
            [
                'currentUser' => $this->getUser(),
                'pages'       => $pages,
            ]
        );
    }

    /**
     * @Route("/new")
     */
    public function newAction(Request $request)
    {
        $page = new Page();
        $form = $this->createForm(AdminNewPageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists new page
            $em = $this->get('doctrine')->getManager();
            $em->persist($page);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'La page a bien été ajoutée.');

            // User is redirected to referrer page
            return $this->redirectToRoute('app_admin_page_index');
        }

        return $this->render(
            'admin/page/new.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"})
     */
    public function editAction(Request $request, Page $page)
    {
        $form = $this->createForm(AdminEditPageType::class, $page);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists edited page
            $em = $this->get('doctrine')->getManager();
            $em->persist($page);
            $em->flush();
            // Displays success message
            $this->get('session')->getFlashBag()->add('success', 'La page a bien été modifiée.');

            return $this->redirectToRoute('app_admin_page_index');
        }

        return $this->render(
            'admin/page/edit.html.twig',
            [
                'currentUser' => $this->getUser(),
                'form'        => $form->createView(),
                'page'        => $page,
            ]
        );
    }

    /**
     * Finds and deletes a Page.
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     */
    public function deleteAction(Request $request, Page $page)
    {
        $user = $this->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles())) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Désolé, <strong>'.$user->getUsername().'</strong><br />'.
                'Vous n\'êtes pas autorisé à effectuer cette action.'
            );

            return $this->redirectToRoute('app_admin_page_index');
        }

        // Deletes specified user
        $em = $this->get('doctrine')->getManager();
        $em->remove($page);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'La page <strong>&quot;'.$page->getTitle().'&quot;</strong> à bien été supprimée.'
        );

        // Admin is redirected to referrer page
        return $this->redirectToRoute('app_admin_page_index');
    }

}
