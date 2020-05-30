<?php

    //
    header('Content-Type: application/json');

    //
    use Marketplace\Connection as Connection;
    use Marketplace\Token as Token;
    use Marketplace\Product as Product;

    // connect to the PostgreSQL database
    $pdo = Connection::get()->connect();

    // STEP 1. Receive passed variables / information
    if(isset($_REQUEST['app'])){$request['app'] = clean($_REQUEST['app']);}
    if(isset($_REQUEST['domain'])){$request['domain'] = clean($_REQUEST['domain']);}
    if(isset($_REQUEST['token'])){$request['token'] = clean($_REQUEST['token']);}

    // INITIATE DATA CLEANSE
    if(isset($_REQUEST['id'])){$request['id'] = clean($_REQUEST['id']);}		
    if(isset($_REQUEST['attributes'])){$request['attributes'] = clean($_REQUEST['attributes']);}		
    if(isset($_REQUEST['online'])){$request['online'] = clean($_REQUEST['online']);}		
    if(isset($_REQUEST['public'])){$request['public'] = clean($_REQUEST['public']);}		
    if(isset($_REQUEST['name'])){$request['name'] = clean($_REQUEST['name']);}		
    if(isset($_REQUEST['description'])){$request['description'] = clean($_REQUEST['description']);}		
    if(isset($_REQUEST['slug'])){$request['slug'] = clean($_REQUEST['slug']);}		
    if(isset($_REQUEST['images'])){$request['images'] = clean($_REQUEST['images']);}		
    if(isset($_REQUEST['category'])){$request['category'] = clean($_REQUEST['category']);}		
    if(isset($_REQUEST['partner'])){$request['partner'] = clean($_REQUEST['partner']);}		

    //
    switch ($_SERVER['REQUEST_METHOD']) {

        //
        case 'POST':

            try {

                // 
                $product = new Product($pdo);
            
                // insert a stock into the stocks table
                $id = $product->insertProduct($request);

                $request['id'] = $id;

                $results = $product->selectProduct($request);

                $results = json_encode($results);
                
                //
                echo $results;
            
            } catch (\PDOException $e) {

                echo $e->getMessage();

            }

        break;

        //
        case 'GET':

            //
            if(isset($_REQUEST['per'])){$request['per'] = clean($_REQUEST['per']);}
            if(isset($_REQUEST['page'])){$request['page'] = clean($_REQUEST['page']);}
            if(isset($_REQUEST['limit'])){$request['limit'] = clean($_REQUEST['limit']);}        

            try {

                // 
                $product = new Product($pdo);

                // get all stocks data
                $results = $product->selectProduct($request);

                $results = json_encode($results);

                echo $results;

            } catch (\PDOException $e) {

                echo $e->getMessage();

            }

        break;

        //
        case 'PUT':

            try {

                // 
                $product = new Product($pdo);
            
                // insert a stock into the stocks table
                $id = $product->updateProduct($request);

                $request['id'] = $id;

                $results = $product->selectProduct($request);

                $results = json_encode($results);

                echo $results;
            
            } catch (\PDOException $e) {

                echo $e->getMessage();

            }

        break;

        //
        case 'DELETE':

            try {

                // 
                $product = new Product($pdo);
            
                // insert a stock into the stocks table
                $id = $product->deleteProduct($request);

                echo 'The record ' . $id . ' has been deleted';
            
            } catch (\PDOException $e) {

                echo $e->getMessage();

            }

        break;

    }

?>
