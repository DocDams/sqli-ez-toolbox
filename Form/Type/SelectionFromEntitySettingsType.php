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
//    protected function getSQLIAnnotations(): array
//    {
//        $annotatedClasses = [];
//
//        // Scan all files into directories defined in configuration
//        foreach ($this->directories as $entitiesMapping) {
//            $directory = $entitiesMapping['directory'];
//            $namespace = $entitiesMapping['namespace'];
//            if (is_null($namespace)) {
//                $namespace = str_replace('/', '\\', $directory);
//            }
//
//            $path = $this->projectDir . '/src/' . $directory;
//            $finder = new Finder();
//            $finder->depth(0)->files()->in($path);
//            $annotatedClasses = $this->getAnnotatedClassesArray($finder, $namespace, $annotatedClasses);
//        }
//
//        return $annotatedClasses;
//    }

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
        $builder->add('filter',ChoiceType::class,[
            'choices' => [
                'orderBy Asc' => true,
                'orderBy Desc' => false,
            ],
            'expanded' => false,
            'multiple' => false,
        ]);


    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}