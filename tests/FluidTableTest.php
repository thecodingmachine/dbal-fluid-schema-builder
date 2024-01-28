<?php

namespace TheCodingMachine\FluidSchema;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use PHPUnit\Framework\TestCase;

class FluidTableTest extends TestCase
{
    public function testColumn()
    {
        $schema = new Schema();
        $fluid = new FluidSchema($schema);

        $posts = $fluid->table('posts');

        $column = $posts->column('foo');

        $this->assertTrue($schema->getTable('posts')->hasColumn('foo'));

        $this->assertSame($column, $posts->column('foo'), 'Failed asserting that the same instance is returned.');
    }

    public function testExistingColumn()
    {
        $schema = new Schema();
        $postsSchemaTable = $schema->createTable('posts');
        $postsSchemaTable->addColumn('foo', 'string');
        $fluid = new FluidSchema($schema);

        $posts = $fluid->table('posts');

        $posts->column('foo')->integer();

        $this->assertSame(Type::getType(Types::INTEGER), $schema->getTable('posts')->getColumn('foo')->getType());
    }

    public function testIndex()
    {
        $schema = new Schema();
        $fluid = new FluidSchema($schema);

        $posts = $fluid->table('posts');

        $posts->column('foo')->integer()->then()->index(['foo']);

        $this->assertCount(1, $schema->getTable('posts')->getIndexes());
    }

    public function testUnique()
    {
        $schema = new Schema();
        $fluid = new FluidSchema($schema);

        $posts = $fluid->table('posts');

        $posts->column('foo')->integer()->then()->unique(['foo']);

        $this->assertCount(1, $schema->getTable('posts')->getIndexes());
    }

    public function testPrimaryKey()
    {
        $schema = new Schema();
        $fluid = new FluidSchema($schema);

        $posts = $fluid->table('posts');

        $posts->column('id')->integer()->then()->primaryKey(['id'], 'pkname');

        $this->assertTrue($schema->getTable('posts')->hasPrimaryKey());
        $this->assertTrue($schema->getTable('posts')->hasIndex('pkname'));
    }

    public function testId()
    {
        $schema = new Schema();
        $fluid = new FluidSchema($schema);

        $posts = $fluid->table('posts');

        $posts->id();

        $this->assertNotNull($schema->getTable('posts')->getPrimaryKey());
        $this->assertTrue($schema->getTable('posts')->hasColumn('id'));
    }

    public function testUuid()
    {
        $schema = new Schema();
        $fluid = new FluidSchema($schema);

        $posts = $fluid->table('posts');

        $posts->uuid();

        $this->assertNotNull($schema->getTable('posts')->getPrimaryKey());
        $this->assertTrue($schema->getTable('posts')->hasColumn('uuid'));
    }

    public function testTimestamps()
    {
        $schema = new Schema();
        $fluid = new FluidSchema($schema);

        $posts = $fluid->table('posts');

        $posts->timestamps();

        $this->assertTrue($schema->getTable('posts')->hasColumn('created_at'));
        $this->assertTrue($schema->getTable('posts')->hasColumn('updated_at'));
    }

    public function testInherits()
    {
        $schema = new Schema();
        $fluid = new FluidSchema($schema);

        $contacts = $fluid->table('contacts');
        $contacts->id();

        $fluid->table('users')->extends('contacts');

        $dbalColumn = $schema->getTable('users')->getColumn('id');

        $this->assertSame(Type::getType(Types::INTEGER), $dbalColumn->getType());
        $fks = $schema->getTable('users')->getForeignKeys();
        $this->assertCount(1, $fks);
        $fk = array_pop($fks);
        $this->assertSame('users', $fk->getLocalTableName());
        $this->assertSame('contacts', $fk->getForeignTableName());
        $this->assertSame(['id'], $fk->getLocalColumns());
    }

    public function testCannotInheritFromATableWithMultiplePrimaryKeys()
    {
        $schema = new Schema();
        $fluid = new FluidSchema($schema);

        $contacts = $fluid->table('contacts');
        $contacts->column('foo')->string();
        $contacts->column('bar')->string();
        $contacts->primaryKey(['foo', 'bar']);

        $this->expectException(FluidSchemaException::class);

        $fluid->table('users')->extends('contacts');
    }

    public function testGetDbalTable()
    {
        $schema = new Schema();
        $fluid = new FluidSchema($schema);

        $contacts = $fluid->table('contacts');
        $this->assertSame('contacts', $contacts->getDbalTable()->getName());
    }
}
