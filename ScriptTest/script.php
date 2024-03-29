<?php
// Chemin vers le fichier source que vous souhaitez copier
$configSource = __DIR__ . '/Ressources/config/ez_field_templates.yml';

// Chemin vers le répertoire de destination où vous souhaitez copier le fichier
$configDestination = __DIR__ . '/../../../config/packages/app/test.yaml';
// Vérifie si le fichier de destination existe déjà
if (!file_exists($configDestination)) {
    // Copie le fichier depuis le répertoire source vers le répertoire de destination
    if (copy($configSource, $configDestination)) {
        echo "Fichier de configuration copié avec succès.";
    } else {
        echo "Une erreur s'est produite lors de la copie du fichier de configuration.";
    }
} else {
    echo "Le fichier de configuration existe déjà dans le répertoire de destination.";
}
?>