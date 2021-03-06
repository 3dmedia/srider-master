<?php

/*
 * @category   Booking Software
 * @package    Srider Module
 * @author     Brinzaru Andrei-Dan <dan.brinzaru@gmail.com>
 * @copyright  Copyright (c) 2016 - Technicopro, Brinzaru Andrei-Dan
 * @version    1.0
 */

namespace Srider\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;


class ResourceTable extends AbstractTableGateway {

    public $table = 'resource';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $this->initialize();
    }

    public function getAllResources()
    {
        try {
            $sql = new Sql($this->getAdapter());
            $select = $sql->select()->from(array(
                'rs' => $this->table
            ));
            $select->columns(array(
                'id',
                'resource_name',
                'active'
            ));
            $statement = $sql->prepareStatementForSqlObject($select);
            $resources = $this->resultSetPrototype->initialize($statement->execute())
                ->toArray();
            return $resources;
        } catch (\Exception $e) {
            throw new \Exception($e->getPrevious()->getMessage());
        }
    }

}
