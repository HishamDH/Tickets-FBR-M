<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Spatie\Permission\Models\Role;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    
    /**
     * Setup the test environment.
     */
    protected function setUp(): void
    {
        parent::setUp();
        
        // Create required roles for tests
        $this->createRoles();
    }
    
    /**
     * Create the roles required for tests
     */
    protected function createRoles(): void
    {
        if (!Role::where('name', 'Customer')->exists()) {
            Role::create(['name' => 'Customer', 'guard_name' => 'web']);
        }
        
        // Other common roles
        $roles = ['Admin', 'Merchant', 'Partner'];
        foreach ($roles as $role) {
            if (!Role::where('name', $role)->exists()) {
                Role::create(['name' => $role, 'guard_name' => 'web']);
            }
        }
    }
}
