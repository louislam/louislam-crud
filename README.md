# Louis Lam's CRUD

Love GroceryCRUD, but don't like CodeIgniter? LouisLam's CRUD is a CRUD Library which is heavily influenced by Grocery CRUD. It provides similar features of GroceryCRUD, but it is not based on CodeIgniter.

LouisLam's CRUD Library is managed by Composer. It sticks to nowadays php standard and works well with other libraries.

## Features
* Create/Update/Delete/ListView web interface.
* RESTful API.
* Auto create tables and fields for you.
* Extensible Field Types.
* Theme
* Work without any framework. (But work better with Slim Framework by default)
* Export to Excel format (Customizable)
* Support MySQL/MariaDB, SQLite, PostgreSQL and CUBRID.

## Why choose us?
* "Write Less, Do More". 
* Easy to customize.

## Getting started with a simple example
1. Require and Import Libraries.
    ```php
    <?php
    
    require "vendor/autoload.php";
    use LouisLam\CRUD\SlimLouisCRUD;
    use RedBeanPHP\R;
    ```

1. Setup a Database Connection (Support MySQL, SQLite etc.)

 ```php
    R::setup('sqlite:dbfile.db');
    ```

1. Create a SlimLouisCRUD instance.
    ```php
    $crud = new SlimLouisCRUD();
    ```
1. Add a route for your table (product).
    ```php
    // Add a Route for "product" table
    $crud->add("product", function () use ($crud) {
    
        // Show and Ordering the fields
        $crud->showFields("id", "name", "price", "description");
        
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
    
