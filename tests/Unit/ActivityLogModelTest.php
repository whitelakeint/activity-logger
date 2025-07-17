<?php

namespace ActivityLogger\Tests\Unit;

use ActivityLogger\Models\ActivityLog;
use ActivityLogger\Tests\TestCase;
use Carbon\Carbon;

class ActivityLogModelTest extends TestCase
{
    public function test_activity_log_can_be_created()
    {
        $log = ActivityLog::create([
            'user_id' => 1,
            'ip_address' => '127.0.0.1',
            'request_date' => Carbon::today()->toDateString(),
            'requested_at' => Carbon::now(),
            'method' => 'GET',
            'url' => 'https://example.com',
            'response_code' => 200,
            'response_time' => 150.5,
            'memory_usage' => 1024,
        ]);

        $this->assertInstanceOf(ActivityLog::class, $log);
        $this->assertEquals(1, $log->user_id);
        $this->assertEquals('127.0.0.1', $log->ip_address);
        $this->assertEquals('GET', $log->method);
        $this->assertEquals(200, $log->response_code);
    }

    public function test_is_successful_attribute()
    {
        $successLog = ActivityLog::make(['response_code' => 200]);
        $errorLog = ActivityLog::make(['response_code' => 500]);

        $this->assertTrue($successLog->is_successful);
        $this->assertFalse($errorLog->is_successful);
    }

    public function test_is_error_attribute()
    {
        $successLog = ActivityLog::make(['response_code' => 200]);
        $errorLog = ActivityLog::make(['response_code' => 500]);
        $errorLogWithMessage = ActivityLog::make(['response_code' => 200, 'error_message' => 'Some error']);

        $this->assertFalse($successLog->is_error);
        $this->assertTrue($errorLog->is_error);
        $this->assertTrue($errorLogWithMessage->is_error);
    }

    public function test_formatted_duration_attribute()
    {
        $fastLog = ActivityLog::make(['response_time' => 150.5]);
        $slowLog = ActivityLog::make(['response_time' => 1500.0]);

        $this->assertEquals('150.5ms', $fastLog->formatted_duration);
        $this->assertEquals('1.5s', $slowLog->formatted_duration);
    }

    public function test_formatted_memory_usage_attribute()
    {
        $smallLog = ActivityLog::make(['memory_usage' => 1024]); // 1KB
        $largeLog = ActivityLog::make(['memory_usage' => 1048576]); // 1MB

        $this->assertEquals('1.00KB', $smallLog->formatted_memory_usage);
        $this->assertEquals('1.00MB', $largeLog->formatted_memory_usage);
    }

    public function test_scope_for_user()
    {
        ActivityLog::create([
            'user_id' => 1,
            'ip_address' => '127.0.0.1',
            'request_date' => Carbon::today()->toDateString(),
            'requested_at' => Carbon::now(),
            'method' => 'GET',
            'url' => 'https://example.com',
            'response_code' => 200,
            'response_time' => 150.5,
            'memory_usage' => 1024,
        ]);

        ActivityLog::create([
            'user_id' => 2,
            'ip_address' => '127.0.0.1',
            'request_date' => Carbon::today()->toDateString(),
            'requested_at' => Carbon::now(),
            'method' => 'POST',
            'url' => 'https://example.com',
            'response_code' => 201,
            'response_time' => 200.0,
            'memory_usage' => 2048,
        ]);

        $user1Logs = ActivityLog::forUser(1)->get();
        $user2Logs = ActivityLog::forUser(2)->get();

        $this->assertCount(1, $user1Logs);
        $this->assertCount(1, $user2Logs);
        $this->assertEquals(1, $user1Logs->first()->user_id);
        $this->assertEquals(2, $user2Logs->first()->user_id);
    }
}