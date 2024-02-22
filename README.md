SQLI eZ Toolbox Bundle
========================================

[SQLI](http://www.sqli.com) eZToolbox is a bundle used in SQLI projects gathering some bundles like "SQLI Entities Manager", "SQLI Command Toolbox", some helpers and some Twig operators
Compatible with Ibexa 3.3

Installation
------------

### Install with composer
```
composer require sqli/eztoolbox=^2.0
```

### Register the bundle

Activate the bundle in `config/bundles.php` AFTER all eZSystem/Ibexa bundles

```php
// config/bundles.php

return [
    // ...
    SQLI\EzToolboxBundle\SQLIEzToolboxBundle::class => ['all' => true],
];
```

### Add routes

In `config/routes/sqli_eztoolbox.yaml` :

```yml
# SQLI Admin routes
_sqli_eztoolbox:
    resource: "@SQLIEzToolboxBundle/Resources/config/routing.yaml"
    prefix: /
```
### Using attributes 

If you're integrating new entities using attribute-based mapping in your Symfony application, you need to update the Doctrine configuration in the `doctrine.yaml` file to include these new mappings.

1. **Navigate to Doctrine Configuration**:

   Open the `doctrine.yaml` file located in `/config/packages/doctrine.yaml`.

2. **Update Mappings Section**:

   Add or modify the `mappings` section to include the new entity namespace and directory:

    ```yaml
    mappings:
        Test:
            is_bundle: false
            dir: '%kernel.project_dir%/src/Entity/Test'
            prefix: 'App\Entity\Test'
            alias: Test
            type: attribute
    ```

   Replace `Test` with your desired alias for the entity namespace. Ensure that the `dir` points to the correct directory containing your entity classes.

3. **Save Changes**:

   Save the changes to the `doctrine.yaml` file.

4. **Clear Cache (if needed)**

### Clear cache

```bash
php bin/console cache:clear
```

### Parameters

##### Full example

In `config/packages/sqli_eztoolbox.yaml` add the localisations and namespaces of the entities :

```yaml
sqli_ez_toolbox:
    entities:
        - { directory: 'Entity/Doctrine', namespace: 'App\Entity'}
    admin_logger:
        enabled: true
    storage_filename_cleaner:
        enabled: true
```

### How to use

*(Optional) Change label tabname*

You can change label of the default tab using this translation key for domain `sqli_admin` : **sqli_admin__menu_entities_tab__default**

[Entities Manager](doc/README_entities_manager.md)

[Toolbox](doc/README_toolbox.md)

### Other

[Changelogs](doc/CHANGELOGS.md)

[Upgrade](doc/UPGRADE.md)
