<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create(Config::get('mailog.tables.messages'), function (Blueprint $table) {
            $table->id();
            $table->dateTime('date')->nullable();
            $table->text('subject')->nullable();
            $table->longText('text')->nullable();
            $table->longText('html')->nullable();
            $table->text('path')->nullable();
            $table->timestamps();

            $table->index(['date', 'subject']);
            $table->index(['subject']);
        });

        Schema::create(Config::get('mailog.tables.message_addresses'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained(Config::get('mailog.tables.messages'))->onDelete('cascade');
            $table->string('type', 20);
            $table->string('address', 320);
            $table->string('domain', 255)->nullable();
            $table->text('name')->nullable();
            $table->timestamps();

            $table->index(['message_id', 'address', 'type'], 'address_idx');
            $table->index(['message_id', 'domain', 'type'], 'domain_idx');
            $table->index(['message_id', 'name', 'type'], 'name_idx');
        });

        Schema::create(Config::get('mailog.tables.message_attachments'), function (Blueprint $table) {
            $table->id();
            $table->foreignId('message_id')->constrained(Config::get('mailog.tables.messages'))->onDelete('cascade');
            $table->text('filename')->nullable();
            $table->integer('size');
            $table->text('path')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(Config::get('mailog.tables.messages'));
        Schema::dropIfExists(Config::get('mailog.tables.message_addresses'));
        Schema::dropIfExists(Config::get('mailog.tables.message_attachments'));
    }
};
