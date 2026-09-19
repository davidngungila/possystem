<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('support_officers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone');
            $table->string('group_name')->nullable();
            $table->string('designation')->nullable();
            $table->boolean('is_online')->default(true);
            $table->integer('order_index')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        $seed = [
            ['name' => 'Undergraduate Studies', 'phone' => '0752811050', 'group_name' => 'Undergraduate Studies', 'order_index' => 0],
            ['name' => 'Support Team', 'phone' => '0734986490', 'group_name' => 'Support Team', 'order_index' => 1],
            ['name' => 'Support Team', 'phone' => '0734986486', 'group_name' => 'Support Team', 'order_index' => 2],
            ['name' => 'Support Team', 'phone' => '0752994657', 'group_name' => 'Support Team', 'order_index' => 3],
            ['name' => 'Support Team', 'phone' => '0752550837', 'group_name' => 'Support Team', 'order_index' => 4],
            ['name' => 'Postgraduate Studies', 'phone' => '0683936599', 'group_name' => 'Postgraduate Studies', 'order_index' => 5],
            ['name' => 'TIZO MAVUNGE', 'phone' => '0715622688', 'group_name' => 'Postgraduate Studies', 'order_index' => 6],
            ['name' => 'ASWILA', 'phone' => '0753000000', 'group_name' => 'Postgraduate Studies', 'designation' => 'Admissions Assistant', 'order_index' => 7],
        ];

        foreach ($seed as $row) {
            DB::table('support_officers')->insert(array_merge($row, ['created_at' => now(), 'updated_at' => now()]));
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('support_officers');
    }
};