<?php
declare(strict_types=1);

namespace SQLI\EzToolboxBundle\Form\Type;


use Doctrine\ORM\EntityManagerInterface;
use SQLI\EzToolboxBundle\Attributes\SQLIAttributesManager;
use SQLI\EzToolboxBundle\FieldType\SelectionFromEntity\Value;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use SQLI\EzToolboxBundle\Annotations\SQLIAnnotationManager;

class SelectionFromEntity extends AbstractType
{
    private $serializer;
    private const ANNOTATION = "annotation";
    public function __construct(private EntityManagerInterface $entityManager,
                                private ContainerInterface $container,
                                private SQLIAttributesManager $attributesManager,
                                private SQLIAnnotationManager $annotationManager)
    {
    }
    public function getMappingType(): string
    {
        // Access the parameter from the container
        return $this->container->getParameter('sqli_ez_toolbox.mapping.type');
    }


    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $fieldSettings = $options['data'];
        $className = $fieldSettings['className'];
        $ids = $fieldSettings['valueAttribute'];
        $label = $fieldSettings['labelAttribute'];
        $filter = $fieldSettings['filter'];
        $mapping_type = $this->getMappingType();
        if ($mapping_type === self::ANNOTATION) {
            $entities = $this->annotationManager->getAnnotatedClasses();
        } else {
            $entities = $this->attributesManager->getAttributedClasses();
        }
        foreach ($entities as $key => $value) {
            $choices[$key] = $value["classname"];
        }
        if(in_array($className,$choices)) {
            $classPath= array_search($className, $choices);
        }
        $builder->add(
            'selection',
            EntityType::class,
            [
                'class' => $classPath,
                'choice_value' => $ids,
                'choice_label' => $label,
                'multiple' => true,
            ]
        ) ->addModelTransformer(new CallbackTransformer(
            function ($groups) use ($classPath, $fieldSettings): Value {
// Transform mon result en value
                $ids = $fieldSettings['valueAttribute'];
                $label = $fieldSettings['labelAttribute'];
                $result = [];
                $filter = $fieldSettings['filter'];

                if (null === $groups) {
                    return $result;
                }
                // requete result dans create content
                return new Value($this->entityManager
                        ->getRepository($classPath)
                        ->findBy(

                            [$ids => $label], //'id' => 'email'
                            [$ids => $filter],
                            5
                        ))
                        ;

            },
            function ($group): mixed {
                // resultat present lors du publish du content
                // reverse transform erreur serializer
                dd($group);
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