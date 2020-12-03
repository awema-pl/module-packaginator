
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
            $table->string('name');
            $table->timestamps();
        });

        Schema::table(config('packaginator.database.tables.packaginator_histories'), function (Blueprint $table) {
            $table->foreignId('user_id')
                ->nullable()
                ->constrained(config('packaginator.database.tables.users'))
                ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table(config('packaginator.database.tables.packaginator_histories'), function (Blueprint $table) {
            $table->dropForeign(['user_id']);
        });
        Schema::drop(config('packaginator.database.tables.packaginator_histories'));
    }
}
