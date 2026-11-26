<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Durable "a job was queued for this contact" marker.
     *
     * NULL  = no job has been queued yet (the dispatch failed or never happened) -> recovery may queue it.
     * set   = when the last job was queued -> recovery leaves it alone until that job is old enough to
     *         be presumed lost, so a stopped worker cannot cause an ever-growing pile of duplicate jobs.
     */
    public function up(): void
    {
        Schema::table('contact_mails', function (Blueprint $table) {
            $table->timestamp('queued_at')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('contact_mails', function (Blueprint $table) {
            $table->dropColumn('queued_at');
        });
    }
};
