<?php

use Illuminate\Database\Seeder;

class QuickAnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('quick_answers')->insert([
        	[
                'user_id' => 0,
    			'quick_text' => 'xinchao',
    			'answer_text' => 'Chào anh (chị)! Rất vui khi anh (chị) đã ghé thăm.'
    		],
    		[
                'user_id' => 0,
    			'quick_text' => 'camon',
    			'answer_text' => 'Cảm ơn anh (chị) đã ghé thăm!'
    		],
    		[
                'user_id' => 0,
    			'quick_text' => 'loainao',
    			'answer_text' => 'Anh (chị) cần mua loại sản phẩm nào ạ?'
    		],
        ]);
    }
}
