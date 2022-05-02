# Louis Lam's CRUD

## Features
* "Write Less, Do More". 
* Create/Update/Delete/ListView web interface.
* RESTful API.
* Auto create tables and fields for you.
* Extensible Field Types.
* Theme
* Work without any framework. (But work better with Slim Framework by default)
* Export to Excel format (Customizable)
* Support MySQL/MariaDB, SQLite, PostgreSQL and CUBRID.


## Docmentation

https://github.com/louislam/louislam-crud/wiki

## Installation

Note: The library is under development and not tested very well currently. 

### Method 1: add louislam-crud to your composer.json

1. Require the library.
    ```json
    "require": {
        "louislam/louislam-crud": "3.x-dev"
    }
    ```
    
1. Compose the project with PHP Composer.

### Method 2: Start a new project with bootstrap project.

1. Download bootstrap project from: https://github.com/louislam/louislam-crud-bootstrap
1. Compose the project with PHP Composer.

### Method 3: Direct Download without Composer (Coming Soon)

## Getting started with a simple example
1. Require and Import Libraries.
    ```php
    <?php
    
    require "vendor/autoload.php";
    use LouisLam\CRUD\SlimLouisCRUD;
    use RedBeanPHP\R;
    ```

1. Setup a Database Connection (Support MySQL, SQLite etc.)

    For SQLite:

    ```php
    R::setup('sqlite:dbfile.db');
    ```
    
    For MySQL:

    ```php
    R::setup( 'mysql:host=localhost;dbname=mydatabase', 'user', 'password' );
    ```
    
    More info: http://www.redbeanphp.com/index.php?p=/connection


1. Create a SlimLouisCRUD instance.
    ```php
    $crud = new SlimLouisCRUD();
    ```
1. Add a route for your table (product).
    ```php
    // Add a Route for "product" table
    $crud->add("product", function () use ($crud) {
    
        // Show and Ordering the fields
        $crud->showFields([
            "id", 
            "name", 
            "price", 
            "description"
        ]);
        
    });
    ```
    
5. Run the application. 
    ```php
    $crud->run();
    ```
    
6. Open it in your browser.
   ```
   http://<your hostname>/index.php/crud/product
   ```
    ![Alt screenshot](http://i.imgur.com/c3rl7zr.png)
    
