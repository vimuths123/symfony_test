<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
//use Symfony\Bridge\Doctrine\Form\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class BookCategoryType extends AbstractType {

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options) {


        $builder->add('category', EntityType::class, array(
            'class' => 'AppBundle:Category',
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('c')
                                ->orderBy('c.name', 'ASC');
            },
            'choice_label' => 'name',
            'placeholder' => 'All categories',
            'required' => false,
        ));
    }

    /**
     * {@inheritdoc}
     */
//    public function configureOptions(OptionsResolver $resolver) {
//        $resolver->setDefaults(array(
//            'data' => 'cat',
//        ));
//    }

}
