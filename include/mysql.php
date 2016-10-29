<?php
if(!defined("_KATE_MAIN")) die("You have no access to this file");
include_once($global_path."config/config.php");
error_reporting(0);
class sql_db {
	var $db_connect_id;
	var $query_result;
	var $row = array();
	var $rowset = array();
	var $num_queries = 0;
	var $total_time_db = 0;
	var $time_query = "";

	function sql_db($sqlserver, $sqluser, $sqlpassword, $database, $persistency = true) {
		$this->db_connect_id = ($persistency) ? @mysql_pconnect($sqlserver, $sqluser, $sqlpassword) : @mysql_connect($sqlserver, $sqluser, $sqlpassword);
		if ($this->db_connect_id) {
			if ($database != "" && !@mysql_select_db($database)) {
				@mysql_close($this->db_connect_id);
				$this->db_connect_id = false;
			}
			return $this->db_connect_id;
		} else {
			return false;
		}
	}

	function sql_close() {
		if ($this->db_connect_id) {
			if ($this->query_result) @mysql_free_result($this->query_result);
			$result = @mysql_close($this->db_connect_id);
			return $result;
		} else {
			return false;
		}
	}

	function sql_query($query = "", $transaction = false) {
		unset($this->query_result);
		if ($query != "") {
			$st = array_sum(explode(" ", microtime()));
			//$query = mysql_real_escape_string(trim($query));
			$this->query_result = @mysql_query($query, $this->db_connect_id);
			$total_tdb = round(array_sum(explode(" ", microtime())) - $st, 5);
			$this->total_time_db += $total_tdb;
			$this->time_query .= "".$total_tdb > 0.01."" ? "<font color=\"red\"><b>".$total_tdb."</b></font> сек. - [".$query."]<br /><br />" : "<font color=\"green\"><b>".$total_tdb."</b></font> сек. - [".$query."]<br /><br />";
		}
		if ($this->query_result) {
			$this->num_queries += 1;
			unset($this->row[$this->query_result]);
			unset($this->rowset[$this->query_result]);
			return $this->query_result;
		} else {
			return ($transaction == END_TRANSACTION) ? true : false;
		}
	}

	function sql_numrows($query_id = 0) {
		if (!$query_id) $query_id = $this->query_result;
		if ($query_id) {
			$result = @mysql_num_rows($query_id);
			return $result;
		} else {
			return false;
		}
	}

	function sql_affectedrows() {
		if ($this->db_connect_id) {
			$result = @mysql_affected_rows($this->db_connect_id);
			return $result;
		} else {
			return false;
		}
	}

	function sql_numfields($query_id = 0) {
		if (!$query_id) $query_id = $this->query_result;
		if ($query_id) {
			$result = @mysql_num_fields($query_id);
			return $result;
		} else {
			return false;
		}
	}

	function sql_fieldname($offset, $query_id = 0) {
		if (!$query_id) $query_id = $this->query_result;
		if ($query_id) {
			$result = @mysql_field_name($query_id, $offset);
			return $result;
		} else {
			return false;
		}
	}

	function sql_fieldtype($offset, $query_id = 0) {
		if (!$query_id) $query_id = $this->query_result;
		if($query_id) {
			$result = @mysql_field_type($query_id, $offset);
			return $result;
		} else {
			return false;
		}
	}

	function sql_fetchrow($query_id = 0) {
		if (!$query_id) $query_id = $this->query_result;
		if ($query_id) {
			$this->row[$query_id] = @mysql_fetch_array($query_id);
			return str_replace("&amp;", "&", $this->row[$query_id]);
		} else {
			return false;
		}
	}

	function sql_fetchrowset($query_id = 0) {
		if (!$query_id) $query_id = $this->query_result;
		if ($query_id) {
			unset($this->rowset[$query_id]);
			unset($this->row[$query_id]);
			while ($this->rowset[$query_id] = @mysql_fetch_array($query_id)) {
				$result[] = $this->rowset[$query_id];
			}
			return $result;
		} else {
			return false;
		}
	}

	function sql_fetchfield($field, $rownum = -1, $query_id = 0) {
		if (!$query_id) $query_id = $this->query_result;
		if ($query_id) {
			if ($rownum > -1) {
				$result = @mysql_result($query_id, $rownum, $field);
			} else {
				if (empty($this->row[$query_id]) && empty($this->rowset[$query_id])) {
					if ($this->sql_fetchrow()) {
						$result = $this->row[$query_id][$field];
					}
				} else {
					if ($this->rowset[$query_id]) {
						$result = $this->rowset[$query_id][0][$field];
					} else if ($this->row[$query_id]) {
						$result = $this->row[$query_id][$field];
					}
				}
			}
			return $result;
		} else {
			return false;
		}
	}

	function sql_rowseek($rownum, $query_id = 0) {
		if (!$query_id) $query_id = $this->query_result;
		if ($query_id) {
			$result = @mysql_data_seek($query_id, $rownum);
			return $result;
		} else {
			return false;
		}
	}

	function sql_nextid() {
		if ($this->db_connect_id) {
			$result = @mysql_insert_id($this->db_connect_id);
			return $result;
		} else {
			return false;
		}
	}

	function sql_freeresult($query_id = 0){
		if (!$query_id) $query_id = $this->query_result;
		if ($query_id) {
			unset($this->row[$query_id]);
			unset($this->rowset[$query_id]);
			@mysql_free_result($query_id);
			return true;
		} else {
			return false;
		}
	}

	function sql_error($query_id = 0) {
		$result["message"] = @mysql_error($this->db_connect_id);
		$result["code"] = @mysql_errno($this->db_connect_id);
		return $result;
	}

	function sql_info($full=0) {
		if(!isset($this->full)) $this->full = 0;
		if ($this->full==1) {
			return "<small>Всего запросов к базе данных: ".$this->num_queries." | Общее время выполнения запросов: ".$this->total_time_db." | Время на каждый из запросов: ".$this->time_query."</small>";
		} else {
			return "<small>Всего запросов к базе данных: ".$this->num_queries." | Общее время выполнения запросов: ".$this->total_time_db." сек.</small>";
		}
	}
}

$db = new sql_db($dbhost, $dbuname, $dbpass, $dbname, false);
if (!$db->db_connect_id) die("Ошибка соединения с базой данных");
mysql_query("SET names utf8");
?>