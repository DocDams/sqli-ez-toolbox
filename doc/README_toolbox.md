SQLI Toolbox
========================

### Configuration

Default values :
```yml
sqli_ez_toolbox:
    admin_logger:
        enabled: false
    storage_filename_cleaner:
        enabled: true
```

### Services

```shell script
FieldHelper
FetchHelper
DataFormattedHelper
```


### Twig Operators

Default values :
```twig
ez_parameter( 'parameter_name', 'namespace' ) {# ConfigResolver #}
content_name( Content ) {# ContentName even if we don't have access to this content #}
is_anonymous_user() {# Check if connected user is an anonymous user #}
empty_field( Content, 'field_identifier', ?'languagecode' ) {# Check if field exists and empty #}
fetch_location( locationId ) {# Fetch a Location object #}
fetch_content( contentId ) {# Fetch a Content object #}
fetch_ancestor( locationId|Location, 'contentTypeIdentifier' ) {# Search first ancestor of specified content type identifier #}
fetch_children( locationId|Location, ?'contentTypeIdentifier' ) {# Retrieve all children filtered with specified content type identifier #}
render_children( locationId|Location, 'viewType', 'contentTypeIdentifier', ?'params to template )
bundle_exists( 'bundle classname' ) {# Check if specified bundle is activated #}
```

### Parameter Handler

Your parameter handler must implement `SQLI\EzToolboxBundle\Services\Parameter\ParameterHandlerInterface` and your service's declaration must be tagged with `sqli.parameter_handler`

Example with "maintenance" handler:
```yaml
    SQLI\EzToolboxBundle\Services\Parameter\ParameterHandlerMaintenance:
        tags:
            - { name: sqli.parameter_handler }
```

All handlers can be accessible through a repository : `SQLI\EzToolboxBundle\Services\Parameter\ParameterHandlerRepository`

### PrepublishVersionSignal

A signal slot exists to make an action just before Content publication : `SQLI\EzToolboxBundle\Services\Core\Signal\PrepublishVersionSignal`

### Admin Logger

Save some actions made in backoffice into a log file

### Storage filename cleaner

Clean name of the file uploaded in backoffice when moving into storage to prevent SEO penalties.
Change special characters to their latin correspondence when it's possible (else they will be removed), replace spaces and force lower case.

### IP CIDR Validator

You can check if an IP is in CIDR range with this :

```php
$ipConstraint = new \SQLI\EzToolboxBundle\Validator\Constraints\IpCidr(['cidr' => "192.168.0.0/24"]);
$errors = $this->validator->validate( '192.168.1.10', $ipConstraint );

if(count($errors)) {
    $message = $errors[0]->getMessage();
}
```

In this case, $message contains `192.168.1.10 not validated with CIDR mask 192.168.0.0/24`