<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("
            create table users
            (
                id int auto_increment,
                name varchar(64) not null,
                email varchar(256) not null,
                created DATETIME not null,
                deleted DATETIME null,
                notes TEXT null,
                constraint users_pk
                    primary key (id)
            );
        ");
        DB::statement("
            create unique index users_email_uindex
                on users (email);
        ");
        DB::statement("
            create unique index users_name_uindex
                on users (name);
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
