<?php

namespace App\Controller;

use App\Entity\Customer;
use App\Form\CustomerType;
use App\Entity\Product;
use App\Entity\ProductType;
use App\Entity\Cart;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

class MainController extends AbstractController
{
    /**
     * @Route("/", name="main")
     */
    public function index(): Response
    {
        $data = $this->getDoctrine()->getManager()->getRepository(Customer::class)->findAll();
        return $this->render('main/index.html.twig', [
            'controller_name' => 'MainController',
            'data' => $data
        ]);
    }

    /**
     * @Route("/customer/{id}/cart", name="view_customer_cart")
     */
    public function viewCustomerCart($id)
    {
        $customer = $this->getDoctrine()->getManager()->getRepository(Customer::class)->find($id);
        $cart = $this->getDoctrine()->getManager()->getRepository(Cart::class)->findOneBy(['Customer' =>$id]);
        $ttlCart = 0;
        foreach($cart->getProducts() as $prod) {
            $ttlCart = $ttlCart + $prod->getPrice();
        }
        return $this->render('main/view.customer.cart.html.twig', [
            'controller_name' => 'MainController',
            'cart' => $cart,
            'customer' => $customer,
            'ttl' => $ttlCart
        ]);
    }

    /**
     * @Route("/create", name="create_customer")
     */
    public function createCustomer(Request $request)
    {
        $customer = new Customer();
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->flush();

            $this->addFlash('notice', 'Submitted Successfully');
            return $this->redirectToRoute('main');
        }

        return $this->render('main/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/update/{id}", name="update_customer")
     */
    public function update(Request $request, $id)
    {
        $customer = $this->getDoctrine()->getRepository(Customer::class)->find($id);
        $form = $this->createForm(CustomerType::class, $customer);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($customer);
            $em->flush();

            $this->addFlash('notice','Updated Successfully');

            return $this->redirectToRoute('main');
        }
        return $this->render('main/update.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete_customer")
     */
    public function delete($id)
    {
        $data = $this->getDoctrine()->getRepository(Customer::class)->find($id);
        $em = $this->getDoctrine()->getManager();
        $em->remove($data);
        $em->flush();

        $this->addFlash('notice', 'Deleted Successfully');

        return $this->redirectToRoute('main');
    }

    /**
     * @Route("/delete/cart/{cartid}/product/{productid}/{cid}",
     * name="delete_from_cart")
     */
    public function deleteFromCart($cartid, $productid, $cid)
    {
        $cart = $this->getDoctrine()->getRepository(Cart::class)->find($cartid);
        $product = $this->getDoctrine()->getRepository(Product::class)->find($productid);
        $em= $this->getDoctrine()->getManager();
        $cart->removeProduct($product);
        $em->persist($cart);
        $em->flush();

        return $this->redirectToRoute("view_customer_cart", ['id'=>$cid]);
    }
}
