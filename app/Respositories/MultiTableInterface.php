<?php 

namespace App\Respositories;

interface MultiTableInterface {
	public function find($id);
	public function create(array $data);
	public function insert(array $data);
	public function destroy($id);
	public function destroyCondict(array $where);
	public function updateCondict(array $where, array $data);
	public function update($id, array $data);
	public function getFirst(array $where);
	public function getCondiction(array $where = array(), array $fields = array('*'), $order = "id desc", array $join = array());
	public function getCount(array $where, array $join = array());
	public function getPaginate(array $where = array(), $fields = array("*"), $order = "id desc", $page = 10);
	public function getCondictionIn($col = 'id', array $arrVal = array(), array $where = array(), array $fields = array('*'), $order = "id desc", array $join = array());
}