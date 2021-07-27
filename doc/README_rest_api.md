SQLI Rest Api
========================

## Get an entity

### Request

`GET /api/{fqcn}`\
 `Example with an entity named POST : api/App%5CEntity%5CPost/`
 
##### Curl

    curl --location --request GET 'http://ibexa33.localhost/api/App%5CEntity%5CPost/'
    
##### Php

    <?php

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://ibexa33.localhost/api/App%255CEntity%255CPost/',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
      ));

      $response = curl_exec($curl);

      curl_close($curl);
      echo $response;

### Response

    {
        "fqcn": "App\\Entity\\Post",
        "class": {
            "classname": "Post",
            "annotation": {
                "create": true,
                "update": true,
                "delete": true,
                "description": "Paramètrage",
                "max_per_page": 10,
                "csv_exportable": false,
                "tabname": "default"
            },
            "properties": {
                "id": {
                    "accessibility": "private",
                    "visible": true,
                    "readonly": true,
                    "required": true,
                    "type": "integer",
                    "description": "",
                    "choices": null,
                    "extra_link": null
                },
                "title": {
                    "accessibility": "private",
                    "visible": true,
                    "readonly": false,
                    "required": true,
                    "type": "string",
                    "description": null,
                    "choices": null,
                    "extra_link": null
                },
                "content": {
                    "accessibility": "private",
                    "visible": true,
                    "readonly": false,
                    "required": true,
                    "type": "text",
                    "description": null,
                    "choices": null,
                    "extra_link": null
                }
            },
            "primary_key": [
                "id"
            ],
            "count": 2
        },
        "elements": [
            {
                "id": 1,
                "title": "Test post",
                "content": "This is the content"
            },
            {
                "id": 2,
                "title": "Test post",
                "content": "This is the content"
            }
        ]
    }
    
## Get an element from an entity 

`GET /api/{fqcn}\{compound_id}`\
 `Example with an entity named POST : api/App%5CEntity%5CPost/{"id":1}`

### Request

##### Curl

      curl --location -g --request GET 'http://ibexa33.localhost/api/App%5CEntity%5CPost/{"id":1}'
      
##### Php

    <?php

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://ibexa33.localhost/api/App%255CEntity%255CPost/%7B%22id%22:1%7D',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'GET',
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;

### Response

     {
      "id": 1,
      "title": "Test post",
      "content": "This is the content"
     }
     

## Get an entity or element which does not exist

### Response
    {
      "type": "https://tools.ietf.org/html/rfc2616#section-10",
      "title": "An error occurred",
      "status": 404,
      "detail": "No route found for \"GET /api/App/Entity/Posts/\" (from \"http://ibexa33.localhost/api/App%5CEntity%5CPosts\")",
      "class": "Symfony\\Component\\HttpKernel\\Exception\\NotFoundHttpException",
      "trace": [
        ...
      ]
    }
    
## Post an element

  `POST /api/{fqcn}/{compound_id}`\
  `Example with an entity named POST : api/App%5CEntity%5CPost/{"id":1}`
 
### Request

##### Curl

    curl --location -g --request POST 'http://ibexa33.localhost/api/App%5CEntity%5CPost/{"id":123}' \
    --header 'Content-Type: application/json' \
    --data-raw '{
        "title": "SQLI-SQLI",
        "content": "Levallois-Perret"
    }'
    
##### Php

    <?php

    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'http://ibexa33.localhost/api/App%255CEntity%255CPost/%7B%22id%22:123%7D',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{
        "title": "SQLI-SQLI",
        "content": "Levallois-Perret"
    }',
      CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json'
      ),
    ));

    $response = curl_exec($curl);

    curl_close($curl);
    echo $response;

### Response

    [
      {
        "message": "Resource App\\Entity\\Post CREATED"
      }
    ]
    
## Delete an element 
  
  `DELETE /api/{fqcn}/{compound_id}`\
  `Example with an entity named POST : api/App%5CEntity%5CPost/{"id":1}`
  
### Request

##### Curl

      curl --location -g --request DELETE 'http://ibexa33.localhost/api/App%5CEntity%5CPost/{"id":14}'
   
##### Php

      <?php

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://ibexa33.localhost/api/App%255CEntity%255CPost/%7B%22id%22:14%7D',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'DELETE',
      ));

      $response = curl_exec($curl);

      curl_close($curl);
      echo $response;


### Response

      {
       "message": "Resource App\\Entity\\PostDELETED"
      }

## Put/Patch an element - Update

`PUT/PATCH /api/{fqcn}/{compound_id}`\
 `Example with an entity named POST : api/App%5CEntity%5CPost/{"id":1}`

### Request

##### Curl

      curl --location -g --request PUT 'http://ibexa33.localhost/api/App%5CEntity%5CPost/{"id":1}' \
      --header 'Content-Type: application/json' \
      --data-raw '{
          "title": "SQLI-SQLI",
          "content": "Levallois-Perret"
      }'

##### Php
      
      <?php

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_URL => 'http://ibexa33.localhost/api/App%255CEntity%255CPost/%7B%22id%22:1%7D',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_POSTFIELDS =>'{
          "title": "SQLI-SQLI",
          "content": "Levallois-Perret"
      }',
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json'
        ),
      ));

      $response = curl_exec($curl);

      curl_close($curl);
      echo $response;

### Response

      {
        "id": 1,
        "title": "SQLI-SQLI",
        "content": "Levallois-Perret"
      }





    
