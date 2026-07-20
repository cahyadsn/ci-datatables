<?php

namespace Cahyadsn\Datatables;

use CodeIgniter\Database\BaseBuilder;
use CodeIgniter\Database\BaseConnection;
use Config\Database;
use Config\Services;

/**
 * CI4 Datatables
 *
 * A port of the CI Datatables library for CodeIgniter 4.x.
 * Handles server-side processing for DataTables using the CI4 Query Builder.
 *
 * @package    CodeIgniter4
 * @category   Library
 * @author     Cahya DSN <cahyadsn@gmail.com>
 * @link       http://github.com/cahyadsn/ci-datatables
 */
class Datatables
{
    /**
     * Database connection
     *
     * @var BaseConnection
     */
    protected $db;

    /**
     * Request object
     *
     * @var \CodeIgniter\HTTP\IncomingRequest
     */
    protected $request;

    /**
     * Target table name
     *
     * @var string
     */
    protected $table;

    /**
     * Columns list mapping aliases to columns
     *
     * @var array
     */
    protected $select = [];

    /**
     * Raw select parameters to be passed to builder
     *
     * @var array
     */
    protected $selectColumns = [];

    /**
     * Joins settings
     *
     * @var array
     */
    protected $joins = [];

    /**
     * Selected column aliases/names
     *
     * @var array
     */
    protected $columns = [];

    /**
     * Pre-filtering conditions
     *
     * @var array
     */
    protected $where = [];

    /**
     * Custom runtime filters
     *
     * @var array
     */
    protected $filter = [];

    /**
     * Added synthetic columns
     *
     * @var array
     */
    protected $add_columns = [];

    /**
     * Edited columns config
     *
     * @var array
     */
    protected $edit_columns = [];

    /**
     * Unset columns config
     *
     * @var array
     */
    protected $unset_columns = [];

    /**
     * Initialize DB connection and Request service
     *
     * @param BaseConnection|null $db
     * @param \CodeIgniter\HTTP\IncomingRequest|null $request
     */
    public function __construct($db = null, $request = null)
    {
        $this->db = $db ?? Database::connect();
        $this->request = $request ?? Services::request();
    }

    /**
     * Connect to a specific database group
     *
     * @param string|null $databaseGroup
     * @return $this
     */
    public function setdb($databaseGroup = null)
    {
        $this->db = Database::connect($databaseGroup ?? 'default');
        return $this;
    }

    /**
     * Generates the SELECT portion of the query
     *
     * @param string $columns
     * @param bool|null $escape
     * @return $this
     */
    public function select($columns, $escape = null)
    {
        foreach ($this->explode(',', $columns) as $val) {
            $column = trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$2', $val));
            $this->columns[] = $column;
            $this->select[$column] = trim(preg_replace('/(.*)\s+as\s+(\w*)/i', '$1', $val));
        }

        $this->selectColumns[] = [
            'columns' => $columns,
            'escape'  => $escape
        ];

        return $this;
    }

    /**
     * Generates the FROM portion of the query
     *
     * @param string $table
     * @return $this
     */
    public function from($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * Generates the JOIN portion of the query
     *
     * @param string $table
     * @param string $fk
     * @param string|null $type
     * @return $this
     */
    public function join($table, $fk, $type = null)
    {
        $this->joins[] = [$table, $fk, $type];
        return $this;
    }

    /**
     * Generates the WHERE portion of the query
     *
     * @param mixed $key_condition
     * @param string|null $val
     * @param bool|null $escape
     * @return $this
     */
    public function where($key_condition, $val = null, $escape = null)
    {
        $this->where[] = [$key_condition, $val, $escape];
        return $this;
    }

    /**
     * Generates a custom filter condition (used to display filtered totals)
     *
     * @param mixed $key_condition
     * @param string|null $val
     * @param bool|null $escape
     * @return $this
     */
    public function filter($key_condition, $val = null, $escape = null)
    {
        $this->filter[] = [$key_condition, $val, $escape];
        return $this;
    }

    /**
     * Sets additional column variables for adding custom columns
     *
     * @param string $column
     * @param string $content
     * @param string|null $match_replacement
     * @return $this
     */
    public function addColumn($column, $content, $match_replacement = null)
    {
        $this->add_columns[$column] = [
            'content'     => $content,
            'replacement' => $this->explode(',', $match_replacement)
        ];
        return $this;
    }

    /**
     * Sets additional column variables for editing columns
     *
     * @param string $column
     * @param string $content
     * @param string $match_replacement
     * @return $this
     */
    public function editColumn($column, $content, $match_replacement)
    {
        $this->edit_columns[$column][] = [
            'content'     => $content,
            'replacement' => $this->explode(',', $match_replacement)
        ];
        return $this;
    }

    /**
     * Exclude column from output array
     *
     * @param string $column
     * @return $this
     */
    public function unsetColumn($column)
    {
        $this->unset_columns[] = $column;
        return $this;
    }

    /**
     * Compile builder and produce JSON output
     *
     * @param string $charset
     * @return string
     */
    public function generate(string $charset = 'UTF-8'): string
    {
        $builder = $this->compileQuery();

        $this->getPaging($builder);
        $this->getOrdering($builder);
        $this->getFiltering($builder);

        return $this->produceOutput($builder, $charset);
    }

    /**
     * Create builder instance and apply core selects, joins, and wheres
     *
     * @return BaseBuilder
     */
    protected function compileQuery(): BaseBuilder
    {
        $builder = $this->db->table($this->table);

        foreach ($this->selectColumns as $sel) {
            $builder->select($sel['columns'], $sel['escape']);
        }

        foreach ($this->joins as $join) {
            $builder->join($join[0], $join[1], $join[2]);
        }

        foreach ($this->where as $where) {
            $builder->where($where[0], $where[1], $where[2]);
        }

        return $builder;
    }

    /**
     * Generates the LIMIT portion of the query
     *
     * @param BaseBuilder $builder
     * @return void
     */
    protected function getPaging(BaseBuilder $builder)
    {
        $iStart = $this->request->getPost('iDisplayStart');
        $iLength = $this->request->getPost('iDisplayLength');
        
        $limit = ($iLength !== null && $iLength !== '' && $iLength != '-1') ? intval($iLength) : 10;
        $offset = ($iStart !== null && $iStart !== '') ? intval($iStart) : 0;
        
        $builder->limit($limit, $offset);
    }

    /**
     * Generates the ORDER BY portion of the query
     *
     * @param BaseBuilder $builder
     * @return void
     */
    protected function getOrdering(BaseBuilder $builder)
    {
        if ($this->checkMDataprop()) {
            $mColArray = $this->getMDataprop();
        } elseif ($this->request->getPost('sColumns')) {
            $mColArray = explode(',', (string) $this->request->getPost('sColumns'));
        } else {
            $mColArray = $this->columns;
        }

        $mColArray = array_values(array_diff($mColArray, $this->unset_columns));
        $columns = array_values(array_diff($this->columns, $this->unset_columns));
        
        $iSortingCols = intval($this->request->getPost('iSortingCols') ?? 0);
        for ($i = 0; $i < $iSortingCols; $i++) {
            $sortColIndex = intval($this->request->getPost('iSortCol_' . $i));
            if (
                isset($mColArray[$sortColIndex])
                && in_array($mColArray[$sortColIndex], $columns)
                && $this->request->getPost('bSortable_' . $sortColIndex) === 'true'
            ) {
                $builder->orderBy($mColArray[$sortColIndex], (string) $this->request->getPost('sSortDir_' . $i));
            }
        }
    }

    /**
     * Generates the LIKE portion of the query
     *
     * @param BaseBuilder $builder
     * @return void
     */
    protected function getFiltering(BaseBuilder $builder)
    {
        if ($this->checkMDataprop()) {
            $mColArray = $this->getMDataprop();
        } elseif ($this->request->getPost('sColumns')) {
            $mColArray = explode(',', (string) $this->request->getPost('sColumns'));
        } else {
            $mColArray = $this->columns;
        }

        $sWhere = '';
        $sSearch = $this->db->escapeString((string) ($this->request->getPost('sSearch') ?? ''));
        $mColArray = array_values(array_diff($mColArray, $this->unset_columns));
        $columns = array_values(array_diff($this->columns, $this->unset_columns));

        if ($sSearch !== '') {
            for ($i = 0; $i < count($mColArray); $i++) {
                if ($this->request->getPost('bSearchable_' . $i) === 'true' && in_array($mColArray[$i], $columns)) {
                    $sWhere .= $this->select[$mColArray[$i]] . " LIKE '%" . $sSearch . "%' OR ";
                }
            }
        }

        $sWhere = substr_replace($sWhere, '', -3);
        if ($sWhere !== '') {
            $builder->where('(' . $sWhere . ')');
        }

        $iColumns = intval($this->request->getPost('iColumns') ?? 0);
        for ($i = 0; $i < $iColumns; $i++) {
            $sSearchVal = $this->request->getPost('sSearch_' . $i);
            if ($sSearchVal !== null && $sSearchVal !== '' && in_array($mColArray[$i], $columns)) {
                $miSearch = explode(',', (string) $sSearchVal);
                foreach ($miSearch as $val) {
                    if (preg_match("/(<=|>=|=|<|>)(\s*)(.+)/i", trim($val), $matches)) {
                        $builder->where($this->select[$mColArray[$i]] . ' ' . $matches[1], $matches[3]);
                    } else {
                        $builder->where($this->select[$mColArray[$i]] . ' LIKE', '%' . $val . '%');
                    }
                }
            }
        }

        foreach ($this->filter as $val) {
            $builder->where($val[0], $val[1], $val[2]);
        }
    }

    /**
     * Builds a JSON encoded string data
     *
     * @param BaseBuilder $builder
     * @param string $charset
     * @return string
     */
    protected function produceOutput(BaseBuilder $builder, string $charset): string
    {
        $aaData = [];
        $query = $builder->get();
        $rResult = $query->getResultArray();

        $iTotal = $this->getTotalResults();
        $iFilteredTotal = $this->getTotalResults(true);

        foreach ($rResult as $row_key => $row_val) {
            $aaData[$row_key] = $this->checkMDataprop() ? $row_val : array_values($row_val);
            
            foreach ($this->add_columns as $field => $val) {
                if ($this->checkMDataprop()) {
                    $aaData[$row_key][$field] = $this->execReplace($val, $aaData[$row_key]);
                } else {
                    $aaData[$row_key][] = $this->execReplace($val, $aaData[$row_key]);
                }
            }

            foreach ($this->edit_columns as $modkey => $modval) {
                foreach ($modval as $val) {
                    $targetIndex = $this->checkMDataprop() ? $modkey : array_search($modkey, $this->columns);
                    if ($targetIndex !== false) {
                        $aaData[$row_key][$targetIndex] = $this->execReplace($val, $aaData[$row_key]);
                    }
                }
            }

            if ($this->checkMDataprop()) {
                foreach ($this->unset_columns as $unset_col) {
                    unset($aaData[$row_key][$unset_col]);
                }
            } else {
                foreach ($this->unset_columns as $unset_col) {
                    $index = array_search($unset_col, $this->columns);
                    if ($index !== false) {
                        unset($aaData[$row_key][$index]);
                    }
                }
            }

            if (!$this->checkMDataprop()) {
                $aaData[$row_key] = array_values($aaData[$row_key]);
            }
        }

        $sColumns = array_diff($this->columns, $this->unset_columns);
        $sColumns = array_merge_recursive($sColumns, array_keys($this->add_columns));

        $sOutput = [
            'sEcho'                => intval($this->request->getPost('sEcho') ?? 0),
            'iTotalRecords'        => $iTotal,
            'iTotalDisplayRecords' => $iFilteredTotal,
            'aaData'               => $aaData,
            'sColumns'             => implode(',', $sColumns)
        ];

        if (strtolower($charset) === 'utf-8') {
            return (string) json_encode($sOutput);
        } else {
            return $this->jsonify($sOutput);
        }
    }

    /**
     * Get result count
     *
     * @param bool $filtering
     * @return int
     */
    protected function getTotalResults(bool $filtering = false): int
    {
        $builder = $this->db->table($this->table);

        foreach ($this->joins as $val) {
            $builder->join($val[0], $val[1], $val[2]);
        }

        foreach ($this->where as $val) {
            $builder->where($val[0], $val[1], $val[2]);
        }

        if ($filtering) {
            $this->getFiltering($builder);
        }

        return $builder->countAllResults();
    }

    /**
     * Runs callback functions and makes replacements
     *
     * @param array $custom_val
     * @param array $row_data
     * @return string
     */
    protected function execReplace(array $custom_val, array $row_data): string
    {
        if (isset($custom_val['replacement']) && is_array($custom_val['replacement'])) {
            foreach ($custom_val['replacement'] as $key => $val) {
                $sval = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($val));
                if (preg_match('/(\w+)\((.*)\)/i', $val, $matches) && function_exists($matches[1])) {
                    $func = $matches[1];
                    $args = preg_split("/[\s,]*\\\"([^\\\"]+)\\\"[\s,]*|" . "[\s,]*'([^']+)'[\s,]*|" . "[,]+/", $matches[2], 0, PREG_SPLIT_NO_EMPTY | PREG_SPLIT_DELIM_CAPTURE);
                    foreach ($args as $args_key => $args_val) {
                        $args_val = preg_replace("/(?<!\w)([\'\"])(.*)\\1(?!\w)/i", '$2', trim($args_val));
                        
                        $colIndex = array_search($args_val, $this->columns);
                        $args[$args_key] = (in_array($args_val, $this->columns)) 
                            ? ($row_data[$this->checkMDataprop() ? $args_val : $colIndex]) 
                            : $args_val;
                    }
                    $replace_string = call_user_func_array($func, $args);
                } elseif (in_array($sval, $this->columns)) {
                    $colIndex = array_search($sval, $this->columns);
                    $replace_string = $row_data[$this->checkMDataprop() ? $sval : $colIndex];
                } else {
                    $replace_string = $sval;
                }
                $custom_val['content'] = str_ireplace('$' . ($key + 1), (string) $replace_string, $custom_val['content']);
            }
        }
        return $custom_val['content'];
    }

    /**
     * Check mDataprop
     *
     * @return bool
     */
    protected function checkMDataprop(): bool
    {
        if (!$this->request->getPost('mDataProp_0')) {
            return false;
        }

        $iColumns = intval($this->request->getPost('iColumns') ?? 0);
        for ($i = 0; $i < $iColumns; $i++) {
            if (!is_numeric($this->request->getPost('mDataProp_' . $i))) {
                return true;
            }
        }
        return false;
    }

    /**
     * Get mDataprop order
     *
     * @return array
     */
    protected function getMDataprop(): array
    {
        $mDataProp = [];
        $iColumns = intval($this->request->getPost('iColumns') ?? 0);
        for ($i = 0; $i < $iColumns; $i++) {
            $mDataProp[] = $this->request->getPost('mDataProp_' . $i);
        }
        return $mDataProp;
    }

    /**
     * Return the difference of open and close characters
     *
     * @param string $str
     * @param string $open
     * @param string $close
     * @return int
     */
    protected function balanceChars($str, $open, $close): int
    {
        $openCount = substr_count($str, $open);
        $closeCount = substr_count($str, $close);
        return $openCount - $closeCount;
    }

    /**
     * Explode, but ignore delimiter until closing characters are found
     *
     * @param string $delimiter
     * @param string $str
     * @param string $open
     * @param string $close
     * @return array
     */
    protected function explode($delimiter, $str, $open = '(', $close = ')'): array
    {
        if ($str === null || $str === '') {
            return [];
        }

        $retval = [];
        $hold = [];
        $balance = 0;
        $parts = explode($delimiter, $str);

        foreach ($parts as $part) {
            $hold[] = $part;
            $balance += $this->balanceChars($part, $open, $close);
            if ($balance < 1) {
                $retval[] = implode($delimiter, $hold);
                $hold = [];
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
     * @param mixed $result
     * @return string
     */
    protected function jsonify($result = false): string
    {
        if (is_null($result)) {
            return 'null';
        }
        if ($result === false) {
            return 'false';
        }
        if ($result === true) {
            return 'true';
        }

        if (is_scalar($result)) {
            if (is_float($result)) {
                return (string) floatval(str_replace(',', '.', strval($result)));
            }
            if (is_string($result)) {
                static $jsonReplaces = [
                    ['\\', '/', "\n", "\t", "\r", "\b", "\f", '"'],
                    ['\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"']
                ];
                return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $result) . '"';
            }
            return (string) $result;
        }

        $isList = true;
        for ($i = 0, reset($result); $i < count($result); $i++, next($result)) {
            if (key($result) !== $i) {
                $isList = false;
                break;
            }
        }

        $json = [];
        if ($isList) {
            foreach ($result as $value) {
                $json[] = $this->jsonify($value);
            }
            return '[' . implode(',', $json) . ']';
        } else {
            foreach ($result as $key => $value) {
                $json[] = $this->jsonify($key) . ':' . $this->jsonify($value);
            }
            return '{' . implode(',', $json) . '}';
        }
    }
}
