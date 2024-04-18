<?php
declare(strict_types=1);

namespace SQLI\EzToolboxBundle\Form\Type;
;

use SQLI\EzToolboxBundle\Annotations\SQLIAnnotationManager;
use SQLI\EzToolboxBundle\Attributes\SQLIAttributesManager;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

final class SelectionFromEntitySettingsType extends AbstractType
{
    private const ANNOTATION = "annotation";
    public function __construct(
        private ContainerInterface $container,
        private SQLIAttributesManager $attributesManager,
        private SQLIAnnotationManager $annotationManager
    ) {
    }

    /**
     * Get the annotation mapping type from the configuration file
     *
     */
    public function getMappingType(): string
    {
        // Access the parameter from the container
        return $this->container->getParameter('sqli_ez_toolbox.mapping.type');
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $choices = [];
        $choice = [];

        $label= [];
        $value = [];

        $mapping_type = $this->getMappingType();
        if ($mapping_type == self::ANNOTATION) {
            $entities = $this->annotationManager->getAnnotatedClasses();
        } else {
            $entities = $this->attributesManager->getAttributedClasses();
        }

        foreach ($entities as $key => $value) {
           $choices[$key] = $value["classname"];
           // $choices[$key] = str_replace("\\","_",$key);
            //$choices[str_replace("\\","_",$key)]] = $key;
        }
        $builder->add('className', ChoiceType::class, [
            'choices' => $choices,
            'expanded' => false,
            'multiple' => false
        ]);

//        $builder->add('valueAttribute',ChoiceType::class, [
//            'choices' => $choice,
//            'expanded' => false,
//            'multiple' => false,
//        ]);
//                   ->addEventListener(
//                   FormEvents::PRE_SET_DATA,
//                   function (FormEvent $event) {
//                       $formData = $event->getData();
//                       dd($formData);
//                   });
//        $builder->add('className',TextType::class);
        $builder->add('valueAttribute',TextType::class);
        $builder->add('labelAttribute',TextType::class);
        $builder->add('filter',ChoiceType::class,[
            'choices' => [
                'orderBy Asc' => 'Asc',
                'orderBy Desc' => 'Desc',
            ],
            'expanded' => false,
            'multiple' => false,
        ]);



    }

}