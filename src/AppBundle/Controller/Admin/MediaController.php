<?php

/**
 * This file is part of the TangoManCMS package.
 *
 * Copyright (c) 2018 Matthias Morin <matthias.morin@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace AppBundle\Controller\Admin;

use AppBundle\Entity\Post;
use AppBundle\Entity\Tag;
use AppBundle\Form\Admin\AdminEditMediaType;
use AppBundle\Form\Admin\AdminNewMediaType;
use AppBundle\Form\FileUploadType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MediaController
 *
 * @author Matthias Morin <matthias.morin@gmail.com>
 * @Route("/admin/medias")
 */
class MediaController extends Controller
{

    /**
     * @Route("/")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        // Show searchable, sortable, paginated media list
        $em = $this->get('doctrine')->getManager();

        $listMedia = $em->getRepository('AppBundle:Post')->findByQuery(
            $request,
            [
                'type' => [
                    '360',
                    'argus360',
                    'csv',
                    'dailymotion',
                    'doc',
                    'document',
                    'embed',
                    'gif',
                    'giphy',
                    'gist',
                    'image',
                    'jpeg',
                    'jpg',
                    'ods',
                    'odt',
                    'pdf',
                    'png',
                    'pptx',
                    'thetas',
                    'tweet',
                    'txt',
                    'vimeo',
                    'xls',
                    'youtube',
                ],
            ]
        );

        return $this->render(
            'admin/media/index.html.twig',
            [
                'listMedia' => $listMedia,
            ]
        );
    }

    /**
     * @Route("/new")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $post = new Post();
        $post->setUser($this->getUser());
        $form = $this->createForm(AdminNewMediaType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists new media
            $em = $this->get('doctrine')->getManager();
            $em->persist($post);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'Le média <strong>&quot;'.$post
                .'&quot;</strong> a bien été ajouté.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/media/new.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @Route("/edit/{id}", requirements={"id": "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \AppBundle\Entity\Post                    $post
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, Post $post)
    {
        $form = $this->createForm(AdminEditMediaType::class, $post);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // Persists edited media
            $em = $this->get('doctrine')->getManager();
            $em->persist($post);
            $em->flush();
            // Displays success message
            $this->get('session')->getFlashBag()->add(
                'success',
                'Le média <strong>&quot;'.$post
                .'&quot;</strong> a bien été modifié.'
            );

            // User is redirected to referrer page
            return $this->redirect($request->get('callback'));
        }

        return $this->render(
            'admin/media/edit.html.twig',
            [
                'form'  => $form->createView(),
                'media' => $post,
            ]
        );
    }

    /**
     * @Route("/publish/{id}/{publish}", requirements={"id": "\d+", "publish":
     *                                   "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \AppBundle\Entity\Post                    $post
     * @param                                           $publish
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function publishAction(Request $request, Post $post, $publish)
    {
        $post->setPublished($publish);
        $em = $this->get('doctrine')->getManager();
        $em->persist($post);
        $em->flush();

        if ($publish) {
            $message = 'Le média <strong>&quot;'.$post
                       .'&quot;</strong> a bien été publié.';
        } else {
            $message = 'La publication du média <strong>&quot;'.$post
                       .'&quot;</strong> a bien été annulée.';
        }

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            $message
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }

    /**
     * @Route("/delete/{id}", requirements={"id": "\d+"})
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \AppBundle\Entity\Post                    $post
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function deleteAction(Request $request, Post $post)
    {
        // Deletes specified media
        $em = $this->get('doctrine')->getManager();
        $em->remove($post);
        $em->flush();

        // Send flash notification
        $this->get('session')->getFlashBag()->add(
            'success',
            'Le média <strong>&quot;'.$post
            .'&quot;</strong> a bien été supprimé.'
        );

        // User is redirected to referrer page
        return $this->redirect($request->get('callback'));
    }

    /**
     * @Route("/export")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exportAction(Request $request)
    {
        $em        = $this->get('doctrine')->getManager();
        $listMedia = $em->getRepository('AppBundle:Post')->export($request);

        return $this->render(
            'admin/media/export.html.twig',
            [
                'listMedia' => $listMedia,
            ]
        );
    }

    /**
     * @Route("/export-json")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function exportJSONAction(Request $request)
    {
        $em        = $this->get('doctrine')->getManager();
        $listMedia = $em->getRepository('AppBundle:Post')->export($request);
        $response  = json_encode($listMedia);

        return new Response(
            $response, 200, [
                'Content-Type'        => 'application/force-download',
                'Content-Disposition' => 'attachment; filename="listMedia.json"',
            ]
        );
    }

    /**
     * @Route("/import")
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function importAction(Request $request)
    {
        $form = $this->createForm(FileUploadType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $file = $request->files->get('file_upload')['file'];

            if ( ! $file->isValid()) {
                // Upload success check
                $this->get('session')->getFlashBag()->add(
                    'error',
                    'Une erreur s\'est produite lors du transfert.<br/>Veuillez réessayer.'
                );

                return $this->redirectToRoute('app_admin_media_import');
            }

            return $this->importJSON($file);
        }

        return $this->render(
            'admin/media/import.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    /**
     * @param $file
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function importJSON($file)
    {
        // Security checks
        $clientExtension = $file->getClientOriginalExtension();
        if ($file->getClientMimeType() !== 'application/json'
            && ! in_array(
                $clientExtension,
                ['json']
            )) {
            $this->get('session')->getFlashBag()->add(
                'error',
                'Ce format du fichier n\'est pas supporté.'
            );

            return $this->redirectToRoute('app_admin_media_import');
        }

        $counter = 0;
        $dupes   = 0;
        // File check
        if (is_file($file)) {
            // Load entities
            $em        = $this->get('doctrine')->getManager();
            $listMedia = $em->getRepository('AppBundle:Post');
            $tags      = $em->getRepository('AppBundle:Tag');
            $users     = $em->getRepository('AppBundle:User');

            // Creates "import" tag when non-existent
            $tag = $tags->findOneByName('Import');
            if ( ! $tag) {
                $tag = new Tag();
                $tag->setName('Import');
                $em->persist($tag);
                $em->flush();
            }

            foreach (json_decode(file_get_contents($file)) as $import) {

                // Check if media exists already
                if ( ! $listMedia->findOneBySlug($import->media_slug)) {
                    $counter++;

                    // Check if media author exists
                    $user = $users->findOneByEmail($import->user_email);
                    // When author doesn't exist sets current user as author
                    if ( ! $user) {
                        $user = $this->getUser();
                    }

                    $post = new Post();
                    $post->setUser($user)
                         ->setTitle($import->media_title)
                         ->setSlug($import->media_slug)
                         ->addTag($tag)
                         ->setText($import->media_text);

                    // $post->setCreated($import->media_created);
                    // $post->setModified($import->media_modified);

                    $em->persist($post);
                    $em->flush();
                }
            }

            if ($counter > 1) {
                $success = $counter.'medias ont été importés.';
            } else {
                $success = 'Aucun media n\'a été importé.';
            }

            $this->get('session')->getFlashBag()->add('success', $success);
        }

        return $this->redirectToRoute('app_admin_media_index');
    }
}
