<?php 

namespace App\Respositories;

use App\Traits\ModelTrait;
use Illuminate\Support\Facades\DB;

class BaseMultiTableModel implements MultiTableInterface
{
	use ModelTrait;

	protected $prefix;
	protected $quantity;
	protected $table;

	public function setTable(int $dividend)
	{
		$tb = $this->getNameTable($dividend, $this->prefix, $this->quantity);
		$this->table = $tb;
	}

	public function getDB()
	{
		return DB::table($this->table);
	}

	public function find($id)
	{
		return $this->getDB()->where('id', $id)->first();
	}

	public function create(array $data)
	{
		$id = $this->getDB()->insertGetId($data);
		return $id;
	}

	public function insert(array $data)
	{
		$res = $this->getDB()->insert($data);
		return $res;
	}

	public function destroy($id)
	{
		$res = optional($this->getDB()->find($id))->delete();
		return $res;
	}

	public function destroyCondict(array $where)
	{
		$res = $this->getDB()->where($where)->delete();
		return $res;
	}

	public function update($id, array $data)
	{
		$res = $this->getDB()->where('id', $id)->update($data);
		return $res;
	}

	public function updateCondict(array $where, array $data)
	{
		$res = $this->getDB()->where($where)->update($data);
		return $res;
	}

	public function getFirst(array $where)
	{
		$res = $this->getDB()->where($where)->first();
		return $res;
	}

	public function getCondiction(array $where = array(), array $fields = array('*'), $order = "id", array $join = array())
    {
        $model = $this->getDB()->select($fields);
        if (!empty($join)) {
            for ($i = 0; $i < count($join); $i++) {
                $model->join($join[$i]['table'], $join[$i]['first'], $join[$i]['operator'], $join[$i]['second']);
            }
        }
        $result = $model->where($where)->orderByRaw(DB::raw($order))->get();
        return $result;
    }

    public function getCount(array $where, array $join = array())
    {
    	$model = $this->getDB();
        if (!empty($join)) {
            for ($i = 0; $i < count($join); $i++) {
                $model->join($join[$i]['table'], $join[$i]['first'], $join[$i]['operator'], $join[$i]['second']);
            }
        }
        $result = $model->where($where)->count();
        return $result;
    }

    public function getPaginate(array $where = array(), $fields = array("*"), $order = "id desc", $page = 10)
    {
    	$result = $this->getDB()->select($fields)->where($where)->orderByRaw(DB::raw($order))->paginate($page);
        return $result;
    }

    public function getCondictionIn($col = 'id', array $arrVal = array(), array $where = array(), array $fields = array('*'), $order = "id desc", array $join = array())
    {
    	$model = $this->getDB()->select($fields);
        if (!empty($join)) {
            for ($i = 0; $i < count($join); $i++) {
                $model->join($join[$i]['table'], $join[$i]['first'], $join[$i]['operator'], $join[$i]['second']);
            }
        }
        $result = $model->where($where)->whereIn($col, $arrVal)->orderByRaw(DB::raw($order))->get();
        return $result;
    }
}