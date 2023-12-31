<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateWkNewsletterTable extends Migration
{
    public function up()
    {
        Schema::create(config('wk-core.table.newsletter.settings'), function (Blueprint $table) {
            $table->uuid('id');
            $table->nullableUuidMorphs('host');
            $table->string('serial')->nullable();
            $table->string('identifier')->nullable();
            $table->text('theme')->nullable();
            $table->string('smtp_host')->nullable();
            $table->string('smtp_port')->nullable();
            $table->string('smtp_encryption')->nullable();
            $table->string('smtp_username')->nullable();
            $table->string('smtp_password')->nullable();
            $table->boolean('is_enabled')->default(0);

            $table->timestampsTz();
            $table->softDeletes();

            $table->primary('id');
            $table->index('serial');
            $table->index('is_enabled');
            $table->index(['host_type', 'host_id', 'is_enabled']);
        });
        if (!config('wk-newsletter.onoff.core-lang_core')) {
            Schema::create(config('wk-core.table.newsletter.settings_lang'), function (Blueprint $table) {
                $table->uuid('id');
                $table->nullableUuidMorphs('morph');
                $table->uuid('user_id')->nullable();
                $table->string('code');
                $table->string('key');
                $table->longText('value')->nullable();
                $table->boolean('is_current')->default(1);

                $table->timestampsTz();
                $table->softDeletes();

                $table->foreign('user_id')->references('id')
                    ->on(config('wk-core.table.user'))
                    ->onDelete('set null')
                    ->onUpdate('cascade');

                $table->primary('id');
            });
        }

        Schema::create(config('wk-core.table.newsletter.articles'), function (Blueprint $table) {
            $table->uuid('id');
            $table->uuid('setting_id');
            $table->string('serial')->nullable();
            $table->text('style')->nullable();
            $table->text('subject');
            $table->longText('content');
            $table->boolean('is_enabled')->default(0);

            $table->timestampsTz();
            $table->softDeletes();

            $table->foreign('setting_id')->references('id')
                  ->on(config('wk-core.table.newsletter.settings'))
                  ->onDelete('cascade')
                  ->onUpdate('cascade');

            $table->index('serial');
            $table->index('is_enabled');

            $table->primary('id');
        });
    }

    public function down() {
        Schema::dropIfExists(config('wk-core.table.newsletter.articles'));
        Schema::dropIfExists(config('wk-core.table.newsletter.settings_lang'));
        Schema::dropIfExists(config('wk-core.table.newsletter.settings'));
    }
}
