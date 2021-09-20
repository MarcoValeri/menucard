<?php

namespace App\Controller;

use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class RegisterController extends AbstractController
{
    
    /**
     * @Route("/reg", name="reg")
     */
    public function reg(Request $request, UserPasswordEncoderInterface $passEncoder) // UserPasswordHasherInterface $passEncoder $passwordHasher
    {

        // Create form
        $regForm = $this->createFormBuilder()
        ->add('username', TextType::class, [
            'label' => 'Employee'
        ])
        ->add('password', RepeatedType::class, [
            'type' => PasswordType::class,
            'required' => true,
            'first_options' => ['label' => 'Password'],
            'second_options' => ['label' => 'Password Repeat']
        ])
        ->add('register', SubmitType::class)
        ->getForm();

        // Save form dat into the database
        $regForm->handleRequest($request);

        if ($regForm->isSubmitted()) {
            $input = $regForm->getData();
            
            $user = new User();
            $user->setUsername($input['username']);

            $user->setPassword(
                $passEncoder->encodePassword($user, $input['password']) //hasPassword()
            );

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            // redirect to the homepage after the login
            return $this->redirect($this->generateUrl('home'));

        }


        return $this->render('register/index.html.twig', [
            'regform' => $regForm->createView()
        ]);
    }
}
