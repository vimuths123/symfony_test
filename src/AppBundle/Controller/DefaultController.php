<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Book;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Response;

//@Cache(expires="+3000 seconds")

class DefaultController extends Controller {

    /**
     * @Route("/admin")
     */
    public function adminAction() {
        return new Response('<html><body>Admin page!</body></html>');
    }

    /**
     * @Route("/", name="homepage")
     * @Cache(expires="+3000 seconds")
     */
    public function indexAction(Request $request) {
        $session = $request->getSession();

        if ($request->query->has('action')) {
            switch ($request->query->get('action')) {
                case "add":
                    if (!empty($request->request->get('quantity'))) {

                        $em = $this->getDoctrine()->getManager();
                        $bookByCode = $em->getRepository('AppBundle:Book')
                                ->findOneBy(
                                ['code' => $request->query->get('code')]
                        );

                        $itemArray = array(
                            $bookByCode->getCode() =>
                            array(
                                'name' => $bookByCode->getName(),
                                'code' => $bookByCode->getCode(),
                                'quantity' => $request->request->get('quantity'),
                                'price' => $bookByCode->getPrice(),
                                'other' => array(
                                    'category' => $bookByCode->getCategory()->getId(),
                                    'category_name' => $bookByCode->getCategory()->getName(),
                                ),
                            )
                        );

                        if ($session->has('cart_item')) {
                            if (in_array($bookByCode->getCode(), array_keys($session->get('cart_item')))) {
                                $itm_arr = $session->get('cart_item');
                                if (empty($itm_arr[$bookByCode->getCode()]["quantity"])) {
                                    $itm_arr[$bookByCode->getCode()]["quantity"] = 0;
                                }
                                $itm_arr[$bookByCode->getCode()]["quantity"] += $request->request->get('quantity');
                                $session->set('cart_item', $itm_arr);
                            } else {
                                $session->set('cart_item', array_merge($session->get('cart_item'), $itemArray));
                            }
                        } else {
                            $session->set('cart_item', $itemArray);
                        }
                    }
                    break;

                case "remove":
                    if (!empty($session->get('cart_item'))) {
                        $itm_arr = $session->get('cart_item');
                        unset($itm_arr[$request->query->get('code')]);
                        $session->set('cart_item', $itm_arr);
                        if (empty($session->get('cart_item'))) {
                            $session->remove('cart_item');
                        }
                    }
                    break;

                case "empty":
                    $session->remove('cart_item');
                    break;

                default:
                    break;
            }
        }

        $category_form = $this->createForm('AppBundle\Form\BookCategoryType');
        $category_form->handleRequest($request);
        if ($category_form->isSubmitted() && $category_form->isValid()) {
            $data = $category_form->getData();
            if ($data['category'] == null) {
                $books = $this->getDoctrine()
                        ->getRepository('AppBundle:Book')
                        ->findAll();
            } else {
                $books = $this->getDoctrine()
                        ->getRepository('AppBundle:Book')
                        ->findBy([
                    'category' => $data['category']->getId()
                ]);
            }
        } else {
            $books = $this->getDoctrine()
                    ->getRepository('AppBundle:Book')
                    ->findAll();
        }

        return $this->render('default/index.html.twig', [
                    'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
                    'books' => $books,
                    'category_form' => $category_form->createView(),
        ]);
    }

    /**
     * @Route("/checkout", name="checkout")
     */
    public function checkoutAction(Request $request) {
        $session = $request->getSession();
        $categoty_discount = 0;
        $childrenbook = 0;
        $child_total = 0;
        $all_categoty_discount = 0;
        $total = 0;

        if ($request->query->has('action')) {
            switch ($request->query->get('action')) {
                case "remove":
                    if (!empty($session->get('cart_item'))) {
                        $itm_arr = $session->get('cart_item');
                        unset($itm_arr[$request->query->get('code')]);
                        $session->set('cart_item', $itm_arr);
                        if (empty($session->get('cart_item'))) {
                            $session->remove('cart_item');
                        }
                    }
                    break;
            }
        }

        $categories = $this->getDoctrine()
                ->getRepository('AppBundle:Category')
                ->findAll();



        if ($session->has('cart_item')) {
            $cart = $session->get('cart_item');
            foreach ($cart as $key => $value) {
                if ($value['other']['category_name'] == 'Children') {
                    $childrenbook += $value['quantity'];
                    $child_total += $value['price'] * $value['quantity'];
                }
                $total += $value['price'] * $value['quantity'];
            }

            if (!empty($categories)) {
                $cat_count = count($categories);
                $ten_book_cats = 0;

                foreach ($categories as $catkey => $catvalue) {
                    $quantity = 0;
                    foreach ($cart as $cartkey => $cartvalue) {
                        if ($cartvalue['other']['category_name'] == $catvalue->getName()) {
                            $quantity += $cartvalue['quantity'];
                        }
                    }
                    if ($quantity >= 10) {
                        $ten_book_cats += 1;
                    }
                }

                if ($ten_book_cats == $cat_count) {
                    $all_categoty_discount = 0.05 * $total;
                }
            }
        }

        if ($childrenbook >= 5) {
            $categoty_discount = 0.1 * $child_total;
        }

        return $this->render('default/checkout.html.twig', [
                    'base_dir' => realpath($this->getParameter('kernel.project_dir')) . DIRECTORY_SEPARATOR,
                    'categoty_discount' => $categoty_discount,
                    'all_categoty_discount' => $all_categoty_discount,
                    'total' => $total,
        ]);
    }

    /**
     * @Route("/validate_coupon", name="validate_coupon")
     */
    public function validateCouponAction(Request $request) {
        $code = $request->request->get('code');

        $coupon = $this->getDoctrine()
                ->getRepository('AppBundle:Coupon')
                ->findBy([
            'code' => $code
        ]);

        if (!empty($coupon)) {
            return new Response('valid');
        } else {
            return new Response('not valid');
        }
    }

}
