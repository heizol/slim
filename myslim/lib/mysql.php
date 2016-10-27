<?php
    use Zend\Db;
    use Zend\Db\TableGateway\TableGateway;
    use Zend\Db\Sql\Sql;
    
	class CustomDb extends Zend\Db\TableGateway\TableGateway {

		private $_server = 'localhost';
		private $_port = '3306';
		private $_user= 'root';
		private $_pass = 'root';
		private $_database = 'tools';

		private $_db = null;
        
		public function __construct() {
		    $adapter = new Zend\Db\Adapter\Adapter([
		        'driver'   => 'PDO',
		        'database' => $this->_database,
		        'username' => $this->_user,
		        'password' => $this->_pass,
		    ]);
		    
		    // or 
		    
		    $adapter = new Zend\Db\Adapter\Adapter(array(
		        'driver' => 'pdo',
		        'dsn' => 'mysql:dbname='. $this->_database .';hostname=' . $this->_server,
		        'username' => $this->_user,
		        'password' => $this->_pass,
		        'driver_options' => array(
		            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
		        ),
		    ));
		    
		    $table = new TableGateway('tools_user', $adapter);
		    $select = $table->select('id > 1');
// 		    $select->where('id > 1');
		    $resultSet = $table->selectWith($select);
		    $result = $resultSet->toArray();
		    var_dump($result);
		    exit;
		    
		    $sql = new Sql($adapter);
		    $select = $sql->select();
		    $select->from('tools_user');
		    $select->where(['id' => [11, 12]]);
		    //sql语句
		    $selectString = $sql->buildSqlString($select);
		    
		    $rSelect = $adapter->selectWith($select);
		    var_dump($rSelect);
		    exit;
		    $statement = $sql->prepareStatementForSqlObject($select);
		    $results = $statement->execute();
		    var_dump();
		}
        
	}


