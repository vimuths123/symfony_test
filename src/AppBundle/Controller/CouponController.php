<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Coupon;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

/**
 * Coupon controller.
 *
 * @Route("admin/coupon")
 */
class CouponController extends Controller {

    /**
     * Lists all coupon entities.
     *
     * @Route("/", name="coupon_index")
     * @Method("GET")
     */
    public function indexAction() {
        $em = $this->getDoctrine()->getManager();

        $coupons = $em->getRepository('AppBundle:Coupon')->findAll();

        return $this->render('coupon/index.html.twig', array(
                    'coupons' => $coupons,
        ));
    }

    /**
     * Creates a new coupon entity.
     *
     * @Route("/new", name="coupon_new")
     * @Method({"GET", "POST"})
     */
    public function newAction(Request $request) {
        $coupon = new Coupon();
        $form = $this->createForm('AppBundle\Form\CouponType', $coupon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($coupon);
            $em->flush();

            return $this->redirectToRoute('coupon_show', array('id' => $coupon->getId()));
        }

        return $this->render('coupon/new.html.twig', array(
                    'coupon' => $coupon,
                    'form' => $form->createView(),
        ));
    }

    /**
     * Finds and displays a coupon entity.
     *
     * @Route("/{id}", name="coupon_show")
     * @Method("GET")
     */
    public function showAction(Coupon $coupon) {
        $deleteForm = $this->createDeleteForm($coupon);

        return $this->render('coupon/show.html.twig', array(
                    'coupon' => $coupon,
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Displays a form to edit an existing coupon entity.
     *
     * @Route("/{id}/edit", name="coupon_edit")
     * @Method({"GET", "POST"})
     */
    public function editAction(Request $request, Coupon $coupon) {
        $deleteForm = $this->createDeleteForm($coupon);
        $editForm = $this->createForm('AppBundle\Form\CouponType', $coupon);
        $editForm->handleRequest($request);

        if ($editForm->isSubmitted() && $editForm->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('coupon_edit', array('id' => $coupon->getId()));
        }

        return $this->render('coupon/edit.html.twig', array(
                    'coupon' => $coupon,
                    'edit_form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
        ));
    }

    /**
     * Deletes a coupon entity.
     *
     * @Route("/{id}", name="coupon_delete")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, Coupon $coupon) {
        $form = $this->createDeleteForm($coupon);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($coupon);
            $em->flush();
        }

        return $this->redirectToRoute('coupon_index');
    }

    /**
     * Creates a form to delete a coupon entity.
     *
     * @param Coupon $coupon The coupon entity
     *
     * @return \Symfony\Component\Form\Form The form
     */
    private function createDeleteForm(Coupon $coupon) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('coupon_delete', array('id' => $coupon->getId())))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

}
