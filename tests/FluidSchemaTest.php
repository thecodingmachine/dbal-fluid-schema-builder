<?php

namespace TheCodingMachine\FluidSchema;

use Doctrine\DBAL\Schema\Schema;
use PHPUnit\Framework\TestCase;

class FluidSchemaTest extends TestCase
{
    public function testTable()
    {
        $schema = new Schema();
        $fluid = new FluidSchema($schema);

        $posts = $fluid->table('posts');

        $this->assertTrue($schema->hasTable('posts'));

        $this->assertSame($posts, $fluid->table('posts'), 'Failed asserting that the same instance is returned.');
    }

    public function testExistingTable()
    {
        $schema = new Schema();
        $postsSchemaTable = $schema->createTable('posts');
        $fluid = new FluidSchema($schema);

        $posts = $fluid->table('posts');
        $posts->column('foo');

        $this->assertTrue($postsSchemaTable->hasColumn('foo'));
    }

    public function testJunctionTable()
    {
        $schema = new Schema();
        $db = new FluidSchema($schema);
        $db->table('users')->id();
        $db->table('roles')->id();
        $db->junctionTable('users', 'roles');

        $this->assertTrue($schema->hasTable('users_roles'));
        $this->assertCount(2, $schema->getTable('users_roles')->getColumns());
        $this->assertNotNull($schema->getTable('users_roles')->getColumn('user_id'));
        $this->assertNotNull($schema->getTable('users_roles')->getColumn('role_id'));
    }
}
