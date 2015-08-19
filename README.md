# Louis Lam's CRUD

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
1. Add CRUD for Product Table
    ```php
    $crud->add("product", function () use ($crud) {
        $crud->showFields("id", "name", "price", "description");
    });
    ```
    
5. Run the application. 
    ```php
    $crud->run();
    ```
    
6. Open it in your browser.
    ![Alt screenshot](http://i.imgur.com/L7ZsPEX.png)
    
