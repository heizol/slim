<?php
    use Zend\Db\Adapter;
    
	class CustomDb {

		private $_server = 'localhost';
		private $_port = '3306';
		private $_user= 'root';
		private $_pass = 'root';
		private $_database = 'tools';

		public $_adapter;
		
		public function __construct() {
		    $this->_adapter = new Zend\Db\Adapter\Adapter(array(
		        'driver' => 'pdo',
		        'dsn' => 'mysql:dbname='. $this->_database .';hostname=' . $this->_server,
		        'username' => $this->_user,
		        'password' => $this->_pass,
		        'driver_options' => array(
		            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
		        ),
		    ));
		    
		    /* eg: 
    		    $artistTable = new TableGateway('tools_user', $db->_adapter);
    		    $rowset = $artistTable->select(function (Select $select) {
    		        $select->where(['mobile' => '18936309997'])->order('id DESC')->limit(10);
    		        //             $select->where(function ($where) {
    		        //                 $where->lessThan('id', 15);
    		        //                 $where->greaterThan('id', 5);
    		        //                 return $where;
    		        //             })->order('id DESC')->limit(10);
    		    
    		        //                  $select->where->like('mobile', '1%');
    		        //                  $select->order('id ASC')->limit(2);
    		    });
    	        var_dump($rowset->toArray());
    	        exit;
		   
		      $set = [
                    'mobile' => '15721490681',
                    'my_money' => '1.00',
                    'add_time' => '1477557661',
                ];
                $rowset = $artistTable->insert($set);
                var_dump($rowset);
                exit;
		   */
		    
		    
		}
	}


