<?php

/*
 * @category   Booking Software
 * @package    Srider Module
 * @author     Brinzaru Andrei-Dan <dan.brinzaru@gmail.com>
 * @copyright  Copyright (c) 2016 - Technicopro, Brinzaru Andrei-Dan
 * @version    1.0
 */

namespace Application\Model;

use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;

class InsuranceTable extends AbstractTableGateway {

    public $table = 'insurance';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet(ResultSet::TYPE_ARRAY);
        $this->initialize();
    }

    public function getInsurances($where = array(), $columns = array()) {
        try {
            $sql = new Sql($this->getAdapter());
            $select = $sql->select()->from(array(
                'insurance' => $this->table
            ));

            if (count($where) > 0) {
                $select->where($where);
            }

            if (count($columns) > 0) {
                $select->columns($columns);
            }

//            $select->join(array('userRole' => 'user_role'), 'userRole.user_id = users.id', array('role_id'), 'LEFT');
//            $select->join(array('role' => 'role'), 'userRole.role_id = role.id', array('role_name'), 'LEFT');

            $statement = $sql->prepareStatementForSqlObject($select);
            $insurances = $this->resultSetPrototype->initialize($statement->execute())->toArray();
            echo '<pre>:::->';
            print_r($insurances);
            print_r($sql->getSqlstringForSqlObject($select));
            die('stop');
            return $insurances;
        } catch (\Exception $e) {
            throw new \Exception($e->getPrevious()->getMessage());
        }
    }

    public function getInsurance($id) {
        $id = (int) $id;
        $rowset = $this->tableGateway->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find insurance id: {$id}");
        }
        return $row;
    }

    public function saveInsurances(Insurance $insurance) {
        $data = array(
            'id' => $insurance->id,
            'name' => $insurance->role_id,
            'percent' => $insurance->permission_id
        );

        $lastId = null;
        $id = (int) $insurance->id;
        if ($id == 0) {
            $this->tableGateway->insert($data);
            $lastId = $this->tableGateway->lastInsertValue;
        } else {
            if ($this->getInsurance($id)) {
                $this->tableGateway->update($data, array('id' => $id));
                $lastId = $id;
            } else {
                throw new \Exception("Insurance id: {$id} does not exist.");
            }
        }

        return $lastId;
    }

    public function getAjaxInsurances($where = array(), $columns = array()) {
        try {
            $sql = new Sql($this->getAdapter());
            $select = $sql->select()->from(array(
                'insurance' => $this->table
            ));

            if (count($where) > 0) {
                $whereOr = new Where();
                $whereOr
                        ->nest()
                        ->like('id', '%' . $where['search'] . '%')
                        ->or
                        ->like('name', '%' . $where['search'] . '%')
                        ->or
                        ->like('percent', '%' . $where['search'] . '%')
                        ->unnest();
                $select->where($whereOr, $columns);
            }

            if (count($columns) > 0) {
                $select->columns($columns);
            }

            $statement = $sql->prepareStatementForSqlObject($select);
            $insurances = $this->resultSetPrototype->initialize($statement->execute())->toArray();

            return $insurances;
        } catch (\Exception $e) {
            throw new \Exception($e->getPrevious()->getMessage());
        }
    }

}
