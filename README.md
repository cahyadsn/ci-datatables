# ci-datatables
Library for server side  datatables implementation on codeigniter. CI Datatables is a wrapper class/library based on the native Datatables server-side implementation by Allan Jardine
found at http://datatables.net/examples/data_sources/server_side.html for CodeIgniter

[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

## Features
1. Easy to use. Generates json using only a few lines of code.
2. Support for table joins (left, right, outer, inner, left outer, right outer).
3. Able to define custom columns, and filters.
4. Editable custom variables with callback function support.
5. Supports generation of tables using non utf-8 charsets.

## Requirements
jQuery 1.5+   
DataTables 1.10+   
CodeIgniter 3.*

## Installation
To install the library, copy the libraries/datatables.php file into your application/libraries folder.


## Function Reference

### $this->setdb($database = 'default')
Sets the name of the database configuration, if need another apart of default, 
The name of the database its optional.

Note: you can use method chaining for more compact syntax. 
### $this->datatables->select($columns);
Sets which columns to fetch from the specified table.  

```
$this->datatables->select('id, name, age, gender');
```
*This function also accepts an optional second parameter.
### $this->datatables->from($table);
Sets the primary table in which data will be fetched from.

```
$this->datatables->from('mytable');
```
### $this->datatables->join($table, $fk, [$type]);
Sets join statement variables. If you want DataTables to include data from another table, you can define an a table along with its columns and foreign key relationship.

```
$this->datatables->join('mystates', 'mycountries.state_id = mystates.id', 'left');
```
*This function also accepts an optional third parameter for specifying the join type.  
### $this->datatables->where();
Sets filtering variable to facilitate custom filters.
For additional filtering conditions, the WHERE clause works like CodeIgniter's. So you can do statements like:  

```
$this->datatables->where('id', '5');
```

```
$this->datatables->where('age >', '25');
```

```
$this->datatables->where('position != "admin"');
```

```
$array = array('name' => 'J%', 'status' => 'Single');
$this->datatables->where($array);
```

### $this->datatables->filter();
Sets filtering variable to facilitate custom filters. Adds "(filtered from xxx total entries)" to datatables. Same usage with 'where()' method. 
### $this->datatables->add_column($column, $content, [$match_replacement]);
Sets additional column variables to facilitate custom columns. You can also make your own custom column definitions via the following syntax:
*match_replacement is optional only needed if you have $1 to $n matches in your content pattern  

```
$this->datatables->add_column('edit', '<a href="profiles/edit/$1">EDIT</a>', 'id');
```
### $this->datatables->edit_column($column, $content, $match_replacement);
Sets additional column variables for editing columns. You can also make your own custom column definitions via the following syntax:
*match_replacement is needed in order to replace $1 to $n matches in your content pattern  

```
$this->datatables->edit_column('username', '<a href="profiles/edit/$1">$2</a>', 'id, username');
```
### $this->datatables->unset_column($column);    
### $this->datatables->generate([$output], [$charset]);
Builds all the necessary query segments and performs the main query based on results set from chained statements.  
*optional parameter output (default is json) can be set to 'raw' to return a non-paginated array of the aaData and sColumns  
*optional parameter charset (default is UTF-8) is used when working with non utf-8 characters for json outputs (may decrease performance and be unstable)

```
$this->datatables->generate();
```

```
$this->datatables->generate('json', 'ISO-8859-1');
```

```
$results = $this->datatables->generate('raw');
$data['aaData'] = $results['aaData'];
$data['sColumns'] = $results['sColumns'];
$this->load->view('downloads/csv', $data);
```

## Method Chaining

Method chaining allows you to simplify your syntax by connecting multiple functions. Consider this example:  
```
function list_all()
{
  $this->datatables
    ->select('id, name, age, gender')
    ->from('tbl_profile')
    ->join('tbl_local', 'tbl_profile.local_id = tbl_local.id')
    ->select('country')
    ->join('tbl_states', 'tbl_profile.state_id = tbl_states.id')
    ->select('state')
    ->add_column('view', '<a href="' . base_url() . 'admin/profiles/view/$1"><img src="' . base_url() . 'assets/images/admin/vcard.png" alt="View" title="View" /></a>', 'id')
    ->add_column('edit', '<a href="' . base_url() . 'admin/profiles/edit/$1"><img src="' . base_url() . 'assets/images/admin/vcard_edit.png" alt="Edit" title="Edit" /></a>', 'id')
    ->add_column('delete', '<a href="' . base_url() . 'admin/profiles/delete/$1"><img src="' . base_url() . 'assets/images/admin/vcard_delete.png" alt="Delete" title="Delete" /></a>', 'id');

  $data['result'] = $this->datatables->generate();
  $this->load->view('ajax', $data);
}
```
