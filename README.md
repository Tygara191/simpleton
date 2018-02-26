## Getting started
1. Download entire repo and put in `htdocs` or wherever your server root directory is;
2. Open up `core/app.php`, which can be considered your "main" file, and modify `BASE_PATH` right at the top. 
3. Import `users_table.sql` or create your own user table
4. If you have created your own users table, modify `core/databasemanager.class.php` respectively.
5. Open up `core/config/main.php` and modify values as required, starting with `dbopts`.
6. You're ready to go!

## What to do now?
The code is designed with the assumption that you're going to be modifying and using the files already present, so that is exactly what you should do.

Read all 8ish files so you could get familiar with the codebase and what everything does. Simpleton is written with the idea to be as small as possible so every developer can get to know and start using the codebase in 10-20 minutes.

## What are the core components and what are they used for?

### Application class ###
The application class `core/app.php` is ment to be included and **instantiated** at every page as follows:

Note: This is the only file from the `core` folder that you should include throughout your files.

Initializing the app: 

`index.php`
```php
<?php
// Always include the application first!!
include 'core/app.php';
$app = new Application();

// Your business logic goes here...
// Here you have access to and should use the 5 core toolkits of Simpleton:
$app->config;
$app->auth;
$app->db;
$app->lang;
$app->validation;

// Include all your other stuff etc
include 'template/header.php';?>

<h1>Your pages` markup goes here</h1>

<?php include 'template/footer.php'; ?>
```

The application class is where you would place common **pure** functions:
https://en.wikipedia.org/wiki/Pure_function

`core/app.php`
```php
<?php
class Application{
    ...
    public function boldMyTextPlease($text){
        return "<b>".$text."</b>";
    }
}
```

### DatabaseManager ###

The DatabaseManager class `core/databasemanager.class.php` is where you put all your database operations.
By default it comes with a few predefined methods, mostly used by the auth system. These methods are intended to be used as a model or guideline when making your own methods.

* `int foundRows()` - Useful when we have a query with LIMIT, this function returns the number of rows while ignoring the limit clause. Especially useful when building pagination.
* `bool update( int $id, array $object)` - a **NON-WORKING** sample of how an update method will look, had you faced the need to make one. This method is left in so you can copy-paste and modify it a bit to get a functioning update method.
* `int insertUser( array $user)` - Inserts a user and returns his ID.
* `array | false getUserById( int $id)` - gets a user by their ID or returns false if no user was found.
* `array | false getUserByUsername( string $username)` - same as getUserById(), but based on username
* `bool updateUserPassword( int $user_id, string $new_password)` - update a user's password

### Config ###
The Config class is a wrapper around the arrays in the config files. You use it **anywhere** as follows:

```php
$app->config->item('some_config_key');
```

And as long as that config item is present in **any** of the configuration files, defined when instantiating the config class in `app.php`, you will get your value.

`app.php`
```php
<?php
class Application{
    const CONFIG_FILES_LOCATION = BASE_PATH."/core/config/";
    ...
    public function __construct($unauthenticated_only=false){
        ...
		$this->config = new Config([
            Application::CONFIG_FILES_LOCATION.'main.conf.php',
        ]);
        ...
    }
}
```

`core/config/main.conf.php`
```php
<?php
...
$config['some_config_key'] = '123';
```

### Language ###

When using the language class, the first thing to do is define all the supported languages  as follows:

`core/config/main.conf.php`
```php
<?php
...

// First in line is considered the default language
$config['supported_languages'] = array(
    'en' => ['filename' => 'en.php', 'label' => 'English'],
    'bg' => ['filename' => 'bg.php', 'label' => 'Български'],
);

...
```

Then create respective files for each language.

`core/lang/bg.php`

```php
<?php

$lang['site_title'] = "Туй миа на български";
...
```

`core/lang/en.php`
```php
<?php

$lang['site_title'] = "This is in english";
...
```

Then we use our translated strings as follows:

```php
echo $app->lang->item('site_title');
```

Note: If no value for a key is found in the current language file, the system will look in the default language file. If no value is found there too, it will die() with an adequate error message.

How the class knows which language to show:

First it looks for a `lang` cookie. The cookie should hold a key. Example of keys in our case are `en` and `bg`, which can be seen in our `main.conf.php` file.

** TODO** If no valid cookie is found it tries to make out the locale through headers. If that also fails, we use the default language. /The default language is the first language in the config file/

### Encryption ###
Rather self explanatory.
```php
<?php
$input = "zdrkp";
$encrypted = $app->encryption->encode($input);
$decrypted = $app->encryption->decode($encrypted);
echo $decrypted;
//zdrkp
```

### Validation ###

Available at
```php
$app->validation
```
Uses GUMP. Read docs at: https://github.com/Wixel/GUMP

## What Simpleton is: ##
This is a basis for a very simple php project. It is designed and written with 2 concepts in mind: simple-to-use, powerful enough to help most common web development tasks in one way or another. It is built with the intention to be easy for newbie php developers to use, while providing solutions to common web development problems.

## What this is NOT: ##
This is not intended to be a full fledged framework and while yes, it can be used as a basis for big web apps, that is far from its intention.

## TODO: ##
The project has a few TODO items:
* Add registration functionality to the authentication class.
* Make the language class use accepted language headers.
* Handle file uploads.