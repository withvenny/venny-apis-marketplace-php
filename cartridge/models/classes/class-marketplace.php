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
            if(isset($request['partner'])){$columns.="partner,";}		
            
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
                    $conditions.= ' AND profile_id = \'' . $request['profile'] . '\' ';
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
            
            $data = $data[0]['thread_id'];

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
    
?>