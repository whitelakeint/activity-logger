<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('session_id')->nullable()->index();
            $table->string('ip_address', 45)->index();
            $table->date('request_date');
            $table->timestamp('requested_at');
            $table->string('user_agent')->nullable();
            $table->string('method', 10)->index();
            $table->text('url');
            $table->string('referer', 2048)->nullable();
            $table->string('route_name')->nullable()->index();
            $table->json('request_headers')->nullable();
            $table->json('request_params')->nullable();
            $table->json('request_body')->nullable();
            $table->integer('response_code')->index();
            $table->json('response_headers')->nullable();
            $table->decimal('response_time', 8, 3); // in milliseconds
            $table->json('response_body')->nullable();
            $table->integer('response_size')->nullable(); // in bytes
            $table->text('error_message')->nullable();
            $table->text('error_trace')->nullable();
            $table->string('error_type')->nullable()->index();

            // Additional Context
            $table->string('route_name')->nullable();
            $table->string('controller_action')->nullable();
            $table->json('middleware')->nullable();
            $table->string('request_id', 100)->nullable(); // For tracking related requests

            // Performance Metrics
            $table->integer('memory_usage')->nullable(); // in bytes
            $table->integer('query_count')->nullable();
            $table->decimal('query_time', 8, 3)->nullable(); // in milliseconds

            // Geographical Information
            $table->string('country', 2)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('timezone', 50)->nullable();

            $table->decimal('duration', 10, 3)->nullable();
            $table->decimal('cpu_usage', 5, 2)->nullable();
            $table->string('browser')->nullable();
            $table->string('platform')->nullable();
            $table->string('device')->nullable();
            $table->string('referer')->nullable();
            $table->boolean('is_ajax')->default(false);
            $table->boolean('is_mobile')->default(false);
            $table->json('custom_data')->nullable();
            $table->timestamps();
            $table->index(['created_at', 'method']);
            $table->index(['created_at', 'response_code']);
            $table->index(['created_at', 'user_id']);

            $table->index('user_id');
            $table->index('ip_address');
            $table->index('requested_at');
            $table->index('request_date');
            $table->index('method');
            $table->index('response_code');
            $table->index('route_name');
            $table->index(['requested_at', 'url']);
            $table->index(['user_id', 'requested_at']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('activity_logs');
    }
};