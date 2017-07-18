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
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class RolePermissions extends AbstractTableGateway
{

    public $table = 'role_permission';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $this->initialize();
    }

    public function getRolePermissions()
    {
        $sql = new Sql($this->getAdapter());
        
        $select = $sql->select()
            ->from(array(
            't1' => 'role'
        ))
            ->columns(array(
            'role_name'
        ))
            ->join(array(
            't2' => $this->table
        ), 't1.id = t2.role_id', array(), 'left')
            ->join(array(
            't3' => 'permission'
        ), 't3.id = t2.permission_id', array(
            'permission_name'
        ), 'left')
            ->join(array(
            't4' => 'resource'
        ), 't4.id = t3.resource_id', array(
            'resource_name'
        ), 'left')
            ->where('t3.permission_name is not null and t4.resource_name is not null')
            ->order('t1.id');
        
        $statement = $sql->prepareStatementForSqlObject($select);
        $result = $this->resultSetPrototype->initialize($statement->execute())
            ->toArray();
//        echo '<pre>'; print_r($result); die(';;;');
        return $result;
    }
}