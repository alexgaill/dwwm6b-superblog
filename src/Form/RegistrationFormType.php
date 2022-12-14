<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;

class RegistrationFormType extends AbstractType
{
    public function __construct(private Security $security){}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class)
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
            ])
            // ->add('plainPassword', PasswordType::class, [
            //     // instead of being set onto the object directly,
            //     // this is read and encoded in the controller
            //     'mapped' => false,
            //     'attr' => ['autocomplete' => 'new-password'],
            //     'constraints' => [
            //         new NotBlank([
            //             'message' => 'Please enter a password',
            //         ]),
            //         new Length([
            //             'min' => 6,
            //             'minMessage' => 'Your password should be at least {{ limit }} characters',
            //             // max length allowed by Symfony for security reasons
            //             'max' => 4096,
            //         ]),
            //     ],
            // ])
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'invalid_message' => "Les 2 mots de passe doivent ??tre identique",
                'mapped' => false,
                'options' => [
                    'attr' => ['autocomplete' => 'new-password'],
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Votre mot de passe doit avoir au moins {{ limit }} caract??res',
                            // max length allowed by Symfony for security reasons
                            'max' => 32,
                            'maxMessage' => 'Votre mot de passe ne doit pas d??passer {{ limit }} caract??res',
                        ]),
                    ],
                ],
                'required' => true,
                'first_options'  => ['label' => 'Password'],
                'second_options' => ['label' => 'R??p??tez votre Password'],
            ])
            ->add('nom', TextType::class)
            ->add('prenom', TextType::class)
            ;
            if ($this->security->getUser() && $this->security->isGranted("ROLE_ADMIN")) {
                $builder->add('roles', ChoiceType::class, [
                    'label' => "R??le de l'utilisateur",
                    'choices' => [
                        'Auteur' => "ROLE_AUTHOR",
                        'Admin' => "ROLE_ADMIN"
                    ],
                    'mapped' => false
                    ]);
            }
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
