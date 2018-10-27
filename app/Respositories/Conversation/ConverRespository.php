<?php 

namespace App\Respositories\Conversation;

use App\Respositories\BaseMultiTableModel;

class ConverRespository extends BaseMultiTableModel
{
	protected $prefix = 'conver';
	protected $quantity = 100;

	public function __construct(int $dividend)
	{
		$this->setTable($dividend);
	}
}