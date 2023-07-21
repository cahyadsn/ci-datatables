<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
/**
* CI Datatables
*
* This is a wrapper class/library based on the native Datatables server-side implementation by Allan Jardine
* found at http://datatables.net/examples/data_sources/server_side.html for CodeIgniter
*
* @package    CodeIgniter
* @subpackage libraries
* @category   library
* @version    0.1
* @author     Cahya DSN <cahyadsn@gmail.com>
* @link       http://github.com/cahyadsn/ci-datatables
*/

class Datatables
{
    /**
    * Global container variables for chained argument results
    *
    */
    protected $ci;
    protected $table;
    protected $select         = array();
    protected $joins          = array();
    protected $columns        = array();
    protected $where          = array();
    protected $filter         = array();
    protected $add_columns    = array();
    protected $edit_columns   = array();
    protected $unset_columns  = array();

    /**
    * Copies an instance of CI
    */
    public function __construct()
    {
        $this->ci =& get_instance();
    }

    /** 
    * Set a specific database event the default, if vopid reconnect the default 
    *
    * @param string $databasename
    * @return void
    */
	public function setdb($databasename = NULL)
	{
		if($databasename == NULL) $databasename = 'default';
		$this->ci->load->database($databasename);
	}

    
    /**
    * Generates the SELECT portion of the query
    *
    * @param string $columns
    * @param bool $backtick_protect
    * @return mixed
    */
    public function select($columns, $backtick_protect = TRUE)
    {
        foreach ($this->explode(',', $columns) as $val){
            $column = trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$2', $val));
            $this->columns[] =  $column;
            $this->select[$column] =  trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$1', $val));
        }
        $this->ci->db->select($columns, $backtick_protect);
        return $this;
    }

    /**
    * Generates the FROM portion of the query
    *
    * @param string $table
    * @return mixed
    */
    public function from($table)
    {
        $this->table = $table;
        $this->ci->db->from($table);
        return $this;
    }

    /**
    * Generates the JOIN portion of the query
    *
    * @param string $table
    * @param string $fk
    * @param string $type
    * @return mixed
    */
    public function join($table, $fk, $type = NULL)
    {
        $this->joins[] = array($table, $fk, $type);
        $this->ci->db->join($table, $fk, $type);
        return $this;
    }

    /**
    * Generates the WHERE portion of the query
    *
    * @param mixed $key_condition
    * @param string $val
    * @param bool $backtick_protect
    * @return mixed
    */
    public function where($key_condition, $val = NULL, $backtick_protect = TRUE)
    {
        $this->where[] = array($key_condition, $val, $backtick_protect);
        $this->ci->db->where($key_condition, $val, $backtick_protect);
        return $this;
    }

    /**
    * Generates the WHERE portion of the query
    *
    * @param mixed $key_condition
    * @param string $val
    * @param bool $backtick_protect
    * @return mixed
    */
    public function filter($key_condition, $val = NULL, $backtick_protect = TRUE)
    {
        $this->filter[] = array($key_condition, $val, $backtick_protect);
        return $this;
    }

    /**
    * Sets additional column variables for adding custom columns
    *
    * @param string $column
    * @param string $content
    * @param string $match_replacement
    * @return mixed
    */
    public function addColumn($column, $content, $match_replacement = NULL)
    {
        $this->add_columns[$column] = array('content' => $content, 'replacement' => $this->explode(',', $match_replacement));
        return $this;
    }

    /**
    * Sets additional column variables for editing columns
    *
    * @param string $column
    * @param string $content
    * @param string $match_replacement
    * @return mixed
    */
    public function editColumn($column, $content, $match_replacement)
    {
        $this->edit_columns[$column][] = array('content' => $content, 'replacement' => $this->explode(',', $match_replacement));
        return $this;
    }

    /**
    * Unset column
    *
    * @param string $column
    * @return mixed
    */
    public function unsetColumn($column)
    {
        $this->unset_columns[] = $column;
        return $this;
    }

    /**
    * Builds all the necessary query segments and performs the main query based on results set from chained statements
    *
    * @param string charset
    * @return string
    */
    public function generate($charset = 'UTF-8')
    {
        $this->getPaging();
        $this->getOrdering();
        $this->getFiltering();
        return $this->produceOutput($charset);
    }

    /**
    * Generates the LIMIT portion of the query
    *
    * @return mixed
    */
    protected function getPaging()
    {
        $iStart = $this->ci->input->post('iDisplayStart');
        $iLength = $this->ci->input->post('iDisplayLength');
        $this->ci->db->limit(($iLength != '' && $iLength != '-1')? $iLength : 10, ($iStart)? $iStart : 0);
    }

    /**
    * Generates the ORDER BY portion of the query
    *
    * @return mixed
    */
    protected function getOrdering()
    {
        if ($this->checkMDataprop()) {
            $mColArray = $this->getMDataprop();
        } elseif ($this->ci->input->post('sColumns')) {
            $mColArray = explode(',', $this->ci->input->post('sColumns'));
        } else {
            $mColArray = $this->columns;
        }
        $mColArray = array_values(array_diff($mColArray, $this->unset_columns));
        $columns = array_values(array_diff($this->columns, $this->unset_columns));
        for ($i = 0; $i < intval($this->ci->input->post('iSortingCols')); $i++) {
            if (
                isset($mColArray[intval($this->ci->input->post('iSortCol_' . $i))])
                && in_array($mColArray[intval($this->ci->input->post('iSortCol_' . $i))], $columns)
                && $this->ci->input->post('bSortable_'.intval($this->ci->input->post('iSortCol_' . $i))) == 'true'
            ) {
                $this->ci->db->order_by($mColArray[intval($this->ci->input->post('iSortCol_' . $i))], $this->ci->input->post('sSortDir_' . $i));
            }
        }
    }

    /**
    * Generates the LIKE portion of the query
    *
    * @return mixed
    */
    protected function getFiltering()
    {
        if ($this->checkMDataprop()) {
            $mColArray = $this->getMDataprop();
        } elseif ($this->ci->input->post('sColumns')) {
            $mColArray = explode(',', $this->ci->input->post('sColumns'));
        } else {
            $mColArray = $this->columns;
        }
        $sWhere = '';
        $sSearch = $this->ci->db->escape($this->ci->input->post('sSearch'));
        $mColArray = array_values(array_diff($mColArray, $this->unset_columns));
        $columns = array_values(array_diff($this->columns, $this->unset_columns));
        if ($sSearch != ''){
            for ($i = 0; $i < count($mColArray); $i++) {
                if ($this->ci->input->post('bSearchable_' . $i) == 'true' && in_array($mColArray[$i], $columns)) {
                    $sWhere .= $this->select[$mColArray[$i]] . " LIKE '%" . $sSearch . "%' OR ";
                }
            }
        }
        $sWhere = substr_replace($sWhere, '', -3);
        if ($sWhere != '') {
            $this->ci->db->where('(' . $sWhere . ')');
        }
        for ($i = 0; $i < intval($this->ci->input->post('iColumns')); $i++) {
            if (isset($_POST['sSearch_' . $i]) && $this->ci->input->post('sSearch_' . $i) != '' && in_array($mColArray[$i], $columns)) {
                $miSearch = explode(',', $this->ci->input->post('sSearch_' . $i));
                foreach ($miSearch as $val) {
                    if (preg_match("/(<=|>=|=|<|>)(\s*)(.+)/i", trim($val), $matches)) {
                        $this->ci->db->where($this->select[$mColArray[$i]].' '.$matches[1], $matches[3]);
                    } else {
                        $this->ci->db->where($this->select[$mColArray[$i]].' LIKE', '%'.$val.'%');
                    }
                }
            }
        }
        foreach ($this->filter as $val) {
            $this->ci->db->where($val[0], $val[1], $val[2]);
        }
    }

    /**
    * Compiles the select statement based on the other functions called and runs the query
    *
    * @return mixed
    */
    protected function getDisplayResult()
    {
        return $this->ci->db->get();
    }

    /**
    * Builds a JSON encoded string data
    *
    * @param string charset
    * @return string
    */
    protected function produceOutput($charset)
    {
        $aaData = array();
        $rResult = $this->getDisplayResult();
        $iTotal = $this->getTotalResults();
        $iFilteredTotal = $this->getTotalResults(true);
        foreach ($rResult->result_array() as $row_key => $row_val) {
            $aaData[$row_key] = ($this->checkMDataprop())? $row_val : array_values($row_val);
            foreach ($this->add_columns as $field => $val) {
                if ($this->checkMDataprop()) {
                    $aaData[$row_key][$field] = $this->execReplace($val, $aaData[$row_key]);
                } else {
                    $aaData[$row_key][] = $this->execReplace($val, $aaData[$row_key]);
                }
            }
            foreach ($this->edit_columns as $modkey => $modval) {
                foreach ($modval as $val) {
                    $aaData[$row_key][($this->checkMDataprop())? $modkey : array_search($modkey, $this->columns)] = $this->execReplace($val, $aaData[$row_key]);
                }
                $aaData[$row_key] = array_diff_key($aaData[$row_key], ($this->checkMDataprop())? $this->unset_columns : array_intersect($this->columns, $this->unset_columns));
            }
            if (!$this->checkMDataprop()) {
                $aaData[$row_key] = array_values($aaData[$row_key]);
            }
        }
        $sColumns = array_diff($this->columns, $this->unset_columns);
        $sColumns = array_merge_recursive($sColumns, array_keys($this->add_columns));
        $sOutput = array
        (
            'sEcho'                => intval($this->ci->input->post('sEcho')),
            'iTotalRecords'        => $iTotal,
            'iTotalDisplayRecords' => $iFilteredTotal,
            'aaData'               => $aaData,
            'sColumns'             => implode(',', $sColumns)
        );
        if (strtolower($charset) == 'utf-8') {
            return json_encode($sOutput);
        } else {
            return $this->jsonify($sOutput);
        }
    }

    /**
    * Get result count
    *
    * @return integer
    */
    protected function getTotalResults($filtering = FALSE)
    {
        if ($filtering) {
            $this->getFiltering();
        }
        foreach ($this->joins as $val) {
            $this->ci->db->join($val[0], $val[1], $val[2]);
        }
        foreach ($this->where as $val) {
            $this->ci->db->where($val[0], $val[1], $val[2]);
        }
        return $this->ci->db->count_all_results($this->table);
    }

    /**
    * Runs callback functions and makes replacements
    *
    * @param mixed $custom_val
    * @param mixed $row_data
    * @return string $custom_val['content']
    */
    protected function execReplace($custom_val, $row_data)
    {
        $replace_string = '';
        if (isset($custom_val['replacement']) && is_array($custom_val['replacement'])){
            foreach ($custom_val['replacement'] as $key => $val) {
                $sval = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($val));
                if (preg_match('/(\w+)\((.*)\)/i', $val, $matches) && function_exists($matches[1])) {
                    $func = $matches[1];
                    $args = preg_split("/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|" . "[\s,]*'([^']+)'[\s,]*|" . "[,]+/", $matches[2], 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                    foreach ($args as $args_key => $args_val) {
                        $args_val = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($args_val));
                        $args[$args_key] = (in_array($args_val, $this->columns))? ($row_data[($this->checkMDataprop())? $args_val : array_search($args_val, $this->columns)]) : $args_val;
                    }
                    $replace_string = call_user_func_array($func, $args);
                } elseif(in_array($sval, $this->columns)) {
                    $replace_string = $row_data[($this->checkMDataprop())? $sval : array_search($sval, $this->columns)];
                } else {
                    $replace_string = $sval;
                }
                $custom_val['content'] = str_ireplace('$' . ($key + 1), $replace_string, $custom_val['content']);
            }
        }
        return $custom_val['content'];
    }

    /**
    * Check mDataprop
    *
    * @return bool
    */
    protected function checkMDataprop()
    {
        if (!$this->ci->input->post('mDataProp_0')) return false;
        for ($i = 0; $i < intval($this->ci->input->post('iColumns')); $i++) {
            if (!is_numeric($this->ci->input->post('mDataProp_' . $i))) {
                return true;
            }
        }
        return false;
    }

    /**
    * Get mDataprop order
    *
    * @return mixed
    */
    protected function getMDataprop()
    {
        $mDataProp = array();
        for ($i = 0; $i < intval($this->ci->input->post('iColumns')); $i++) {
            $mDataProp[] = $this->ci->input->post('mDataProp_' . $i);
        }
        return $mDataProp;
    }

    /**
    * Return the difference of open and close characters
    *
    * @param string $str
    * @param string $open
    * @param string $close
    * @return string $retval
    */
    protected function balanceChars($str, $open, $close)
    {
        $openCount = substr_count($str, $open);
        $closeCount = substr_count($str, $close);
        $retval = $openCount - $closeCount;
        return $retval;
    }

    /**
    * Explode, but ignore delimiter until closing characters are found
    *
    * @param string $delimiter
    * @param string $str
    * @param string $open
    * @param string $close
    * @return mixed $retval
    */
    protected function explode($delimiter, $str, $open='(', $close=')')
    {
        $retval = array();
        $hold = array();
        $balance = 0;
        $parts = explode($delimiter, $str);
        foreach ($parts as $part) {
            $hold[] = $part;
            $balance += $this->balanceChars($part, $open, $close);
            if ($balance < 1) {
                $retval[] = implode($delimiter, $hold);
                $hold = array();
                $balance = 0;
            }
        }
        if (count($hold) > 0) {
            $retval[] = implode($delimiter, $hold);
        }
        return $retval;
    }

    /**
    * Workaround for json_encode's UTF-8 encoding if a different charset needs to be used
    *
    * @param mixed result
    * @return string
    */
    protected function jsonify($result = FALSE)
    {
        if(is_null($result)) return 'null';
        if($result === FALSE) return 'false';
        if($result === TRUE) return 'true';
        if (is_scalar($result)) {
            if (is_float($result)) {
                return floatval(str_replace(',', '.', strval($result)));
            }
            if (is_string($result)) {
                static $jsonReplaces = array(array('\\', '/', '\n', '\t', '\r', '\b', '\f', '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
                return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $result) . '"';
            } else {
                return $result;
            }
        }
        $isList = TRUE;
        for ($i = 0, reset($result); $i < count($result); $i++, next($result)) {
            if (key($result) !== $i) {
                $isList = FALSE;
                break;
            }
        }
        $json = array();
        if ($isList) {
            foreach ($result as $value) {
                $json[] = $this->jsonify($value);
            }
            return '[' . join(',', $json) . ']';
        } else {
            foreach ($result as $key => $value) {
                $json[] = $this->jsonify($key) . ':' . $this->jsonify($value);
            }
            return '{' . join(',', $json) . '}';
        }
    }
}
/* End of file Datatables.php */
/* Location: ./application/libraries/Datatables.php */
