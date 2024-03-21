<?php
declare(strict_types=1);

namespace App\Form\Type;


use App\Services\FieldType\SelectionFromEntity\Value;
use Doctrine\ORM\EntityManagerInterface;
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
            EntityType::class,
            [
                'class' => $fieldSettings['className'],
                'choice_value' => $fieldSettings['valueAttribute'],
                'choice_label' => $fieldSettings['labelAttribute'],
                'multiple' => true,
            ]
        );

        $fieldSettingsReference = &$fieldSettings;

        $builder->get('selection')
            ->addModelTransformer(new CallbackTransformer(
                function ($groups) use ($fieldSettingsReference): array {
                    $result = [];

                    if (null === $groups) {
                        return $result;
                    }

                    $ids = array_map(function ($group) {
                        return $group['id'];
                    }, $groups);

                    // Assuming you want to return an array here
                    return $ids;
                },
                // This function is just an example, adjust it according to your requirements
                function ($groupIds) use ($fieldSettingsReference) {
                    // Transform group IDs back to their original form if needed
                    // For example, fetch entities from IDs
                    return $this->entityManager->getRepository($fieldSettingsReference['className'])->findBy(['id' => $groupIds]);
                }
            ));
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
                'data_class' => Value::class,
        ]);
    }

}
