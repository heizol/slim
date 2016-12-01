<?php

	require 'Credis/Client.php';
	require 'Credis/Cluster.php';

	class CustomRedis  {

		private $_cluster = null;

		public function __construct() {
			if (ENVIRONMENT == 'production') {
				$this->_servers = array(
					array(
							'host'=>'127.0.0.1',
							'port'=>'6379',
							'alias'=>'master',
					),
					array(
							'host'=>'127.0.0.1',
							'port'=>'6379',
							'alias'=>'slave',
					),
				);
			} else {
				$this->_servers = array(
					array(
							'host'=>'localhost',
							'port'=>'6379',
							'alias'=>'master',
					),
					array(
							'host'=>'localhost',
							'port'=>'6379',
							'alias'=>'slave',
					),
				);
			}

			$this->_initConn();
		}
		public function __call(string $function_name, array $arguments) {
            echo "call".$function_name;
            exit;
		}
		private function _initConn() {
			$cluster = new Credis_Cluster($this->_servers);

//			$cluster->set('key','value'.time());
//			echo "Alpha: ".$cluster->client('slave')->get('key');
			$this->_cluster = $cluster;
//          $this->adsf();
		}		
		public function getConnection() {	
			if (!$this->_cluster) {
				$this->_initConn();
			}
			return $this->_cluster;
		}
		
		public function setEx($key, $seconds, $value) {
			if (!$this->_cluster) {
				$this->_initConn();
			}			
			$this->_cluster->client('master')->setEx($key, $seconds, $value);
			return true;
		}

		public function expire(string $key, int $seconds) {
			if (!$this->_cluster) {
				$this->_initConn();
			}			
			$this->_cluster->client('master')->expire($key, $seconds);
			return true;
		}

		public function expireAt(string $key, int $seconds) {
			if (!$this->_cluster) {
				$this->_initConn();
			}			
			$this->_cluster->client('master')->expireAt($key, $seconds);
			return true;
		}

		public function set($key, $val) {
			if (!$this->_cluster) {
				$this->_initConn();
			}			
			$this->_cluster->client('master')->set($key,$val);
			return true;
		}


		public function get($key) {
			if (!$this->_cluster) {
				$this->_initConn();
			}			
			return $this->_cluster->client('master')->get($key);
		}
		public function del($key) {
			if (!$this->_cluster) {
				$this->_initConn();
			}			
			return $this->_cluster->client('master')->del($key);
		}
		

		public function lSet(string $key, int $index, mixed $value) {
			if (!$this->_cluster) {
				$this->_initConn();
			}			
			return $this->_cluster->client('master')->lSet( $key, $index, $value);
		}
		
		public function lRange(string $key, int $start, int $stop) {
			if (!$this->_cluster) {
				$this->_initConn();
			}			
			return $this->_cluster->client('slave')->lRange( $key, $start, $stop);
		}

		
		public function hSet(string $key, string $field, string $value) {
			if (!$this->_cluster) {
				$this->_initConn();
			}			
			return $this->_cluster->client('master')->hSet($key, $field, $value);
		}
		public function hGet(string $key, string $field) {
			if (!$this->_cluster) {
				$this->_initConn();
			}			
			return $this->_cluster->client('slave')->hGet($key, $field);
		}
		public function hIncrBy(string $key, string $field, int $value) {
			if (!$this->_cluster) {
				$this->_initConn();
			}			
			return $this->_cluster->client('master')->hIncrBy($key, $field, $value);
		}
		public function hLen(string $key) {
			if (!$this->_cluster) {
				$this->_initConn();
			}			
			return $this->_cluster->client('slave')->hLen($key);
		}
		public function hDel(string $key, string $field) {
			if (!$this->_cluster) {
				$this->_initConn();
			}			
			return $this->_cluster->client('master')->hDel($key, $field);
		}



		public function zAdd(string $key, int $index, $value) {
			if (!$this->_cluster) {
				$this->_initConn();
			}			
			return $this->_cluster->client('master')->zAdd( $key, $index, $value);
		}


		public function zRange(string $key, int $min, int $max) {
			if (!$this->_cluster) {
				$this->_initConn();
			}			
			return $this->_cluster->client('slave')->zRange( $key, $min, $max);
		}



		public function zRangeByScore(string $key, int $min, int $max, string $withscores=null) {
			if (!$this->_cluster) {
				$this->_initConn();
			}			
			if (is_null($withscores)) {
				return $this->_cluster->client('slave')->zRangeByScore( $key, $min, $max);
			} else {
				return $this->_cluster->client('slave')->zRangeByScore( $key, $min, $max, "withscores");
			}
		}

		public function zRem(string $key, $value) {
			if (!$this->_cluster) {
				$this->_initConn();
			}			
			return $this->_cluster->client('master')->zRem( $key, $value);
		}


		public function zRemRangeByScore(string $key, int $min, int $max) {
			if (!$this->_cluster) {
				$this->_initConn();
			}			
			return $this->_cluster->client('master')->zRemRangeByScore( $key, $min, $max);
		}
		

		public function zCount(string $key, int $min, int $max) {
			if (!$this->_cluster) {
				$this->_initConn();
			}			
			return $this->_cluster->client('slave')->zCount( $key, $min, $max);
		}
		
		public function ttl(string $key) {
		    if (!$this->_cluster) {
		        $this->_initConn();
		    }
		    return $this->_cluster->client('slave')->ttl( $key);
		}

		public function auth($key) {
		    if (!$this->_cluster) {
		        $this->_initConn();
		    }
		    return $this->_cluster->client('master')->auth( $key);
		}
	}
?>
