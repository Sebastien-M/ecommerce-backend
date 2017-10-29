<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Utilisateur;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Form\UtilisateurType;
use AppBundle\Entity\Panier;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Utilisateur controller.
 *
 * @Route("utilisateur")
 */
class UtilisateurController extends Controller {

    /**
     * Lists all utilisateur entities.
     *
     * @Route("/", name="utilisateur_index")
     * @Method("GET")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $utilisateurs = $em->getRepository('AppBundle:Utilisateur')->findAll();

        return $this->render('utilisateur/index.html.twig', array(
                    'utilisateurs' => $utilisateurs,
        ));
    }

    /**
     * Creates a new utilisateur entity.
     *
     * @Route("/new", name="utilisateur_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $utilisateur = new Utilisateur();
        $form = $this->createForm('AppBundle\Form\UtilisateurType', $utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($utilisateur);
            $em->flush();

            return $this->redirectToRoute('utilisateur_show', array('id' => $utilisateur->getId()));
        }

        return $this->render('utilisateur/new.html.twig', array(
                    'utilisateur' => $utilisateur,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Creates a new utilisateur entity accessible from api.
     *
     * @Route("/new/user", name="utilisateur_new")
     * @Method({"POST"})
     */
    public function newUserAction(Request $request) {
        $utilisateur = new Utilisateur();
        $form = $this->createForm(UtilisateurType::class, $utilisateur);
        $form->submit($request->request->all());
        if ($form->isValid()) {
            $usermail = $this->getDoctrine()
                    ->getRepository('AppBundle:Utilisateur')
                    ->findOneBy(array('email' => $utilisateur->getEmail()));
            $username = $this->getDoctrine()
                    ->getRepository('AppBundle:Utilisateur')
                    ->findOneBy(array('username' => $utilisateur->getUsername()));
            if (count($usermail) > 0 || count($username) > 0) {
                return new JsonResponse('User already exists', 403);
            } else {
                $em = $this->get('doctrine.orm.entity_manager');
                $panier = new Panier();
                $em->persist($panier);
                
                $em->flush();
                $panierId = $panier->getId();
                $utilisateur->setPanier($panier);
                $em->persist($utilisateur);
                $em->flush();
                dump($utilisateur);
                return new Response("ok", 201, array('Access-Control-Allow-Origin' => '*'));
            }
        } else {
            return new JsonResponse('Form not completed', 400);
        }
    }

    /**
     * Deletes a utilisateur entity with api.
     *
     * @Route("/delete/{id}", name="utilisateur_delete_api")
     * @Method("DELETE")
     */
    public function deleteApiAction(Request $request, $id) {
        $user = $this->getDoctrine()
                ->getRepository('AppBundle:Utilisateur')
                ->findOneBy(array('id' => $id));

        if (!empty($user)) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($user);
            $em->flush();
            return new JsonResponse("Deleted", 200, array('Access-Control-Allow-Origin' => '*'));
        }
        return new JsonResponse("No user found with the id " . $id, 400, array('Access-Control-Allow-Origin' => '*'));
    }

    /**
     * Return true if username and password matches.
     *
     * @Route("/connect", name="utilisateur_connect")
     * @Method("POST")
     */
    public function connectAction(Request $request) {

        $user_manager = $this->get('fos_user.user_manager');
        $factory = $this->get('security.encoder_factory');

//        $user = $user_manager->loadUserByUsername($request->get("username"));
        $user = $this->getDoctrine()
                ->getRepository('AppBundle:Utilisateur')
                ->findOneBy(array('username' => $request->get("username")));

        $encoder = $factory->getEncoder($user);

        $bool = ($encoder->isPasswordValid($user->getPassword(), $request->get("password"), $user->getSalt())) ? "true" : "false";

        if($bool == "true"){
            return new JsonResponse("ok", 200, array('Access-Control-Allow-Origin' => '*'));
        }
        else{
            return new JsonResponse("bad username or password", 400, array('Access-Control-Allow-Origin' => '*'));
        }
        

    }

    /**
     * Finds and displays a utilisateur entity.
     *
     * @Route("/{id}", name="utilisateur_show")
     * @Method("GET")
     */
    public function showAction(Utilisateur $utilisateur) {
        $deleteForm = $this->createDeleteForm($utilisateur);

        return $this->render('utilisateur/show.html.twig', array(
                    'utilisateur' => $utilisateur,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing utilisateur entity.
     *
     * @Route("/{id}/edit", name="utilisateur_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Utilisateur $utilisateur) {
        $deleteForm = $this->createDeleteForm($utilisateur);
        $editForm = $this->createForm('AppBundle\Form\UtilisateurType', $utilisateur);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('utilisateur_edit', array('id' => $utilisateur->getId()));
        }

        return $this->render('utilisateur/edit.html.twig', array(
                    'utilisateur' => $utilisateur,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a utilisateur entity.
     *
     * @Route("/{id}", name="utilisateur_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Utilisateur $utilisateur) {
        $form = $this->createDeleteForm($utilisateur);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($utilisateur);
            $em->flush();
        }

        return $this->redirectToRoute('utilisateur_index');
    }

    /**
     * Creates a form to delete a utilisateur entity.
     *
     * @param Utilisateur $utilisateur The utilisateur entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Utilisateur $utilisateur) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('utilisateur_delete', array('id' => $utilisateur->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
