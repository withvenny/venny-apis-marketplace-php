<?php

    //
    namespace Marketplace;

    //
    class Connection {
    
        /**
         * Connection
         * @var type 
         */
        private static $conn;
    
        /**
         * Connect to the database and return an instance of \PDO object
         * @return \PDO
         * @throws \Exception
         */
        public function connect() {

            // read parameters in the ini configuration file
            //$params = parse_ini_file('database.ini');
            $db = parse_url(getenv("DATABASE_URL"));

            //if ($params === false) {throw new \Exception("Error reading database configuration file");}
            if ($db === false) {throw new \Exception("Error reading database configuration file");}
            // connect to the postgresql database
            $conStr = sprintf("pgsql:host=%s;port=%d;dbname=%s;user=%s;password=%s", 
                    $db['host'],
                    $db['port'], 
                    ltrim($db["path"], "/"), 
                    $db['user'], 
                    $db['pass']);
    
            $pdo = new \PDO($conStr);
            $pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    
            return $pdo;
        }
    
        /**
         * return an instance of the Connection object
         * @return type
         */
        public static function get() {
            if (null === static::$conn) {
                static::$conn = new static();
            }
    
            return static::$conn;
        }
    
        protected function __construct() {
            
        }
    
        private function __clone() {
            
        }
    
        private function __wakeup() {
            
        }
    
    }

    //
    class Token {

        /**
         * PDO object
         * @var \PDO
         */
        private $pdo;
    
        /**
         * init the object with a \PDO object
         * @param type $pdo
         */
        public function __construct($pdo) {
            $this->pdo = $pdo;
        }

        /**
         * Return all rows in the stocks table
         * @return array
         */
        public function all() {
            $stmt = $this->pdo->query('SELECT id, symbol, company '
                    . 'FROM stocks '
                    . 'ORDER BY symbol');
            $stocks = [];
            while ($row = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $stocks[] = [
                    'id' => $row['id'],
                    'symbol' => $row['symbol'],
                    'company' => $row['company']
                ];
            }
            return $stocks;
        }

        //
        public function validatedToken() {
            
            //
            return true;
            
            //exit;

        }

        //
        public function process_id($object='obj') {

            //
            $id = substr(md5(uniqid(microtime(true),true)),0,13);

            $id = $object.'_'.$id;

            //
            return $id;
            
            //exit;

        }
        
        //
        public function event_id($object='obj') {

            //
            $id = substr(md5(uniqid(microtime(true),true)),0,13);

            $id = $object.'_'.$id;
    
            //
            return $id;
            
            //exit;

        }

        //
        public function new_id($object='obj') {

            //
            $id = substr(md5(uniqid(microtime(true),true)),0,13);
            $id = $object . "_" . $id;
    
            //
            return $id;
            
            //exit;

        }

        /**
         * Find stock by id
         * @param int $id
         * @return a stock object
         */
        public function check($id) {

            //
            $sql = "SELECT message_id FROM messages WHERE id = :id AND active = 1";

            // prepare SELECT statement
            $statement = $this->pdo->prepare($sql);
            // bind value to the :id parameter
            $statement->bindValue(':id', $id);
            
            // execute the statement
            $stmt->execute();
    
            // return the result set as an object
            return $stmt->fetchObject();
        }

        /**
         * Delete a row in the stocks table specified by id
         * @param int $id
         * @return the number row deleted
         */
        public function delete($id) {
            $sql = 'DELETE FROM stocks WHERE id = :id';
    
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id);
    
            $stmt->execute();
    
            return $stmt->rowCount();
        }

        /**
         * Delete all rows in the stocks table
         * @return int the number of rows deleted
         */
        public function deleteAll() {
    
            $stmt = $this->pdo->prepare('DELETE FROM stocks');
            $stmt->execute();
            return $stmt->rowCount();
        }

    }

    //
    class Catalog {

        //
        private $pdo;
    
        //
        public function __construct($pdo) {

            //
            $this->pdo = $pdo;

            //
            $this->token = new \Marketplace\Token($this->pdo);

        }

        //
        public function insertCatalog($request) {

            //generate ID
            if(!isset($request['id'])){$request['id'] = $this->token->new_id('ctg');}

            $columns = "";

            // INSERT OBJECT - COLUMNS
            if(isset($request['id'])){$columns.="catalog_id,";}		
            if(isset($request['attributes'])){$columns.="catalog_attributes,";}		
            if(isset($request['online'])){$columns.="catalog_online,";}		
            if(isset($request['public'])){$columns.="catalog_public,";}		
            if(isset($request['name'])){$columns.="catalog_name,";}		
            if(isset($request['description'])){$columns.="catalog_description,";}		
            if(isset($request['slug'])){$columns.="catalog_slug,";}		
            if(isset($request['images'])){$columns.="catalog_images,";}		
            if(isset($request['partner'])){$columns.="partner_id,";}		
            
            $columns.= "app_id,";
            $columns.= "event_id,";
            $columns.= "process_id";

            $values = "";

            // INSERT OBJECT - VALUES
            if(isset($request['id'])){$values.=":catalog_id,";}		
            if(isset($request['attributes'])){$values.=":catalog_attributes,";}		
            if(isset($request['online'])){$values.=":catalog_online,";}		
            if(isset($request['public'])){$values.=":catalog_public,";}		
            if(isset($request['name'])){$values.=":catalog_name,";}		
            if(isset($request['description'])){$values.=":catalog_description,";}		
            if(isset($request['slug'])){$values.=":catalog_slug,";}		
            if(isset($request['images'])){$values.=":catalog_images,";}		
            if(isset($request['partner'])){$values.=":partner,";}		
            
            $values.= ":app_id,";
            $values.= ":event_id,";
            $values.= ":process_id";

            // prepare statement for insert
            $sql = "INSERT INTO {$request['domain']} (";
            $sql.= $columns;
            $sql.= ") VALUES (";
            $sql.= $values;
            $sql.= ")";
            $sql.= " RETURNING " . prefixed($request['domain']) . "_id";
    
            //
            $statement = $this->pdo->prepare($sql);
            
            // INSERT OBJECT - BIND VALUES
            if(isset($request['id'])){$statement->bindValue('catalog_id',$request['id']);}		
            if(isset($request['attributes'])){$statement->bindValue('catalog_attributes',$request['attributes']);}		
            if(isset($request['online'])){$statement->bindValue('catalog_online',$request['online']);}		
            if(isset($request['public'])){$statement->bindValue('catalog_public',$request['public']);}		
            if(isset($request['name'])){$statement->bindValue('catalog_name',$request['name']);}		
            if(isset($request['description'])){$statement->bindValue('catalog_description',$request['description']);}		
            if(isset($request['slug'])){$statement->bindValue('catalog_slug',$request['slug']);}		
            if(isset($request['images'])){$statement->bindValue('catalog_images',$request['images']);}		
            if(isset($request['partner'])){$statement->bindValue('partner',$request['partner']);}		
            
            $statement->bindValue(':app_id', $request['app']);
            $statement->bindValue(':event_id', $this->token->event_id());
            $statement->bindValue(':process_id', $this->token->process_id());
            
            // execute the insert statement
            $statement->execute();

            $data = $statement->fetchAll();
            
            $data = $data[0]['catalog_id'];

            return $data;
        
        }

        //
        public function selectCatalogs($request) {

            //echo json_encode($request); exit;

            //$token = new \Core\Token($this->pdo);
            $token = $this->token->validatedToken($request['token']);

            // Retrieve data ONLY if token  
            if($token) {
                
                // domain, app always present
                if(!isset($request['per'])){$request['per']=20;}
                if(!isset($request['page'])){$request['page']=1;}
                if(!isset($request['limit'])){$request['limit']=100;}

                //
                $conditions = "";
                $domain = $request['domain'];
                $prefix = prefixed($domain);

                // SELECT OBJECT - COLUMNS
                $columns = "

                catalog_ID,		
                catalog_attributes,		
                catalog_online,		
                catalog_public,		
                catalog_name,		
                catalog_description,		
                catalog_slug,		
                catalog_images,		
                partner_ID,		
                app_ID,		
                time_updated,		
                time_finished	

                ";

                $table = $domain;

                //
                $start = 0;

                //
                if(isset($request['page'])) {

                    //
                    $start = ($request['page'] - 1) * $request['per'];
                
                }

                //
                if(!empty($request['id'])) {

                    $conditions.= ' WHERE ';
                    $conditions.= ' ' . $prefix . '_id = :id ';
                    $conditions.= ' AND active = 1 ';
                    $conditions.= ' ORDER BY time_finished DESC ';

                    $subset = " LIMIT 1";

                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= " FROM " . $table;
                    $sql.= $conditions;
                    $sql.= $subset;
                    
                    //echo json_encode($request['id']);
                    //echo '<br/>';
                    //echo $sql; exit;

                    //
                    $statement = $this->pdo->prepare($sql);

                    // bind value to the :id parameter
                    $statement->bindValue(':id', $request['id']);

                    //echo $sql; exit;

                } else {

                    $conditions = "";
                    $refinements = "";

                    // SELECT OBJECT - WHERE CLAUSES
                    // SKIP ID		
                    //if(isset($request['attributes'])){$refinements.="catalog_attributes"." ILIKE "."'%".$request['attributes']."%' AND ";}		
                    if(isset($request['online'])){$refinements.="catalog_online"." = "."'".$request['online']."' AND ";}		
                    if(isset($request['public'])){$refinements.="catalog_public"." = "."'".$request['public']."' AND ";}		
                    if(isset($request['name'])){$refinements.="catalog_name"." ILIKE "."'%".$request['name']."%' AND ";}		
                    if(isset($request['description'])){$refinements.="catalog_description"." ILIKE "."'%".$request['description']."%' AND ";}		
                    if(isset($request['slug'])){$refinements.="catalog_slug"." ILIKE "."'%".$request['slug']."%' AND ";}		
                    //if(isset($request['images'])){$refinements.="catalog_images"." ILIKE "."'%".$request['images']."%' AND ";}		
                    if(isset($request['partner'])){$refinements.="partner_id"." = "."'".$request['partner']."' AND ";}		

                    //echo $conditions . 'conditions1<br/>';
                    //echo $refinements . 'refinements1<br/>';
                    
                    $conditions.= " WHERE ";
                    $conditions.= $refinements;
                    $conditions.= " active = 1 ";
                    $conditions.= ' AND app_id = \'' . $request['app'] . '\' ';
                    $conditions.= ' AND partner_id = \'' . $request['partner'] . '\' ';
                    $conditions.= " ORDER BY time_finished DESC ";
                    $subset = " OFFSET {$start}" . " LIMIT {$request['per']}";
                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= "FROM " . $table;
                    $sql.= $conditions;
                    $sql.= $subset;

                    //echo $conditions . 'conditions2<br/>';
                    //echo $refinements . 'refinements2<br/>';

                    //echo $sql; exit;
                    
                    //
                    $statement = $this->pdo->prepare($sql);

                }
                    
                // execute the statement
                $statement->execute();

                //
                $results = [];
                $total = $statement->rowCount();
                $pages = ceil($total/$request['per']); //
                //$current = 1; // current page
                //$limit = $result['limit'];
                //$max = $result['max'];

                //
                if($statement->rowCount() > 0) {

                    //
                    $data = array();
                
                    //
                    while($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
        
                        //
                        $data[] = [

                            'id' => $row['catalog_id'],		
                            'attributes' => json_decode($row['catalog_attributes']),		
                            'online' => $row['catalog_online'],		
                            'public' => $row['catalog_public'],		
                            'name' => $row['catalog_name'],		
                            'description' => $row['catalog_description'],		
                            'slug' => $row['catalog_slug'],		
                            'images' => json_decode($row['catalog_images']),		
                            'partner' => $row['partner_id'],		
                            'app' => $row['app_id'],		
                            'updated' => $row['time_updated'],		
                            'when' => $row['time_finished'],		
                            
                        ];

                    }

                    $code = 200;
                    $message = "OK";

                } else {

                    //
                    $data = NULL;
                    $code = 204;
                    $message = "No Content";

                }

            } else {

                //
                $data[] = NULL;
                $code = 401;
                $message = "Forbidden - Valid token required";

            }

            $results = array(

                'status' => $code,
                'message' => $message,
                'metadata' => [
                    'page' => $request['page'],
                    'pages' => $pages,
                    'total' => $total
                ],
                'data' => $data,
                'log' => [
                    'process' => $process_id = $this->token->process_id(),
                    'event' => $event_id = $this->token->event_id($process_id)
                ]

            );

            //
            return $results;

        }

        //
        public function updateCatalog($request) {

            //
            $domain = $request['domain'];
            $table = prefixed($domain);
            $id = $request['id'];

            //
            $set = "";

            // UPDATE OBJECT - SET
            // SKIP as ID won't be getting UPDATED		
            if(isset($request['attributes'])){$set.= " catalog_attributes = :catalog_attributes ";}		
            if(isset($request['online'])){$set.= " catalog_online = :catalog_online ";}		
            if(isset($request['public'])){$set.= " catalog_public = :catalog_public ";}		
            if(isset($request['name'])){$set.= " catalog_name = :catalog_name ";}		
            if(isset($request['description'])){$set.= " catalog_description = :catalog_description ";}		
            if(isset($request['slug'])){$set.= " catalog_slug = :catalog_slug ";}		
            if(isset($request['images'])){$set.= " catalog_images = :catalog_images ";}		

            //
            $set = str_replace('  ',',',$set);

            // GET table name
            $condition = $table."_id = :id";
            $condition.= " RETURNING " . $table . "_id";

            // sql statement to update a row in the stock table
            $sql = "UPDATE {$domain} SET ";
            $sql.= $set;
            $sql.= " WHERE ";
            $sql.= $condition;

            //echo $sql; exit;

            $statement = $this->pdo->prepare($sql);
    
            // UPDATE OBJECT - BIND VALUES
            //if(isset($request['id'])){$statement->bindValue(':catalog_id', $request['id']);}		
            if(isset($request['attributes'])){$statement->bindValue(':catalog_attributes', $request['attributes']);}		
            if(isset($request['online'])){$statement->bindValue(':catalog_online', $request['online']);}		
            if(isset($request['public'])){$statement->bindValue(':catalog_public', $request['public']);}		
            if(isset($request['name'])){$statement->bindValue(':catalog_name', $request['name']);}		
            if(isset($request['description'])){$statement->bindValue(':catalog_description', $request['description']);}		
            if(isset($request['slug'])){$statement->bindValue(':catalog_slug', $request['slug']);}		
            if(isset($request['images'])){$statement->bindValue(':catalog_images', $request['images']);}		

            $statement->bindValue(':id', $id);

            // update data in the database
            $statement->execute();

            $data = $statement->fetchAll();
            
            $data = $data[0]['catalog_id'];

            // return generated id
            return $data;

        }

        //
        public function deleteCatalog($request) {

            $id = $request['id'];
            $domain = $request['domain'];
            $column = prefixed($domain) . '_id';
            $sql = 'DELETE FROM ' . $domain . ' WHERE '.$column.' = :id';
            //echo $id; //exit
            //echo $column; //exit;
            //echo $domain; //exit;
            //echo $sql; //exit

            $statement = $this->pdo->prepare($sql);
            //$statement->bindParam(':column', $column);
            $statement->bindValue(':id', $id);
            $statement->execute();
            return $statement->rowCount();

        }

    }

    //
    class Category {

        //
        private $pdo;
    
        //
        public function __construct($pdo) {

            //
            $this->pdo = $pdo;

            //
            $this->token = new \Marketplace\Token($this->pdo);

        }

        //
        public function insertCategory($request) {

            //echo json_encode($request);
            //exit;

            //generate ID
            if(!isset($request['id'])){$request['id'] = $this->token->new_id('cat');}

            $columns = "";

            // INSERT OBJECT - COLUMNS
            if(isset($request['id'])){$columns.="category_id,";}		
            if(isset($request['attributes'])){$columns.="category_attributes,";}		
            if(isset($request['online'])){$columns.="category_online,";}		
            if(isset($request['public'])){$columns.="category_public,";}		
            if(isset($request['name'])){$columns.="category_name,";}		
            if(isset($request['description'])){$columns.="category_description,";}		
            if(isset($request['slug'])){$columns.="category_slug,";}		
            if(isset($request['images'])){$columns.="category_images,";}		
            if(isset($request['catalog'])){$columns.="catalog_id,";}		
            if(isset($request['partner'])){$columns.="partner_id,";}		
            
            $columns.= "app_id,";
            $columns.= "event_id,";
            $columns.= "process_id";

            $values = "";

            // INSERT OBJECT - VALUES
            if(isset($request['id'])){$values.=":category_id,";}		
            if(isset($request['attributes'])){$values.=":category_attributes,";}		
            if(isset($request['online'])){$values.=":category_online,";}		
            if(isset($request['public'])){$values.=":category_public,";}		
            if(isset($request['name'])){$values.=":category_name,";}		
            if(isset($request['description'])){$values.=":category_description,";}		
            if(isset($request['slug'])){$values.=":category_slug,";}		
            if(isset($request['images'])){$values.=":category_images,";}		
            if(isset($request['catalog'])){$values.=":catalog_id,";}		
            if(isset($request['partner'])){$values.=":partner_id,";}		
            
            $values.= ":app_id,";
            $values.= ":event_id,";
            $values.= ":process_id";

            // prepare statement for insert
            $sql = "INSERT INTO {$request['domain']} (";
            $sql.= $columns;
            $sql.= ") VALUES (";
            $sql.= $values;
            $sql.= ")";
            $sql.= " RETURNING " . $request['domain'] . "_id";

            //echo json_encode($request);
            //exit;
    
            //
            $statement = $this->pdo->prepare($sql);
            
            // INSERT OBJECT - BIND VALUES
            if(isset($request['id'])){$statement->bindValue('category_id',$request['id']);}		
            if(isset($request['attributes'])){$statement->bindValue('category_attributes',$request['attributes']);}		
            if(isset($request['online'])){$statement->bindValue('category_online',$request['online']);}		
            if(isset($request['public'])){$statement->bindValue('category_public',$request['public']);}		
            if(isset($request['name'])){$statement->bindValue('category_name',$request['name']);}		
            if(isset($request['description'])){$statement->bindValue('category_description',$request['description']);}		
            if(isset($request['slug'])){$statement->bindValue('category_slug',$request['slug']);}		
            if(isset($request['images'])){$statement->bindValue('category_images',$request['images']);}		
            if(isset($request['catalog'])){$statement->bindValue('catalog_id',$request['catalog']);}		
            if(isset($request['partner'])){$statement->bindValue('partner_id',$request['partner']);}		
            
            $statement->bindValue(':app_id', $request['app']);
            $statement->bindValue(':event_id', $this->token->event_id());
            $statement->bindValue(':process_id', $this->token->process_id());
            
            // execute the insert statement
            $statement->execute();

            $data = $statement->fetchAll();
            
            $data = $data[0]['category_id'];

            return $data;
        
        }

        //
        public function selectCategory($request) {

            //echo json_encode($request); exit;

            //$token = new \Core\Token($this->pdo);
            $token = $this->token->validatedToken($request['token']);

            // Retrieve data ONLY if token  
            if($token) {
                
                // domain, app always present
                if(!isset($request['per'])){$request['per']=20;}
                if(!isset($request['page'])){$request['page']=1;}
                if(!isset($request['limit'])){$request['limit']=100;}

                //
                $conditions = "";
                $domain = $request['domain'];
                $prefix = prefixed($domain);

                // SELECT OBJECT - COLUMNS
                $columns = "

                category_ID,		
                category_attributes,		
                category_online,		
                category_public,		
                category_name,		
                category_description,		
                category_slug,		
                category_images,		
                catalog_ID,
                parent,
                app_ID,		
                time_updated,		
                time_finished	

                ";

                $table = $domain;

                //
                $start = 0;

                //
                if(isset($request['page'])) {

                    //
                    $start = ($request['page'] - 1) * $request['per'];
                
                }

                //
                if(!empty($request['id'])) {

                    $conditions.= ' WHERE ';
                    $conditions.= ' ' . $domain . '_id = :id ';
                    $conditions.= ' AND active = 1 ';
                    $conditions.= ' ORDER BY time_finished DESC ';

                    $subset = " LIMIT 1";

                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= " FROM " . $table;
                    $sql.= $conditions;
                    $sql.= $subset;
                    
                    //echo json_encode($request['id']);
                    //echo '<br/>';
                    //echo $sql; exit;

                    //
                    $statement = $this->pdo->prepare($sql);

                    // bind value to the :id parameter
                    $statement->bindValue(':id', $request['id']);

                    //echo $sql; exit;

                } else {

                    $conditions = "";
                    $refinements = "";

                    // SELECT OBJECT - WHERE CLAUSES
                    // SKIP ID		
                    //if(isset($request['attributes'])){$refinements.="category_attributes"." ILIKE "."'%".$request['attributes']."%' AND ";}		
                    if(isset($request['online'])){$refinements.="category_online"." = "."'".$request['online']."' AND ";}		
                    if(isset($request['public'])){$refinements.="category_public"." = "."'".$request['public']."' AND ";}		
                    if(isset($request['parent'])){$refinements.="category_parent"." = "."'".$request['parent']."' AND ";}		
                    if(isset($request['name'])){$refinements.="category_name"." ILIKE "."'%".$request['name']."%' AND ";}		
                    if(isset($request['description'])){$refinements.="category_description"." ILIKE "."'%".$request['description']."%' AND ";}		
                    if(isset($request['slug'])){$refinements.="category_slug"." ILIKE "."'%".$request['slug']."%' AND ";}		
                    //if(isset($request['images'])){$refinements.="category_images"." ILIKE "."'%".$request['images']."%' AND ";}		
                    if(isset($request['catalog'])){$refinements.="catalog_id"." = "."'".$request['catalog']."' AND ";}		

                    //echo $conditions . 'conditions1<br/>';
                    //echo $refinements . 'refinements1<br/>';
                    
                    $conditions.= " WHERE ";
                    $conditions.= $refinements;
                    $conditions.= " active = 1 ";
                    $conditions.= ' AND app_id = \'' . $request['app'] . '\' ';
                    $conditions.= ' AND partner_id = \'' . $request['partner'] . '\' ';
                    $conditions.= " ORDER BY time_finished DESC ";
                    $subset = " OFFSET {$start}" . " LIMIT {$request['per']}";
                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= "FROM " . $table;
                    $sql.= $conditions;
                    $sql.= $subset;

                    //echo $conditions . 'conditions2<br/>';
                    //echo $refinements . 'refinements2<br/>';

                    //echo $sql; exit;
                    
                    //
                    $statement = $this->pdo->prepare($sql);

                }
                    
                // execute the statement
                $statement->execute();

                //
                $results = [];
                $total = $statement->rowCount();
                $pages = ceil($total/$request['per']); //
                //$current = 1; // current page
                //$limit = $result['limit'];
                //$max = $result['max'];

                //
                if($statement->rowCount() > 0) {

                    //
                    $data = array();
                
                    //
                    while($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
        
                        //
                        $data[] = [

                            'id' => $row['category_id'],		
                            'attributes' => json_decode($row['category_attributes']),		
                            'online' => $row['category_online'],		
                            'public' => $row['category_public'],		
                            'name' => $row['category_name'],		
                            'description' => $row['category_description'],		
                            'slug' => $row['category_slug'],		
                            'parent' => $row['category_parent'],		
                            'images' => json_decode($row['category_images']),		
                            'catalog' => $row['catalog_id'],		
                            'app' => $row['app_id'],		
                            'updated' => $row['time_updated'],		
                            'when' => $row['time_finished'],		
                            
                        ];

                    }

                    $code = 200;
                    $message = "OK";

                } else {

                    //
                    $data = NULL;
                    $code = 204;
                    $message = "No Content";

                }

            } else {

                //
                $data[] = NULL;
                $code = 401;
                $message = "Forbidden - Valid token required";

            }

            $results = array(

                'status' => $code,
                'message' => $message,
                'metadata' => [
                    'page' => $request['page'],
                    'pages' => $pages,
                    'total' => $total
                ],
                'data' => $data,
                'log' => [
                    'process' => $process_id = $this->token->process_id(),
                    'event' => $event_id = $this->token->event_id($process_id)
                ]

            );

            //
            return $results;

        }

        //
        public function updateCategory($request) {

            //
            $domain = $request['domain'];
            $table = $domain;
            $id = $request['id'];

            //
            $set = "";

            // UPDATE OBJECT - SET
            // SKIP as ID won't be getting UPDATED		
            if(isset($request['attributes'])){$set.= " category_attributes = :category_attributes ";}		
            if(isset($request['online'])){$set.= " category_online = :category_online ";}		
            if(isset($request['public'])){$set.= " category_public = :category_public ";}		
            if(isset($request['name'])){$set.= " category_name = :category_name ";}		
            if(isset($request['parent'])){$set.= " category_parent = :category_parent ";}		
            if(isset($request['description'])){$set.= " category_description = :category_description ";}		
            if(isset($request['slug'])){$set.= " category_slug = :category_slug ";}		
            if(isset($request['images'])){$set.= " category_images = :category_images ";}		

            //
            $set = str_replace('  ',',',$set);

            // GET table name
            $condition = $table."_id = :id";
            $condition.= " RETURNING " . $table . "_id";

            // sql statement to update a row in the stock table
            $sql = "UPDATE {$domain} SET ";
            $sql.= $set;
            $sql.= " WHERE ";
            $sql.= $condition;

            //echo $sql; exit;

            $statement = $this->pdo->prepare($sql);
    
            // UPDATE OBJECT - BIND VALUES
            //if(isset($request['id'])){$statement->bindValue(':category_id', $request['id']);}		
            if(isset($request['attributes'])){$statement->bindValue(':category_attributes', $request['attributes']);}		
            if(isset($request['online'])){$statement->bindValue(':category_online', $request['online']);}		
            if(isset($request['public'])){$statement->bindValue(':category_public', $request['public']);}		
            if(isset($request['name'])){$statement->bindValue(':category_name', $request['name']);}		
            if(isset($request['parent'])){$statement->bindValue(':category_parent', $request['parent']);}		
            if(isset($request['description'])){$statement->bindValue(':category_description', $request['description']);}		
            if(isset($request['slug'])){$statement->bindValue(':category_slug', $request['slug']);}		
            if(isset($request['images'])){$statement->bindValue(':category_images', $request['images']);}		

            $statement->bindValue(':id', $id);

            // update data in the database
            $statement->execute();

            $data = $statement->fetchAll();
            
            $data = $data[0]['category_id'];

            // return generated id
            return $data;

        }

        //
        public function deleteCategory($request) {

            $id = $request['id'];
            $domain = $request['domain'];
            $column = prefixed($domain) . '_id';
            $sql = 'DELETE FROM ' . $domain . ' WHERE '.$column.' = :id';
            //echo $id; //exit
            //echo $column; //exit;
            //echo $domain; //exit;
            //echo $sql; //exit

            $statement = $this->pdo->prepare($sql);
            //$statement->bindParam(':column', $column);
            $statement->bindValue(':id', $id);
            $statement->execute();
            return $statement->rowCount();

        }

    }

    //
    class Product {

        //
        private $pdo;
    
        //
        public function __construct($pdo) {

            //
            $this->pdo = $pdo;

            //
            $this->token = new \Marketplace\Token($this->pdo);

        }

        //
        public function insertProduct($request) {

            //generate ID
            if(!isset($request['id'])){$request['id'] = $this->token->new_id('prd');}

            $columns = "";

            // INSERT OBJECT - COLUMNS
            if(isset($request['id'])){$columns.="product_id,";}		
            if(isset($request['attributes'])){$columns.="product_attributes,";}		
            if(isset($request['online'])){$columns.="product_online,";}		
            if(isset($request['public'])){$columns.="product_public,";}		
            if(isset($request['name'])){$columns.="product_name,";}		
            if(isset($request['description'])){$columns.="product_description,";}		
            if(isset($request['slug'])){$columns.="product_slug,";}		
            if(isset($request['images'])){$columns.="product_images,";}		
            if(isset($request['category'])){$columns.="category_id,";}		
            
            $columns.= "app_id,";
            $columns.= "event_id,";
            $columns.= "process_id";

            $values = "";

            // INSERT OBJECT - VALUES
            if(isset($request['id'])){$values.=":product_id,";}		
            if(isset($request['attributes'])){$values.=":product_attributes,";}		
            if(isset($request['online'])){$values.=":product_online,";}		
            if(isset($request['public'])){$values.=":product_public,";}		
            if(isset($request['name'])){$values.=":product_name,";}		
            if(isset($request['description'])){$values.=":product_description,";}		
            if(isset($request['slug'])){$values.=":product_slug,";}		
            if(isset($request['images'])){$values.=":product_images,";}		
            if(isset($request['category'])){$values.=":category_id,";}		
            
            $values.= ":app_id,";
            $values.= ":event_id,";
            $values.= ":process_id";

            // prepare statement for insert
            $sql = "INSERT INTO {$request['domain']} (";
            $sql.= $columns;
            $sql.= ") VALUES (";
            $sql.= $values;
            $sql.= ")";
            $sql.= " RETURNING " . prefixed($request['domain']) . "_id";
    
            //
            $statement = $this->pdo->prepare($sql);
            
            // INSERT OBJECT - BIND VALUES
            if(isset($request['id'])){$statement->bindValue('product_id',$request['id']);}		
            if(isset($request['attributes'])){$statement->bindValue('product_attributes',$request['attributes']);}		
            if(isset($request['online'])){$statement->bindValue('product_online',$request['online']);}		
            if(isset($request['public'])){$statement->bindValue('product_public',$request['public']);}		
            if(isset($request['name'])){$statement->bindValue('product_name',$request['name']);}		
            if(isset($request['description'])){$statement->bindValue('product_description',$request['description']);}		
            if(isset($request['slug'])){$statement->bindValue('product_slug',$request['slug']);}		
            if(isset($request['images'])){$statement->bindValue('product_images',$request['images']);}		
            if(isset($request['category'])){$statement->bindValue('category_id',$request['category']);}		
            
            $statement->bindValue(':app_id', $request['app']);
            $statement->bindValue(':event_id', $this->token->event_id());
            $statement->bindValue(':process_id', $this->token->process_id());
            
            // execute the insert statement
            $statement->execute();

            $data = $statement->fetchAll();
            
            $data = $data[0]['product_id'];

            return $data;
        
        }

        //
        public function selectProducts($request) {

            //echo json_encode($request); exit;

            //$token = new \Core\Token($this->pdo);
            $token = $this->token->validatedToken($request['token']);

            // Retrieve data ONLY if token  
            if($token) {
                
                // domain, app always present
                if(!isset($request['per'])){$request['per']=20;}
                if(!isset($request['page'])){$request['page']=1;}
                if(!isset($request['limit'])){$request['limit']=100;}

                //
                $conditions = "";
                $domain = $request['domain'];
                $prefix = prefixed($domain);

                // SELECT OBJECT - COLUMNS
                $columns = "

                product_ID,		
                product_attributes,		
                product_online,		
                product_public,		
                product_name,		
                product_description,		
                product_slug,		
                product_images,		
                category_ID,		
                app_ID,		
                time_updated,		
                time_finished	

                ";

                $table = $domain;

                //
                $start = 0;

                //
                if(isset($request['page'])) {

                    //
                    $start = ($request['page'] - 1) * $request['per'];
                
                }

                //
                if(!empty($request['id'])) {

                    $conditions.= ' WHERE ';
                    $conditions.= ' ' . $prefix . '_id = :id ';
                    $conditions.= ' AND active = 1 ';
                    $conditions.= ' ORDER BY time_finished DESC ';

                    $subset = " LIMIT 1";

                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= " FROM " . $table;
                    $sql.= $conditions;
                    $sql.= $subset;
                    
                    //echo json_encode($request['id']);
                    //echo '<br/>';
                    //echo $sql; exit;

                    //
                    $statement = $this->pdo->prepare($sql);

                    // bind value to the :id parameter
                    $statement->bindValue(':id', $request['id']);

                    //echo $sql; exit;

                } else {

                    $conditions = "";
                    $refinements = "";

                    // SELECT OBJECT - WHERE CLAUSES
                    // SKIP ID		
                    //if(isset($request['attributes'])){$refinements.="product_attributes"." ILIKE "."'%".$request['attributes']."%' AND ";}		
                    if(isset($request['online'])){$refinements.="product_online"." = "."'".$request['online']."' AND ";}		
                    if(isset($request['public'])){$refinements.="product_public"." = "."'".$request['public']."' AND ";}		
                    if(isset($request['name'])){$refinements.="product_name"." ILIKE "."'%".$request['name']."%' AND ";}		
                    if(isset($request['description'])){$refinements.="product_description"." ILIKE "."'%".$request['description']."%' AND ";}		
                    if(isset($request['slug'])){$refinements.="product_slug"." ILIKE "."'%".$request['slug']."%' AND ";}		
                    //if(isset($request['images'])){$refinements.="product_images"." ILIKE "."'%".$request['images']."%' AND ";}		
                    if(isset($request['category'])){$refinements.="category_id"." = "."'".$request['category']."' AND ";}		

                    //echo $conditions . 'conditions1<br/>';
                    //echo $refinements . 'refinements1<br/>';
                    
                    $conditions.= " WHERE ";
                    $conditions.= $refinements;
                    $conditions.= " active = 1 ";
                    $conditions.= ' AND app_id = \'' . $request['app'] . '\' ';
                    $conditions.= ' AND partner_id = \'' . $request['partner'] . '\' ';
                    $conditions.= " ORDER BY time_finished DESC ";
                    $subset = " OFFSET {$start}" . " LIMIT {$request['per']}";
                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= "FROM " . $table;
                    $sql.= $conditions;
                    $sql.= $subset;

                    //echo $conditions . 'conditions2<br/>';
                    //echo $refinements . 'refinements2<br/>';

                    //echo $sql; exit;
                    
                    //
                    $statement = $this->pdo->prepare($sql);

                }
                    
                // execute the statement
                $statement->execute();

                //
                $results = [];
                $total = $statement->rowCount();
                $pages = ceil($total/$request['per']); //
                //$current = 1; // current page
                //$limit = $result['limit'];
                //$max = $result['max'];

                //
                if($statement->rowCount() > 0) {

                    //
                    $data = array();
                
                    //
                    while($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
        
                        //
                        $data[] = [

                            'id' => $row['product_id'],		
                            'attributes' => json_decode($row['product_attributes']),		
                            'online' => $row['product_online'],		
                            'public' => $row['product_public'],		
                            'name' => $row['product_name'],		
                            'description' => $row['product_description'],		
                            'slug' => $row['product_slug'],		
                            'images' => json_decode($row['product_images']),		
                            'category' => $row['category_id'],		
                            'app' => $row['app_id'],		
                            'updated' => $row['time_updated'],		
                            'when' => $row['time_finished'],		
                            
                        ];

                    }

                    $code = 200;
                    $message = "OK";

                } else {

                    //
                    $data = NULL;
                    $code = 204;
                    $message = "No Content";

                }

            } else {

                //
                $data[] = NULL;
                $code = 401;
                $message = "Forbidden - Valid token required";

            }

            $results = array(

                'status' => $code,
                'message' => $message,
                'metadata' => [
                    'page' => $request['page'],
                    'pages' => $pages,
                    'total' => $total
                ],
                'data' => $data,
                'log' => [
                    'process' => $process_id = $this->token->process_id(),
                    'event' => $event_id = $this->token->event_id($process_id)
                ]

            );

            //
            return $results;

        }

        //
        public function updateProduct($request) {

            //
            $domain = $request['domain'];
            $table = prefixed($domain);
            $id = $request['id'];

            //
            $set = "";

            // UPDATE OBJECT - SET
            // SKIP as ID won't be getting UPDATED		
            if(isset($request['attributes'])){$set.= " product_attributes = :product_attributes ";}		
            if(isset($request['online'])){$set.= " product_online = :product_online ";}		
            if(isset($request['public'])){$set.= " product_public = :product_public ";}		
            if(isset($request['name'])){$set.= " product_name = :product_name ";}		
            if(isset($request['description'])){$set.= " product_description = :product_description ";}		
            if(isset($request['slug'])){$set.= " product_slug = :product_slug ";}		
            if(isset($request['images'])){$set.= " product_images = :product_images ";}		

            //
            $set = str_replace('  ',',',$set);

            // GET table name
            $condition = $table."_id = :id";
            $condition.= " RETURNING " . $table . "_id";

            // sql statement to update a row in the stock table
            $sql = "UPDATE {$domain} SET ";
            $sql.= $set;
            $sql.= " WHERE ";
            $sql.= $condition;

            //echo $sql; exit;

            $statement = $this->pdo->prepare($sql);
    
            // UPDATE OBJECT - BIND VALUES
            //if(isset($request['id'])){$statement->bindValue(':product_id', $request['id']);}		
            if(isset($request['attributes'])){$statement->bindValue(':product_attributes', $request['attributes']);}		
            if(isset($request['online'])){$statement->bindValue(':product_online', $request['online']);}		
            if(isset($request['public'])){$statement->bindValue(':product_public', $request['public']);}		
            if(isset($request['name'])){$statement->bindValue(':product_name', $request['name']);}		
            if(isset($request['description'])){$statement->bindValue(':product_description', $request['description']);}		
            if(isset($request['slug'])){$statement->bindValue(':product_slug', $request['slug']);}		
            if(isset($request['images'])){$statement->bindValue(':product_images', $request['images']);}		

            $statement->bindValue(':id', $id);

            // update data in the database
            $statement->execute();

            $data = $statement->fetchAll();
            
            $data = $data[0]['product_id'];

            // return generated id
            return $data;

        }

        //
        public function deleteProduct($request) {

            $id = $request['id'];
            $domain = $request['domain'];
            $column = prefixed($domain) . '_id';
            $sql = 'DELETE FROM ' . $domain . ' WHERE '.$column.' = :id';
            //echo $id; //exit
            //echo $column; //exit;
            //echo $domain; //exit;
            //echo $sql; //exit

            $statement = $this->pdo->prepare($sql);
            //$statement->bindParam(':column', $column);
            $statement->bindValue(':id', $id);
            $statement->execute();
            return $statement->rowCount();

        }

    }

    //
    class Item {

        //
        private $pdo;
    
        //
        public function __construct($pdo) {

            //
            $this->pdo = $pdo;

            //
            $this->token = new \Marketplace\Token($this->pdo);

        }

        //
        public function insertItem($request) {

            //generate ID
            if(!isset($request['id'])){$request['id'] = $this->token->new_id('itm');}

            $columns = "";

            // INSERT OBJECT - COLUMNS
            if(isset($request['id'])){$columns.="item_id,";}		
            if(isset($request['attributes'])){$columns.="item_attributes,";}		
            if(isset($request['online'])){$columns.="item_online,";}		
            if(isset($request['public'])){$columns.="item_public,";}		
            if(isset($request['name'])){$columns.="item_name,";}		
            if(isset($request['description'])){$columns.="item_description,";}		
            if(isset($request['slug'])){$columns.="item_slug,";}		
            if(isset($request['images'])){$columns.="item_images,";}		
            if(isset($request['product'])){$columns.="product_id,";}		
            
            $columns.= "app_id,";
            $columns.= "event_id,";
            $columns.= "process_id";

            $values = "";

            // INSERT OBJECT - VALUES
            if(isset($request['id'])){$values.=":item_id,";}		
            if(isset($request['attributes'])){$values.=":item_attributes,";}		
            if(isset($request['online'])){$values.=":item_online,";}		
            if(isset($request['public'])){$values.=":item_public,";}		
            if(isset($request['name'])){$values.=":item_name,";}		
            if(isset($request['description'])){$values.=":item_description,";}		
            if(isset($request['slug'])){$values.=":item_slug,";}		
            if(isset($request['images'])){$values.=":item_images,";}		
            if(isset($request['product'])){$values.=":product_id,";}		
            
            $values.= ":app_id,";
            $values.= ":event_id,";
            $values.= ":process_id";

            // prepare statement for insert
            $sql = "INSERT INTO {$request['domain']} (";
            $sql.= $columns;
            $sql.= ") VALUES (";
            $sql.= $values;
            $sql.= ")";
            $sql.= " RETURNING " . prefixed($request['domain']) . "_id";
    
            //
            $statement = $this->pdo->prepare($sql);
            
            // INSERT OBJECT - BIND VALUES
            if(isset($request['id'])){$statement->bindValue('item_id',$request['id']);}		
            if(isset($request['attributes'])){$statement->bindValue('item_attributes',$request['attributes']);}		
            if(isset($request['online'])){$statement->bindValue('item_online',$request['online']);}		
            if(isset($request['public'])){$statement->bindValue('item_public',$request['public']);}		
            if(isset($request['name'])){$statement->bindValue('item_name',$request['name']);}		
            if(isset($request['description'])){$statement->bindValue('item_description',$request['description']);}		
            if(isset($request['slug'])){$statement->bindValue('item_slug',$request['slug']);}		
            if(isset($request['images'])){$statement->bindValue('item_images',$request['images']);}		
            if(isset($request['product'])){$statement->bindValue('product_id',$request['product']);}		
            
            $statement->bindValue(':app_id', $request['app']);
            $statement->bindValue(':event_id', $this->token->event_id());
            $statement->bindValue(':process_id', $this->token->process_id());
            
            // execute the insert statement
            $statement->execute();

            $data = $statement->fetchAll();
            
            $data = $data[0]['item_id'];

            return $data;
        
        }

        //
        public function selectItems($request) {

            //echo json_encode($request); exit;

            //$token = new \Core\Token($this->pdo);
            $token = $this->token->validatedToken($request['token']);

            // Retrieve data ONLY if token  
            if($token) {
                
                // domain, app always present
                if(!isset($request['per'])){$request['per']=20;}
                if(!isset($request['page'])){$request['page']=1;}
                if(!isset($request['limit'])){$request['limit']=100;}

                //
                $conditions = "";
                $domain = $request['domain'];
                $prefix = prefixed($domain);

                // SELECT OBJECT - COLUMNS
                $columns = "

                item_ID,		
                item_attributes,		
                item_online,		
                item_public,		
                item_name,		
                item_description,		
                item_slug,		
                item_images,		
                product_ID,		
                app_ID,		
                time_updated,		
                time_finished	

                ";

                $table = $domain;

                //
                $start = 0;

                //
                if(isset($request['page'])) {

                    //
                    $start = ($request['page'] - 1) * $request['per'];
                
                }

                //
                if(!empty($request['id'])) {

                    $conditions.= ' WHERE ';
                    $conditions.= ' ' . $prefix . '_id = :id ';
                    $conditions.= ' AND active = 1 ';
                    $conditions.= ' ORDER BY time_finished DESC ';

                    $subset = " LIMIT 1";

                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= " FROM " . $table;
                    $sql.= $conditions;
                    $sql.= $subset;
                    
                    //echo json_encode($request['id']);
                    //echo '<br/>';
                    //echo $sql; exit;

                    //
                    $statement = $this->pdo->prepare($sql);

                    // bind value to the :id parameter
                    $statement->bindValue(':id', $request['id']);

                    //echo $sql; exit;

                } else {

                    $conditions = "";
                    $refinements = "";

                    // SELECT OBJECT - WHERE CLAUSES
                    // SKIP ID		
                    //if(isset($request['attributes'])){$refinements.="item_attributes"." ILIKE "."'%".$request['attributes']."%' AND ";}		
                    if(isset($request['online'])){$refinements.="item_online"." = "."'".$request['online']."' AND ";}		
                    if(isset($request['public'])){$refinements.="item_public"." = "."'".$request['public']."' AND ";}		
                    if(isset($request['name'])){$refinements.="item_name"." ILIKE "."'%".$request['name']."%' AND ";}		
                    if(isset($request['description'])){$refinements.="item_description"." ILIKE "."'%".$request['description']."%' AND ";}		
                    if(isset($request['slug'])){$refinements.="item_slug"." ILIKE "."'%".$request['slug']."%' AND ";}		
                    //if(isset($request['images'])){$refinements.="item_images"." ILIKE "."'%".$request['images']."%' AND ";}		
                    if(isset($request['product'])){$refinements.="product_id"." = "."'".$request['product']."' AND ";}		

                    //echo $conditions . 'conditions1<br/>';
                    //echo $refinements . 'refinements1<br/>';
                    
                    $conditions.= " WHERE ";
                    $conditions.= $refinements;
                    $conditions.= " active = 1 ";
                    $conditions.= ' AND app_id = \'' . $request['app'] . '\' ';
                    $conditions.= ' AND partner_id = \'' . $request['partner'] . '\' ';
                    $conditions.= " ORDER BY time_finished DESC ";
                    $subset = " OFFSET {$start}" . " LIMIT {$request['per']}";
                    $sql = "SELECT ";
                    $sql.= $columns;
                    $sql.= "FROM " . $table;
                    $sql.= $conditions;
                    $sql.= $subset;

                    //echo $conditions . 'conditions2<br/>';
                    //echo $refinements . 'refinements2<br/>';

                    //echo $sql; exit;
                    
                    //
                    $statement = $this->pdo->prepare($sql);

                }
                    
                // execute the statement
                $statement->execute();

                //
                $results = [];
                $total = $statement->rowCount();
                $pages = ceil($total/$request['per']); //
                //$current = 1; // current page
                //$limit = $result['limit'];
                //$max = $result['max'];

                //
                if($statement->rowCount() > 0) {

                    //
                    $data = array();
                
                    //
                    while($row = $statement->fetch(\PDO::FETCH_ASSOC)) {
        
                        //
                        $data[] = [

                            'id' => $row['item_id'],		
                            'attributes' => json_decode($row['item_attributes']),		
                            'online' => $row['item_online'],		
                            'public' => $row['item_public'],		
                            'name' => $row['item_name'],		
                            'description' => $row['item_description'],		
                            'slug' => $row['item_slug'],		
                            'images' => json_decode($row['item_images']),		
                            'product' => $row['product_id'],		
                            'app' => $row['app_id'],		
                            'updated' => $row['time_updated'],		
                            'when' => $row['time_finished'],		
                            
                        ];

                    }

                    $code = 200;
                    $message = "OK";

                } else {

                    //
                    $data = NULL;
                    $code = 204;
                    $message = "No Content";

                }

            } else {

                //
                $data[] = NULL;
                $code = 401;
                $message = "Forbidden - Valid token required";

            }

            $results = array(

                'status' => $code,
                'message' => $message,
                'metadata' => [
                    'page' => $request['page'],
                    'pages' => $pages,
                    'total' => $total
                ],
                'data' => $data,
                'log' => [
                    'process' => $process_id = $this->token->process_id(),
                    'event' => $event_id = $this->token->event_id($process_id)
                ]

            );

            //
            return $results;

        }

        //
        public function updateItem($request) {

            //
            $domain = $request['domain'];
            $table = prefixed($domain);
            $id = $request['id'];

            //
            $set = "";

            // UPDATE OBJECT - SET
            // SKIP as ID won't be getting UPDATED		
            if(isset($request['attributes'])){$set.= " item_attributes = :item_attributes ";}		
            if(isset($request['online'])){$set.= " item_online = :item_online ";}		
            if(isset($request['public'])){$set.= " item_public = :item_public ";}		
            if(isset($request['name'])){$set.= " item_name = :item_name ";}		
            if(isset($request['description'])){$set.= " item_description = :item_description ";}		
            if(isset($request['slug'])){$set.= " item_slug = :item_slug ";}		
            if(isset($request['images'])){$set.= " item_images = :item_images ";}		

            //
            $set = str_replace('  ',',',$set);

            // GET table name
            $condition = $table."_id = :id";
            $condition.= " RETURNING " . $table . "_id";

            // sql statement to update a row in the stock table
            $sql = "UPDATE {$domain} SET ";
            $sql.= $set;
            $sql.= " WHERE ";
            $sql.= $condition;

            //echo $sql; exit;

            $statement = $this->pdo->prepare($sql);
    
            // UPDATE OBJECT - BIND VALUES
            //if(isset($request['id'])){$statement->bindValue(':item_id', $request['id']);}		
            if(isset($request['attributes'])){$statement->bindValue(':item_attributes', $request['attributes']);}		
            if(isset($request['online'])){$statement->bindValue(':item_online', $request['online']);}		
            if(isset($request['public'])){$statement->bindValue(':item_public', $request['public']);}		
            if(isset($request['name'])){$statement->bindValue(':item_name', $request['name']);}		
            if(isset($request['description'])){$statement->bindValue(':item_description', $request['description']);}		
            if(isset($request['slug'])){$statement->bindValue(':item_slug', $request['slug']);}		
            if(isset($request['images'])){$statement->bindValue(':item_images', $request['images']);}		

            $statement->bindValue(':id', $id);

            // update data in the database
            $statement->execute();

            $data = $statement->fetchAll();
            
            $data = $data[0]['item_id'];

            // return generated id
            return $data;

        }

        //
        public function deleteItem($request) {

            $id = $request['id'];
            $domain = $request['domain'];
            $column = prefixed($domain) . '_id';
            $sql = 'DELETE FROM ' . $domain . ' WHERE '.$column.' = :id';
            //echo $id; //exit
            //echo $column; //exit;
            //echo $domain; //exit;
            //echo $sql; //exit

            $statement = $this->pdo->prepare($sql);
            //$statement->bindParam(':column', $column);
            $statement->bindValue(':id', $id);
            $statement->execute();
            return $statement->rowCount();

        }

    }
    
?>