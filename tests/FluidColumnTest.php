<?php

namespace TheCodingMachine\FluidSchema;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;
use PHPUnit\Framework\TestCase;

class FluidColumnTest extends TestCase
{
    public function testTypes()
    {
        $schema = new Schema();
        $fluid = new FluidSchema($schema);

        $posts = $fluid->table('posts');

        $column = $posts->column('foo');

        $dbalColumn = $schema->getTable('posts')->getColumn('foo');

        $column->integer();
        $this->assertSame(Type::getType(Types::INTEGER), $dbalColumn->getType());

        $column->smallInt();
        $this->assertSame(Type::getType(Types::SMALLINT), $dbalColumn->getType());

        $column->bigInt();
        $this->assertSame(Type::getType(Types::BIGINT), $dbalColumn->getType());

        $column->decimal(12, 32);
        $this->assertSame(Type::getType(Types::DECIMAL), $dbalColumn->getType());
        $this->assertSame(12, $dbalColumn->getPrecision());
        $this->assertSame(32, $dbalColumn->getScale());

        $column->float(32, 12);
        $this->assertSame(Type::getType(Types::FLOAT), $dbalColumn->getType());
        $this->assertSame(32, $dbalColumn->getPrecision());
        $this->assertSame(12, $dbalColumn->getScale());

        $column->string(42, true);
        $this->assertSame(Type::getType(Types::STRING), $dbalColumn->getType());
        $this->assertSame(42, $dbalColumn->getLength());
        $this->assertSame(true, $dbalColumn->getFixed());

        $column->text();
        $this->assertSame(Type::getType(Types::TEXT), $dbalColumn->getType());

        $column->guid();
        $this->assertSame(Type::getType(Types::GUID), $dbalColumn->getType());

        $column->blob();
        $this->assertSame(Type::getType(Types::BLOB), $dbalColumn->getType());

        $column->boolean();
        $this->assertSame(Type::getType(Types::BOOLEAN), $dbalColumn->getType());

        $column->date();
        $this->assertSame(Type::getType(Types::DATE_MUTABLE), $dbalColumn->getType());

        $column->datetime();
        $this->assertSame(Type::getType(Types::DATETIME_MUTABLE), $dbalColumn->getType());

        $column->datetimeTz();
        $this->assertSame(Type::getType(Types::DATETIMETZ_MUTABLE), $dbalColumn->getType());

        $column->time();
        $this->assertSame(Type::getType(Types::TIME_MUTABLE), $dbalColumn->getType());

        $column->array();
        $this->assertSame(Type::getType(Types::ARRAY), $dbalColumn->getType());

        $column->simpleArray();
        $this->assertSame(Type::getType(Types::SIMPLE_ARRAY), $dbalColumn->getType());

        $column->jsonArray();
        $this->assertSame(Type::getType(Types::JSON), $dbalColumn->getType());

        $column->object();
        $this->assertSame(Type::getType(Types::OBJECT), $dbalColumn->getType());

        if (defined('Doctrine\\DBAL\\Types\\Types::BINARY')) {
            $column->binary(43);
            $this->assertSame(Type::getType(Types::BINARY), $dbalColumn->getType());
            $this->assertSame(43, $dbalColumn->getLength());
            $this->assertSame(false, $dbalColumn->getFixed());
        }

        if (defined('Doctrine\\DBAL\\Types\\Types::DATE_IMMUTABLE')) {
            // Doctrine DBAL 2.6+
            $column->dateImmutable();
            $this->assertSame(Type::getType('date_immutable'), $dbalColumn->getType());

            $column->datetimeImmutable();
            $this->assertSame(Type::getType(Types::DATETIME_IMMUTABLE), $dbalColumn->getType());

            $column->datetimeTzImmutable();
            $this->assertSame(Type::getType(Types::DATETIMETZ_IMMUTABLE), $dbalColumn->getType());

            $column->time();
            $this->assertSame(Type::getType(Types::TIME_MUTABLE), $dbalColumn->getType());

            $column->timeImmutable();
            $this->assertSame(Type::getType(Types::TIME_IMMUTABLE), $dbalColumn->getType());

            $column->dateInterval();
            $this->assertSame(Type::getType(Types::DATEINTERVAL), $dbalColumn->getType());

            $column->json();
            $this->assertSame(Type::getType(Types::JSON), $dbalColumn->getType());
        }

        $this->assertSame('foo', $column->getDbalColumn()->getName());
    }

    public function testReference()
    {
        $schema = new Schema();
        $fluid = new FluidSchema($schema);

        $countries = $fluid->table('countries');
        $countries->id();

        $users = $fluid->table('users');
        $users->column('country_id')->references('countries', 'myfk');

        $dbalColumn = $schema->getTable('users')->getColumn('country_id');

        $this->assertSame(Type::getType(Types::INTEGER), $dbalColumn->getType());
        $fk = $schema->getTable('users')->getForeignKey('myfk');
        $this->assertSame('users', $fk->getLocalTableName());
        $this->assertSame('countries', $fk->getForeignTableName());
        $this->assertSame(['country_id'], $fk->getLocalColumns());
    }

    public function testReferenceException()
    {
        $schema = new Schema();
        $fluid = new FluidSchema($schema);

        $countries = $fluid->table('countries');
        $countries->column('id1')->integer();
        $countries->column('id2')->integer();
        $countries->primaryKey(['id1','id2']);

        $users = $fluid->table('users');
        $this->expectException(FluidSchemaException::class);
        $users->column('country_id')->references('countries', 'myfk');
    }
}
