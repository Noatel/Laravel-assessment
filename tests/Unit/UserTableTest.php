<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class UserTableTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test if the users table has the expected columns.
     *
     * @return void
     */
    public function test_columns_table_users()
    {
        // Arrangements
        $expectedColumns = [
            'id',
            'name',
            'email',
            'email_verified_at',
            'password',
            'remember_token',
            'created_at',
            'updated_at',
            'prefixname',
            'middlename',
            'lastname',
            'suffixname',
            'username',
            'photo',
            'type',
        ];

        // Act

        // Assert
        $this->assertTrue(Schema::hasTable('users'));
        
        foreach ($expectedColumns as $column) {
            $this->assertTrue(Schema::hasColumn('users', $column));
        }
    }
}
