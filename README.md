# Louis Lam's CRUD

    <?php
    require "vendor/autoload.php";
    
	use LouisLam\CRUD\SlimLouisCRUD;
	use RedBeanPHP\R;

	R::setup('sqlite:dbfile.db');
	
	// CRUD for Product Table
	$crud->add("product", function () use ($crud) {
	    $crud->showFields("id", "name", "price", "description");
	    $crud->field("price")->required();
	});

	$crud->run();