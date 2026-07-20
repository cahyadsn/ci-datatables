# ci-datatables

Library for server side datatables implementation on CodeIgniter 4. CI Datatables is a wrapper class/library based on the native Datatables server-side implementation by Allan Jardine for CodeIgniter.

[![GitHub license](https://img.shields.io/badge/license-MIT-blue.svg)](LICENSE)

## Features
1. Easy to use. Generates json using only a few lines of code.
2. Support for table joins (left, right, outer, inner, left outer, right outer).
3. Able to define custom columns, and filters.
4. Editable custom variables with callback function support.
5. Supports generation of tables using non utf-8 charsets.

## Requirements
- jQuery 1.8+   
- DataTables 2.3+   
- CodeIgniter 4.4.4+
- PHP 8.3+

## Installation

### Via Composer
You can install this library in your CodeIgniter 4 project using Composer:

```bash
composer require cahyadsn/ci-datatables
```

### Manual Installation
If you prefer not to use Composer, you can download `src/Datatables.php` and place it in your CodeIgniter 4 application's `app/Libraries` directory. 
If installed manually, update its namespace to `App\Libraries` at the top of the file:
```php
namespace App\Libraries;
```

---

## Function Reference

To use the library, instantiate it:
```php
$datatables = new \Cahyadsn\Datatables\Datatables();
// Or if manually installed:
// $datatables = new \App\Libraries\Datatables();
```

### $datatables->setdb($database = 'default')
Sets the name of the database configuration group if you need a group other than `default`. Returns `$this` for chaining.

### $datatables->select($columns, $escape = null);
Sets which columns to fetch from the specified table.  

```php
$datatables->select('id, name, age, gender');
```

### $datatables->from($table);
Sets the primary table in which data will be fetched from.

```php
$datatables->from('mytable');
```

### $datatables->join($table, $fk, [$type]);
Sets join statement variables. If you want DataTables to include data from another table, define the relationship.

```php
$datatables->join('mystates', 'mycountries.state_id = mystates.id', 'left');
```

### $datatables->where($key_condition, $val = null, $escape = null);
Sets filtering variable to facilitate custom filters. The WHERE clause works like CodeIgniter 4's Query Builder.

```php
$datatables->where('id', '5');
$datatables->where('age >', '25');
```

### $datatables->filter($key_condition, $val = null, $escape = null);
Sets filtering variable to facilitate custom filters. Adds filtered results info to DataTables. Same usage as `where()`.

### $datatables->addColumn($column, $content, [$match_replacement]);
Sets additional column variables to facilitate custom columns.
* `$match_replacement` is optional, and only needed if you have `$1` to `$n` matches in your content pattern.

```php
$datatables->addColumn('edit', '<a href="profiles/edit/$1">EDIT</a>', 'id');
```

### $datatables->editColumn($column, $content, $match_replacement);
Sets additional column variables for editing existing columns.
* `$match_replacement` is needed in order to replace `$1` to `$n` matches in your content pattern.

```php
$datatables->editColumn('username', '<a href="profiles/edit/$1">$2</a>', 'id, username');
```

### $datatables->unsetColumn($column);    
Excludes columns from the output array.

### $datatables->generate([$charset = 'UTF-8']);
Builds all the query segments and runs queries to return the DataTables-compliant JSON response.

```php
return $this->response->setJSON($datatables->generate());
```

---

## Method Chaining Example (CI4 Controller)

```php
namespace App\Controllers;

use Cahyadsn\Datatables\Datatables;

class Profiles extends BaseController
{
    public function listAll()
    {
        $datatables = new Datatables();
        
        $json = $datatables
            ->select('id, name, age, gender')
            ->from('tbl_profile')
            ->join('tbl_local', 'tbl_profile.local_id = tbl_local.id')
            ->select('country')
            ->join('tbl_states', 'tbl_profile.state_id = tbl_states.id')
            ->select('state')
            ->addColumn('view', '<a href="' . base_url('admin/profiles/view/$1') . '"><img src="' . base_url('assets/images/admin/vcard.png') . '" alt="View" /></a>', 'id')
            ->addColumn('edit', '<a href="' . base_url('admin/profiles/edit/$1') . '"><img src="' . base_url('assets/images/admin/vcard_edit.png') . '" alt="Edit" /></a>', 'id')
            ->generate();

        return $this->response->setJSON($json);
    }
}
```

## Running Tests

To run the unit tests, install development dependencies using Composer and run PHPUnit:

```bash
composer install
vendor/bin/phpunit
```

---

## Changelog

### [v2.1.0] - 2026-07-20
#### Added Unit Tests
- **PHPUnit Integration**: Added PHPUnit configuration file (`phpunit.xml.dist`) and test suite file (`tests/DatatablesTest.php`).
- **Constructor Injection**: Added support for database and request mocks injection inside `Datatables` constructor.
- **Autoload Development Mapping**: Configured development PSR-4 namespace mapping in `composer.json`.

### [v2.0.0] - 2026-07-20
#### Migrated to CodeIgniter 4
- **Refactored to CodeIgniter 4**: Fully rewritten to support CodeIgniter 4's Query Builder (via connection and builder instances) and new HTTP request helper routines (`Services::request()`).
- **PHP 8.3+ Types**: Added modern PHP type definitions, return declarations, and strict comparisons.
- **Autoloading & PSR-4**: Configured namespaces and directory mapping to support PSR-4 auto-loading via composer namespace `Cahyadsn\Datatables\`.
- **Composer Package Support**: Added `composer.json` metadata to allow installation via `composer require`.
- **Fixed bugs in column unsetting**: Fixed target array index unsetting behavior to accurately map numeric indexes and custom column property labels when removing columns from final responses.


