# Symfony Validation Enhancements Bundle

The **Symfony Validation Enhancements Bundle** is a Symfony bundle designed to extend and enhance the default validation
capabilities provided by the Symfony framework. It introduces additional constraints and validation features to
facilitate more robust data validation within Symfony applications.

## Key Features

- **Additional Validators**: Custom validators that complement Symfony's native validation constraints for more specific
  and tailored validation rules.
- **Enhanced Validation Logic**: Advanced validation mechanisms to implement complex scenarios with ease.

## Installation

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

### Applications that use Symfony Flex

Open a command console, enter your project directory and execute:

```console
$ composer require michaelcozzolino\SymfonyValidationEnhancementsBundle
```

### Applications that don't use Symfony Flex

#### Enable the Bundle

Then, enable the bundle by adding it to the list of registered bundles
in the `config/bundles.php` file of your project:

```php
// config/bundles.php

return [
    // ...
    MichaelCozzolino\SymfonyValidationEnhancementsBundle\SymfonyValidationEnhancementsBundle::class => ['all' => true],
];
```

## Docs

The bundle automatically register the following event listeners:

## `RequestPayloadTrimmerListener`

The `RequestPayloadTrimmerListener` is an event listener designed to trim whitespace from the request payload during the
Symfony kernel request event. This ensures clean and sanitized data is passed through the application.

When used together with the `NonEmptyString` constraint, it will cause validation to fail for requests containing only
whitespace or empty strings, such as:
```json
{
    "name": "     ",
    "surname": ""
}
```

### Usage

`max` is used to specify the maximum length of the string, if `null` or omitted, the max length is `+inf`.

```php
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint\NonEmptyString;

class MyRequest {
    public function __construct(
        #[NonEmptyString(max: 1000)]
        public readonly string $name,
        
        #[NonEmptyString(max: null)]
        public readonly string $surname
    ) {
    }
}
```

if working with MySql, more specific constraints can be used:

```php
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint\NonEmptyMySqlText;
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint\NonEmptyMySqlVarcharDefault;

class PersonRequest {
    public function __construct(
        #[NonEmptyMySqlText]
        public readonly string $name,
        
        #[NonEmptyMySqlVarcharDefault]
        public readonly string $surname
    ) {
    }
}
```

## `ValidationErrorListener`

The `ValidationErrorListener` automatically standardizes the obtained response after one or more validation failure.
Let's suppose your request object is the following one:

```php
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint\NonEmptyString;
use Symfony\Component\Validator\Constraints as Assert;

class AddressRequest {
    public function __construct(
        #[NonEmptyString]
        public readonly string $street,
        
        #[Assert\Positive]
        public readonly int $number
    ) {
    }
}

class UserRequest {
    public function __construct(
        #[Assert\Positive]
        public readonly int $userId,
        
        #[Assert\All([
            new Assert\Positive
        ])]
        public readonly array $productIds,
        
        #[Assert\Valid]
        public readonly AddressRequest $address
    ) {
    
    }
}
```

and an invalid payload could be:

```json
{
    "userId": -19,
    "productIds": [
        -1,
        2,
        0
    ],
    "address": {
        "street": "",
        "number": -1
    }
}
```

the listener above will return a json response structured as:

```json
{
    "userId": [
        "validation error for -19"
    ],
    "productIds": {
        "0": [
            "validation error for -1"
        ],
        "2": [
            "validation error for 0"
        ]
    },
    "address": {
        "street": [
            "validation error for street"
        ],
        "number": [
            "validation error for number"
        ]
    }
}
```

So, the final json response is the same object from the request whose values are
an array (in case of multiple constraint) of errors.

## Constraints

In addition to the constraints mentioned before, we also have:

## `EntityExists`

It checks that an entity exists, meaning that there exists one row in the database using the specified identifier.

### Usage

```php
use MichaelCozzolino\SymfonyValidationEnhancementsBundle\Validator\Constraint\EntityExists;class EntityRequest {
    public function __construct(
        #[EntityExists(entityClass: MyEntity::Class, validateExistence:true, entityProperty: 'id', entityName: 'my entity')]
        public readonly int $entityId
    ) {
    }
}
```
### Parameters
`entityClass`: The class-string of the entity.

`validateExistence`: If set to true the validation will fail if the entity already exists, if set to false the validation
will fail if the entity does not exist. The last one is useful for example when you want to store some data whose id
is for example a non auto generated one but decided by the code. **Default**: `true`

`entityProperty`: The name of the column used as primary key to retrieve the entity. **Default**: `'id'`

`entityName`: The name of the entity that will be used in the validation message. **Default**: `null`. 
In case the default value is used the validator will try to guess the short name of the entity class.

### Message
**type**: `string` **default**: ```The requested `{entityName}` does not exist.```
