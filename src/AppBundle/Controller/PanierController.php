<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Panier;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;use Symfony\Component\HttpFoundation\Request;

/**
 * Panier controller.
 *
 * @Route("panier")
 */
class PanierController extends Controller
{
    /**
     * Lists all panier entities.
     *
     * @Route("/", name="panier_index")
     * @Method("GET")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getManager();

        $paniers = $em->getRepository('AppBundle:Panier')->findAll();

        return $this->render('panier/index.html.twig', array(
            'paniers' => $paniers,
        ));
    }

    /**
     * Creates a new panier entity.
     *
     * @Route("/new", name="panier_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request)
    {
        $panier = new Panier();
        $form = $this->createForm('AppBundle\Form\PanierType', $panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($panier);
            $em->flush();

            return $this->redirectToRoute('panier_show', array('id' => $panier->getId()));
        }

        return $this->render('panier/new.html.twig', array(
            'panier' => $panier,
            'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a panier entity.
     *
     * @Route("/{id}", name="panier_show")
     * @Method("GET")
     */
    public function showAction(Panier $panier)
    {
        $deleteForm = $this->createDeleteForm($panier);

        return $this->render('panier/show.html.twig', array(
            'panier' => $panier,
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing panier entity.
     *
     * @Route("/{id}/edit", name="panier_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Panier $panier)
    {
        $deleteForm = $this->createDeleteForm($panier);
        $editForm = $this->createForm('AppBundle\Form\PanierType', $panier);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('panier_edit', array('id' => $panier->getId()));
        }

        return $this->render('panier/edit.html.twig', array(
            'panier' => $panier,
            'edit_form' => $editForm->createView(),
            'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a panier entity.
     *
     * @Route("/{id}", name="panier_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Panier $panier)
    {
        $form = $this->createDeleteForm($panier);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($panier);
            $em->flush();
        }

        return $this->redirectToRoute('panier_index');
    }

    /**
     * Creates a form to delete a panier entity.
     *
     * @param Panier $panier The panier entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Panier $panier)
    {
        return $this->createFormBuilder()
            ->setAction($this->generateUrl('panier_delete', array('id' => $panier->getId())))
            ->setMethod('DELETE')
            ->getForm()
        ;
    }
}
