<?php
declare(strict_types=1);

namespace SQLI\EzToolboxBundle\Form\Type;
;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SelectionFromEntitySettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

       // $entities = $this->getSQLIAnnotations() ;
//        $builder->add('className',ChoiceType::class,[
//            'choices' => $entities,
//            'expanded' => false,
//            'multiple' => false,
//        ]);
        $builder->add('className',TextType::class);
        $builder->add('valueAttribute',TextType::class);
        $builder->add('labelAttribute',TextType::class);
//        $builder->add('filter',ChoiceType::class,[
//            'choices' => [
//                'orderBy Asc' => true,
//                'orderBy Desc' => false,
//            ],
//            'expanded' => false,
//            'multiple' => false,
//        ]);


    }
//    public function configureOptions(OptionsResolver $resolver): void
//    {
//        $resolver->setDefaults([
//            'data_class' => null,
//        ]);
//    }
}