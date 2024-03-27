<?php
declare(strict_types=1);

namespace SQLI\EzToolboxBundle\Form\Type;


use Doctrine\ORM\EntityManagerInterface;
use Ibexa\Contracts\Core\FieldType\Value;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

final class SelectionFromEntity extends AbstractType
{
    public function __construct(EntityManagerInterface $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $fieldSettings = $options['data'];

        $builder->add(
            'selection',
            ChoiceType::class,
           // EntityType::class,
            [
                //'class' => $fieldSettings['className'],
                'choice_value' => $fieldSettings['valueAttribute'],
                'choice_label' => $fieldSettings['labelAttribute'],
                'multiple' => true,
                'choices' => $this->entityManager->getRepository($fieldSettings['className'])->findBy([])

            ]
        );
        $builder->get('selection')
            ->addModelTransformer(new CallbackTransformer(
                function ($groups) use ($fieldSettings): array {
                    $result = [];
                    if (null === $groups) {
                        return $result;
                    }

                    $ids = array_map(function ($group) {
                        return $group['id'];
                    }, $groups);


                   return $this->entityManager
                        ->getRepository($fieldSettings['className'])
                        ->findBy([$fieldSettings['valueAttribute'] => $ids])
                        ;
                },
                function ($group): array {
                    return $group;
                }
            ))
        ;

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
//            'data_class' => Value::class,
        ]);

    }

}
