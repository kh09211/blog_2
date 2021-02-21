<?php

namespace App\Form;

use App\Entity\Blog;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
//use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class BlogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [ 'label' => 'Titulo'])
            ->add('photo', TextType::class, ['label' => 'Foto URL'])
            ->add('text', TextareaType::class, ['label' => 'Texto']);
            /*                    This info is not needed in the form!
            ->add('created_at')
            ->add('user', EntityType::class, [
                // looks for choices from this entity
                'class' => User::class,

                // uses the User.username property as the visible option string
                'choice_label' => 'email',
            ]);
            */
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Blog::class,
        ]);
    }
}
