<?php 

namespace App\Respositories\Conversation;

use App\Respositories\BaseMultiTableModel;

class MessageRespository extends BaseMultiTableModel
{
	protected $prefix = 'message';
	protected $quantity = 100;

	public function __construct(int $dividend)
	{
		$this->setTable($dividend);
	}
}