<?php

namespace SQLI\EzToolboxBundle\Form\Type;


use Doctrine\ORM\EntityManagerInterface;
use SQLI\EzToolboxBundle\Entity\Doctrine\GroupMail;
use SQLI\EzToolboxBundle\FieldType\SqliNewsLetter\Value;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SqliNewsLetter extends AbstractType
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add(
            'selection',
            EntityType::class,
            [
                'class' => "App\Entity\Doctrine\GroupMail",
                'choice_label' => 'groupName',
                'multiple' => true,
            ]
        );

        $builder->get('selection')
            ->addModelTransformer(new CallbackTransformer(
                function ($groups): array {
                    $result = [];

                    if (null === $groups) {
                        return $result;
                    }

                    $ids = array_map(function ($group) {
                        return $group['id'];
                    }, $groups);

                    return $this->entityManager
                        ->getRepository("App\Entity\Doctrine\GroupMail")
                        ->findBy(['id' => $ids])
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
            'data_class' => Value::class,
        ]);
    }
}
