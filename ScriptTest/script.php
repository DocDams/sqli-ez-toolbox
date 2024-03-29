<?php
//chemin du directory que je copie
$configSource = __DIR__ . '/Ressources/config/ez_field_templates.yml';
//chemin directory ou je le copie
$configDestination = __DIR__ . '/../../../config/config_file.php';

if (!file_exists($configDestination)) {
    copy($configSource, $configDestination);
    echo "Fichier de configuration copié avec succès.";
} else {
    echo "Le fichier de configuration existe déjà.";
}
?>