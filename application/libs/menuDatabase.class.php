<?php
namespace application\libs;
use \PDO;
use core\libs\Database as Db;
/**
*
*
*/
defined('ACCESS') || Error::exitApp();

class MenuDatabase extends Db{

	public function fetchDetails($menuId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchMenuDetails(:menuId)');
		$st->bindValue(':menuId', $menuId, PDO::PARAM_INT);
		return $st->execute() ? $st->fetch(PDO::FETCH_OBJ) : false;
	}

	public function fetchType($typeId)
	{
		# code...
		$st = $this->_driver->prepare('CALL sp_fetchMenuType(:typeId)');
		$st->bindValue(':typeId', $typeId, PDO::PARAM_INT);
		$st->execute();
		$st->bindColumn('name', $name);
		$st->fetch(PDO::FETCH_ASSOC);
		return $name;

	}

	public function fetchTypes()
	{
		# code...
		$st = $this->_driver->prepare('select * from menuTypes');
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();

	}

	public function fetchAll()
	{
		$st = $this->_driver->prepare('select * from `menu`');
		return $st->execute() ? $st->fetchAll(PDO::FETCH_OBJ) : array();
	}


	public function addNew(Array $data)
    {
        # code...
        $st = $this->_driver->prepare('insert into `menu` (name, typeId, price, reductions ) values ( :name, :typeId, :price, :reductions)');
        foreach ($data as $key => $value) {
            $st->bindValue(':'.$key, $value);
        }
        $st->execute();
    }


    public function checkIfAlreadyExist($menuName)
	{
		# code...
		$st = $this->_driver->prepare('select `id` from `menu` where `name` = :name');
		$st->bindValue(':name', $menuName, PDO::PARAM_STR);
		$st->execute();
		$st->bindColumn('id', $id);
		$st->fetch(PDO::FETCH_ASSOC);
		return (is_null($id) || $id === false) ? false : true;
	}


	public function updateDetail($detail, $value, $itemId)
	{
		# code...
		$query = 'update `menu` set `' . $detail . '` = :value where `id` = :id';
		$st = $this->_driver->prepare($query);
		$st->bindValue(':value', $value, PDO::PARAM_STR);
		$st->bindValue(':id', $itemId, PDO::PARAM_INT);
		return $st->execute() ? true : false;
	}



#end of class
}
