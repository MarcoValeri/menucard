<?php

namespace App\Controller;

use App\Entity\Dish;
use App\Form\DishType; // Import form
use App\Repository\DishRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/dish", name="dish.")
 */
class DishController extends AbstractController
{
    /**
    * @Route("/", name="edit")
    */
    public function index(DishRepository $dr): Response // add the name of the repository as parameter
    {

        $dishes = $dr->findAll(); // Create a query by Repository fill and save into a variable

        return $this->render('dish/index.html.twig', [
            'dishes' => $dishes, // Pass the variable into the template
        ]);
    }

    /**
     * @Route("/create", name="create")
     */
    public function create(Request $request) {
        $dish = new Dish();

        // Form
        $form = $this->createForm(DishType::class, $dish);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            // EntityManager
            $em = $this->getDoctrine()->getManager();

            $image = $request->files->get('dish')['attachment'];

            if ($image) {
                $filename = md5(uniqid()) . '.' . $image->guessClientExtension();
            }

            $image->move(
                $this->getParameter('images_folder'),
                $filename
            );

            $dish->setImage($filename);
            $em->persist($dish);
            $em->flush();

            return $this->redirect($this->generateUrl('dish.edit')); // redirect the user after submit the form
        }

        // Response the form in a template
        return $this->render('dish/create.html.twig', [
            'createForm' => $form->createView()
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function remove($id, DishRepository $dr) {

        $em = $this->getDoctrine()->getManager();
        $dish = $dr->find($id);
        $em->remove($dish);
        $em->flush();

        // message
        $this->addFlash('success', 'Remove dish successfuly');

        return $this->redirect($this->generateUrl('dish.edit'));

    }

    /**
     * @Route("/show/{id}", name="show")
     */
    public function show(Dish $dish) {

        return $this->render('dish/show.html.twig', [
            'dish' => $dish,
        ]);

    }

}
