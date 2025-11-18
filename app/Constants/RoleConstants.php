<?php

namespace App\Constants;

class RoleConstants
{
    // System role names
    const SUPERADMIN = 'superadmin';
    const ADMIN = 'admin';
    const SALESMAN = 'salesman';
    const CUSTOMER = 'customer';
    const SUPPLIER = 'supplier';

    // All system roles that cannot be deleted
    const PROTECTED_ROLES = [
        self::SUPERADMIN,
        self::ADMIN,
        self::SALESMAN,
        self::CUSTOMER,
        self::SUPPLIER,
    ];

    // Role display names
    const ROLE_DISPLAY_NAMES = [
        self::SUPERADMIN => 'Super Admin',
        self::ADMIN => 'Admin',
        self::SALESMAN => 'Salesman',
        self::CUSTOMER => 'Customer',
        self::SUPPLIER => 'Supplier',
    ];

    /**
     * Check if a role name is protected
     */
    public static function isProtected(string $roleName): bool
    {
        return in_array($roleName, self::PROTECTED_ROLES);
    }

    /**
     * Get display name for a role
     */
    public static function getDisplayName(string $roleName): string
    {
        return self::ROLE_DISPLAY_NAMES[$roleName] ?? ucfirst($roleName);
    }

    /**
     * Get all role names
     */
    public static function getAllRoleNames(): array
    {
        return [
            self::SUPERADMIN,
            self::ADMIN,
            self::SALESMAN,
            self::CUSTOMER,
            self::SUPPLIER,
        ];
    }
}
