<?php
declare(strict_types=1);

namespace SQLI\EzToolboxBundle\Form\Type;


use Doctrine\ORM\EntityManagerInterface;
use SQLI\EzToolboxBundle\Entity\Doctrine\GroupMail;
use SQLI\EzToolboxBundle\FieldType\SelectionFromEntity\ReverseTrans;
use SQLI\EzToolboxBundle\FieldType\SelectionFromEntity\Value;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Choice;

class SelectionFromEntity extends AbstractType
{
    private $serializer;

    public function __construct(private EntityManagerInterface $entityManager)
    {
    }



    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $fieldSettings = $options['data'];
        $className = $fieldSettings['className'];
        $ids = $fieldSettings['labelAttribute'];
        $label = $fieldSettings['valueAttribute'];
        $filter = $fieldSettings['filter'];
        $builder->add(
            'selection',
            EntityType::class,
            [
                'class' => $className,
                'choice_value' => $label,
                'choice_label' => $ids,
                'multiple' => true,
            ]
        ) ->addModelTransformer(new CallbackTransformer(
            function ($groups) use ($fieldSettings): Value {
                $className = $fieldSettings['className'];
                $ids = $fieldSettings['valueAttribute'];
                $label = $fieldSettings['labelAttribute'];
                $result = [];
                $filter = $fieldSettings['filter'];

                if (null === $groups) {
                    return $result;
                }

                return new Value($this->entityManager
                        ->getRepository($className)
                        ->findBy(
                            [$ids => $label],
                            [$ids => $filter],
                            5
                        ))
                        ;
            },
            function ($group): mixed {
                return ($group);
            }
        ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {

        $resolver->setDefaults([
            'data_class' => Value::class,
        ]);
    }

}