<?php

	class CustomDb  {

		private $_server = '192.168.99.100';
		private $_port  = '3306';
		private $_user	= 'root';
		private $_pass	= 'iamchaichai';
		private $_database = 'wangyiyungou';

		private $_conn = null;

		public function __construct() {
			if (ENVIRONMENT == 'PRODUCTION') {
				$this->_server = 'rm-uf6a42856pfasa0da.mysql.rds.aliyuncs.com';
				$this->_port  = '3306';
				$this->_user	= 'kuaishouduobao';
				$this->_pass	= 'Kuaishouduobao123';
				$this->_database = 'kuaishouduobao';
			} else {
				$this->_server  = 'localhost';
				$this->_port    = '3306';
				$this->_user	= 'root';
				$this->_pass	= 'root';
				$this->_database = 'wangyiyungou';
			}

			$this->_initConn();
		}
		private function _initConn() {

			$dbh = new PDO('mysql:host='.$this->_server.';port='.$this->_port.';dbname='.$this->_database, $this->_user, $this->_pass, array( PDO::ATTR_PERSISTENT => false, PDO::ATTR_PERSISTENT => true));
			$stmt = $dbh->prepare("SET NAMES UTF8;SET AUTOCOMMIT=0;");
			$stmt->execute();
			
			$this->_conn = $dbh;
		}
		
		public function getConnection() {	
			if (!$this->_conn) {
				$this->_initConn();
			}
			return $this->_conn;
		}

		// Result In Array
		public function GetList($sql, $parameters=array()) {
			if (!$this->_conn) {
				$this->_initConn();
			}


			$sth = $this->_conn->prepare($sql);
			$sth->execute($parameters);
			$result = $sth->fetchAll(PDO::FETCH_ASSOC);
			return $result;

		}

		// Result In Array
		public function GetOne($sql, $parameters=array()) {
			if (!$this->_conn) {
				$this->_initConn();
			}

			$sth = $this->_conn->prepare($sql);
			$sth->execute($parameters);
			$result = $sth->fetch(PDO::FETCH_ASSOC);
			return $result;
		}


		// Result In Array
		public function create($table, $parameters=array()) {
			if (!$this->_conn) {
				$this->_initConn();
			}

			$keys = array();
			$vals = array();
			foreach($parameters as $key => $val) {
				$keys[] = '`'.$key.'`';
				$vals[] = ':'.$key;
			}
			$sql = "INSERT INTO `".$table."` (".(implode(",",$keys)).") VALUES (".(implode(",",$vals)).")";

			$sth = $this->_conn->prepare($sql);
			$sth->execute($parameters);


			return $this->_conn->lastInsertId();
		}

		/*
		public function update($table, $parameters=array(), $where=array()) {
			if (!$this->_conn) {
				$this->_initConn();
			}

			$wheres = array();
			$sets = array();

			foreach($where as $key => $val) {
				$wheres[] = '`'.$key.'`=:'.$key;
				$parameters[$key] = $val;
			}

			foreach($parameters as $key => $val) {
				$sets[] = '`'.$key.'`=:'.$key;
				$parameters[$key] = $val;
			}
			$sql = "UPDATE `".$table."` SET ".(implode(",",$sets))." WHERE ".implode(" AND ", $wheres);

			$sth = $this->_conn->prepare($sql);
			$sth->execute($parameters);
			return true;
		}
		*/

		public function update($table, $set=array(), $where=array()) {
			if (!$this->_conn) {
				$this->_initConn();
			}

			$wheres = array();
			$sets = array();
			$parameters = array();

			foreach($set as $key => $val) {
				$sets[] = '`'.$key.'`=:p_'.$key;
				$parameters['p_'.$key] = $val;
			}

			foreach($where as $key => $val) {
				$wheres[] = '`'.$key.'`=:w_'.$key;
				$parameters['w_'.$key] = $val;
			}


			$sql = "UPDATE `".$table."` SET ".(implode(",",$sets))." WHERE ".implode(" AND ", $wheres);

			$sth = $this->_conn->prepare($sql);
			$sth->execute($parameters);
			return true;
//			return array($sql,$parameters);
		}


		public function remove($table, $where=array()) {
			if (!$this->_conn) {
				$this->_initConn();
			}

			$wheres	    = array();
			$parameters = array();

			foreach($where as $key => $val) {
				$wheres[] = '`'.$key.'`=:'.$key;
				$parameters[$key] = $val;
			}


			$sql = "DELETE FROM `".$table."` WHERE ".implode(" AND ", $wheres);

			$sth = $this->_conn->prepare($sql);
			$sth->execute($parameters);
			return true;

		}
		

		public function beginTransaction() {
			if (!$this->_conn) {
				$this->_initConn();
			}
			$this->_conn->setAttribute(PDO::ATTR_AUTOCOMMIT, FALSE);
			$this->_conn->beginTransaction();
		}


		public function commit() {
			if (!$this->_conn) {
				$this->_initConn();
			}
			$this->_conn->commit();
		}

		public function rollBack() {
			if (!$this->_conn) {
				$this->_initConn();
			}
			$this->_conn->rollBack();
		}
		public function exec($sql, $parameters=array()) {
			if (!$this->_conn) {
				$this->_initConn();
			}

			$sth = $this->_conn->prepare($sql);
			return $sth->execute($parameters);


//			return $this->_conn->exec($sql);
		}

		public function lastInsertId() {
			if (!$this->_conn) {
				$this->_initConn();
			}
			return $this->_conn->lastInsertId();
		}


	}


