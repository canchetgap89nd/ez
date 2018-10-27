<?php

use Illuminate\Database\Seeder;

class NamespaceSlugSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('intro_slugs')->insert([
        	[
        		'namespace' => 'blog/category',
        		'type' => 1
        	],
        	[
        		'namespace' => 'blog',
        		'type' => 2
        	]
        ]);
    }
}
