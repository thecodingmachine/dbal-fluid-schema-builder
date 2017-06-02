<?php

namespace TheCodingMachine\FluidSchema;

use PHPUnit\Framework\TestCase;

class DefaultNamingStrategyTest extends TestCase
{
    public function testGetJointureTableName()
    {
        $strategy = new DefaultNamingStrategy();

        $this->assertSame('users_roles', $strategy->getJointureTableName('users', 'roles'));
    }

    public function testGetForeignKeyColumnName()
    {
        $strategy = new DefaultNamingStrategy();

        $this->assertSame('user_id', $strategy->getForeignKeyColumnName('user'));
    }
}
