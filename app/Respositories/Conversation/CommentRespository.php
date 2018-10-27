<?php 

namespace App\Respositories\Conversation;

use App\Respositories\BaseMultiTableModel;

class CommentRespository extends BaseMultiTableModel
{
	protected $prefix = 'comment';
	protected $quantity = 100;

	public function __construct(int $dividend)
	{
		$this->setTable($dividend);
	}
}