<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UserController
 *
 * @package AppBundle\Controller
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * Lists all users.
     *
     * @Route("/", name="user_index")
     * @Method("GET")
     */
    public function indexAction(Request $request)
    {
        // User must log in
        if ( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') ) {
            $this->get('session')->getFlashBag()->add('error', "Vous devez être connecté pour réaliser cette action.");
            return $this->redirectToRoute('app_login');
        }

        // Only admins are allowed to perform this action
        if ( !in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ) {
            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à réaliser cette action.");
            return $this->redirectToRoute('app_homepage');
        }

        $users = $this->get('em')->repository('AppBundle:User')->sorting(
            $request->query->getInt('page', 1),
            20,
            $request->query->get('order', 'username'),
            $request->query->get('way', 'ASC')
        );

        return $this->render('user/index.html.twig', [
            'users' => $users
        ]);
    }

    /**
     * Exports user list in json format.
     *
     * @Route("/export-json", name="user_export_json")
     * @Method("GET")
     */
    public function exportJsonAction()
    {
        // User must log in
        if ( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') ) {
            $this->get('session')->getFlashBag()->add('error', "Vous devez être connecté pour réaliser cette action.");
            return $this->redirectToRoute('app_login');
        }

        // Only admins are allowed to perform this action
        if ( !in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ) {
            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à réaliser cette action.");
            return $this->redirectToRoute('app_homepage');
        }

        $users = $this->get('em')->repository('AppBundle:User')->findBy([], ['username' => 'ASC']);

        $response = serialize($users);

        return new JsonResponse($response, 200, [
            'Content-Type' => 'application/force-download',
            'Content-Disposition' => 'attachment; filename="export_users.json"'
        ]);
    }

    /**
     * Exports user list in csv format.
     *
     * @Route("/export-csv", name="user_export_csv")
     * @Method("GET")
     */
    public function exportCSVAction()
    {
        // User must log in
        if ( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') ) {
            $this->get('session')->getFlashBag()->add('error', "Vous devez être connecté pour réaliser cette action.");
            return $this->redirectToRoute('app_login');
        }

        // Only admins are allowed to perform this action
        if ( !in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ) {
            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à réaliser cette action.");
            return $this->redirectToRoute('app_homepage');
        }

        $users = $this->get('em')->repository('AppBundle:User')->findBy([], ['username' => 'ASC']);
        $delimiter = ';';
        $handle = fopen('php://memory', 'r+');

        fputcsv($handle, [
            'User Id',
            'Utilisateur',
            'Email',
            'Date de Création'
        ], $delimiter);

        foreach ($users as $user) {
            fputcsv($handle, [
                $user->getId(),
                $user->getusername(),
                $user->getEmail(),
                $user->getDateCreated()->format('d/m/Y H:i:s')
            ], $delimiter);
        }

        rewind($handle);
        $response = stream_get_contents($handle);
        fclose($handle);

        return new Response($response, 200, [
            'Content-Type' => 'application/force-download',
            'Content-Disposition' => 'attachment; filename="export_users.csv"'
        ]);
    }

    /**
     * Finds and deletes a User entity.
     *
     * @Route("/delete/{id}", requirements={"id": "\d+"}, name="admin_user_delete")
     * @Method("GET")
     */
    public function deleteAction(Request $request, User $user)
    {
        // User must log in
        if ( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') ) {
            $this->get('session')->getFlashBag()->add('error', "Vous devez être connecté pour réaliser cette action.");
            return $this->redirectToRoute('app_login');
        }

        // Only admins are allowed to perform this action
        if ( !in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ) {
            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à réaliser cette action.");
            return $this->redirectToRoute('app_homepage');
        }

        // Deletes specified user
        $this->get('em')->remove($user);
        $this->get('em')->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add('success', "L'utilisateur ".
            "<strong>&quot;{$user->getUsername()}&quot;</strong> à ".
            "bien été supprimé.");

        // Disconnects user
        if ($user == $this->getUser()) {
            $this->get('security.token_storage')->setToken(null);
            $request->getSession()->invalidate();
            return $this->redirectToRoute('app_homepage');
        }

        // User is redirected to referrer page
        return $this->redirectToRoute('app_homepage');
    }

    /**
     * Sets user roles.
     *
     * @Route("/add-role/{id}/{role}", requirements={"id": "\d+"}, name="user_add_role")
     */
    public function addRoleAction(Request $request, User $user, $role)
    {
        // User must log in
        if ( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') ) {
            $this->get('session')->getFlashBag()->add('error', "Vous devez être connecté pour réaliser cette action.");
            return $this->redirectToRoute('app_login');
        }

        // Only admins are allowed to perform this action
        if ( !in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ) {
            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à réaliser cette action.");
            return $this->redirectToRoute('app_homepage');
        }

        $user->addRole($role);
        $this->get('em')->save($user);
        $this->get('session')->getFlashBag()->add('success', "Le role <strong>&quot;$role&quot; à été attribué à ".
                                                             "&quot;{$user->getUsername()}&quot;</strong>.");

        // User is redirected to referrer page
        return $this->redirect( $request->headers->get('referer') );
    }

    /**
     * Removes user role.
     *
     * @Route("/remove-role/{id}/{role}", requirements={"id": "\d+"}, name="user_remove_role")
     */
    public function removeRoleAction(Request $request, User $user, $role)
    {
        // User must log in
        if ( !$this->get('security.authorization_checker')->isGranted('IS_AUTHENTICATED_REMEMBERED') ) {
            $this->get('session')->getFlashBag()->add('error', "Vous devez être connecté pour réaliser cette action.");
            return $this->redirectToRoute('app_login');
        }

        // Only admins are allowed to perform this action
        if ( !in_array('ROLE_ADMIN', $this->getUser()->getRoles()) ) {
            $this->get('session')->getFlashBag()->add('error', "Vous n'êtes pas autorisé à réaliser cette action.");
            return $this->redirectToRoute('app_homepage');
        }

        // Disconnects user who changes his own admin rights
        if ( $user == $this->getUser() ) {
            $this->get('security.token_storage')->setToken(null);
            $request->getSession()->invalidate();
            return $this->redirectToRoute('app_homepage');
        }

        $user->removeRole($role);
        $this->get('em')->save($user);
        $this->get('session')->getFlashBag()->add('success', "Le role <strong>&quot;$role&quot; à été retiré à ".
                                                             "&quot;{$user->getUsername()}&quot;</strong>.");

        // User is redirected to referrer page
        return $this->redirect( $request->headers->get('referer') );
    }
}
