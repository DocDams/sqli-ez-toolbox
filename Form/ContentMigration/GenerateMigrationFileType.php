<?php


namespace SQLI\EzToolboxBundle\Form\ContentMigration;


use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GenerateMigrationFileType extends \Symfony\Component\Form\AbstractType
{

    /**
     * GenerateMigrationFile constructor.
     */
    public function __construct()
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $contentTypes = $options['contentTypes'];
        $contentTypeGroupIdentifier = $options['contentTypeGroupIdentifier'];
        foreach ($contentTypes as $key => $contentType) {
            if ($contentTypeGroupIdentifier != 'false') {
                $builder->add(
                    $contentType->identifier,
                    CheckboxType::class,
                    [
                        'label' => false,
                        'required' => false,
                    ]
                );
            } else {
                $builder->add(
                    $key,
                    CheckboxType::class,
                    [
                        'label' => false,
                        'required' => false,
                    ]
                );
            }
        }
        $builder
            ->add(
                'submit',
                SubmitType::class,
                [
                    'label' => 'generate migration file'
                ]
            );
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired('contentTypes')
        ->setRequired('contentTypeGroupIdentifier');
    }
}