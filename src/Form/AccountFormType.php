<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Callback;
use Symfony\Component\Validator\Constraints\EqualTo;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

class AccountFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
            ])
            ->add('adress', TextType::class, [
                'label' => 'Address',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Email',
            ])
            ->add('newPassword', PasswordType::class, [
                'label' => 'New Password',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Length([
                        'min' => 8,
                        'minMessage' => 'Your password should be at least {{ limit }} characters',
                        'max' => 4096,
                    ]),
                    new Regex([
                        'pattern' => '/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
                        'message' => 'Your password must contain at least one uppercase letter, one lowercase letter, one number, and one special character.',
                    ]),
                ],
            ])
            ->add('confirmNewPassword', PasswordType::class, [
                'label' => 'Confirm New Password',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Callback(function ($value, ExecutionContextInterface $context) {
                        $form = $context->getRoot(); // Get the entire form
                        $newPassword = $form->get('newPassword')->getData();

                        if (!empty($newPassword) && $value !== $newPassword) {
                            $context->buildViolation('The passwords must match.')
                                ->addViolation();
                        }
                    }),
                ],
            ])
            ->add('currentPassword', PasswordType::class, [
            'label' => 'Current Password',
            'mapped' => false, // This field is not mapped to the entity
            'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please confirm your password',
                    ])
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}