<?php

namespace Cahyadsn\Datatables\Tests;

use PHPUnit\Framework\TestCase;
use Cahyadsn\Datatables\Datatables;
use CodeIgniter\Database\BaseConnection;
use CodeIgniter\HTTP\IncomingRequest;

class DatatablesTest extends TestCase
{
    protected $dbMock;
    protected $requestMock;

    protected function setUp(): void
    {
        $this->dbMock = $this->createMock(BaseConnection::class);
        $this->requestMock = $this->createMock(IncomingRequest::class);
    }

    public function testSelectAndFrom()
    {
        $datatables = new Datatables($this->dbMock, $this->requestMock);
        
        $datatables->select('id, name as user_name, age')
                   ->from('users');

        // Test internal state reflection
        $reflector = new \ReflectionClass(Datatables::class);
        
        $columnsProperty = $reflector->getProperty('columns');
        $columnsProperty->setAccessible(true);
        $columns = $columnsProperty->getValue($datatables);

        $tableProperty = $reflector->getProperty('table');
        $tableProperty->setAccessible(true);
        $table = $tableProperty->getValue($datatables);

        $this->assertEquals(['id', 'user_name', 'age'], $columns);
        $this->assertEquals('users', $table);
    }

    public function testAddColumn()
    {
        $datatables = new Datatables($this->dbMock, $this->requestMock);
        
        $datatables->addColumn('action', '<a href="edit/$1">Edit</a>', 'id');

        $reflector = new \ReflectionClass(Datatables::class);
        $addColumnsProperty = $reflector->getProperty('add_columns');
        $addColumnsProperty->setAccessible(true);
        $addColumns = $addColumnsProperty->getValue($datatables);

        $this->assertArrayHasKey('action', $addColumns);
        $this->assertEquals('<a href="edit/$1">Edit</a>', $addColumns['action']['content']);
        $this->assertEquals(['id'], $addColumns['action']['replacement']);
    }

    public function testEditColumn()
    {
        $datatables = new Datatables($this->dbMock, $this->requestMock);
        
        $datatables->editColumn('status', '<strong>$1</strong>', 'status');

        $reflector = new \ReflectionClass(Datatables::class);
        $editColumnsProperty = $reflector->getProperty('edit_columns');
        $editColumnsProperty->setAccessible(true);
        $editColumns = $editColumnsProperty->getValue($datatables);

        $this->assertArrayHasKey('status', $editColumns);
        $this->assertEquals('<strong>$1</strong>', $editColumns['status'][0]['content']);
        $this->assertEquals(['status'], $editColumns['status'][0]['replacement']);
    }

    public function testUnsetColumn()
    {
        $datatables = new Datatables($this->dbMock, $this->requestMock);
        
        $datatables->unsetColumn('password');

        $reflector = new \ReflectionClass(Datatables::class);
        $unsetColumnsProperty = $reflector->getProperty('unset_columns');
        $unsetColumnsProperty->setAccessible(true);
        $unsetColumns = $unsetColumnsProperty->getValue($datatables);

        $this->assertContains('password', $unsetColumns);
    }

    public function testExplodeHelper()
    {
        $datatables = new Datatables($this->dbMock, $this->requestMock);
        
        $reflector = new \ReflectionClass(Datatables::class);
        $method = $reflector->getMethod('explode');
        $method->setAccessible(true);

        // Standard explode
        $result = $method->invokeArgs($datatables, [',', 'id, name, age']);
        $this->assertEquals(['id', ' name', ' age'], $result);

        // Explode ignoring parentheses
        $result = $method->invokeArgs($datatables, [',', 'id, CONCAT(first_name, " ", last_name) as full_name, email']);
        $this->assertEquals(['id', ' CONCAT(first_name, " ", last_name) as full_name', ' email'], $result);
    }

    public function testBalanceCharsHelper()
    {
        $datatables = new Datatables($this->dbMock, $this->requestMock);
        
        $reflector = new \ReflectionClass(Datatables::class);
        $method = $reflector->getMethod('balanceChars');
        $method->setAccessible(true);

        $this->assertEquals(0, $method->invokeArgs($datatables, ['CONCAT(a, b)', '(', ')']));
        $this->assertEquals(1, $method->invokeArgs($datatables, ['CONCAT(a, b', '(', ')']));
        $this->assertEquals(-1, $method->invokeArgs($datatables, ['CONCAT a, b)', '(', ')']));
    }
}
