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

Note: This is the only file from the `core` folder that you can include throughout your files.

Initializing the app: 

```php
<?php include 'core/app.php';
$app = new Application();

Your business logic goes here...

// Include all your other stuff etc
include 'template/header.php';?>

<h1>Your pages` markup goes here</h1>

<?php include 'template/footer.php'; ?>
```

The application class is where you would place common **pure** functions:
https://en.wikipedia.org/wiki/Pure_function

```php
<?php
class Application{
    ...
    public function boldMyTextPlease($text){
        return "<b>".$text."</b>";
    }
}
```

##What this is:
This is a basis for a very simple php project. It is designed and written with 2 concepts in mind: simple-to-use, powerful enough to help most common web development tasks in one way or another. It is built with the intention to be easy for newbie php developers to use, while providing solutions to common web development problems.

#What this is NOT:
This is not intended to be a full fledged framework and while yes, it can be used as a basis for big web apps, that is far from its intention.

##TODO:
The project has a few TODO items:
