<?php 

namespace App\Respositories\Conversation;

use App\Respositories\BaseMultiTableModel;

class AttachRespository extends BaseMultiTableModel
{
	protected $prefix = 'attachment';
	protected $quantity = 100;

	public function __construct(int $dividend)
	{
		$this->setTable($dividend);
	}
}