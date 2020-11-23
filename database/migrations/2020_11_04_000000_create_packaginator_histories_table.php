
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePackaginatorHistoriesTable extends Migration
{
    public function up()
    {
        Schema::create(config('packaginator.database.tables.packaginator_histories'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::drop(config('packaginator.database.tables.packaginator_histories'));
    }
}
