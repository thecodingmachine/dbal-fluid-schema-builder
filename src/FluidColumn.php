<?php


namespace TheCodingMachine\FluidSchema;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;
use Doctrine\DBAL\Types\Types;

class FluidColumn
{
    /**
     * @var FluidSchema
     */
    private $fluidSchema;
    /**
     * @var FluidTable
     */
    private $fluidTable;
    /**
     * @var Column
     */
    private $column;
    /**
     * @var Table
     */
    private $table;
    /**
     * @var NamingStrategyInterface
     */
    private $namingStrategy;

    /**
     * FluidColumn constructor.
     * @param FluidSchema $fluidSchema
     * @param FluidTable $fluidTable
     * @param Table $table
     * @param Column $column
     */
    public function __construct(FluidSchema $fluidSchema, FluidTable $fluidTable, Table $table, Column $column, NamingStrategyInterface $namingStrategy)
    {
        $this->fluidSchema = $fluidSchema;
        $this->fluidTable = $fluidTable;
        $this->column = $column;
        $this->table = $table;
        $this->namingStrategy = $namingStrategy;
    }

    public function integer(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::INTEGER));
        return $this->getOptions();
    }

    public function smallInt(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::SMALLINT));
        return $this->getOptions();
    }

    public function bigInt(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::BIGINT));
        return $this->getOptions();
    }

    public function decimal(int $precision = 10, int $scale = 0): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::DECIMAL));
        $this->column->setPrecision($precision);
        $this->column->setScale($scale);
        return $this->getOptions();
    }

    public function float(int $precision = 10, int $scale = 0): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::FLOAT));
        $this->column->setPrecision($precision);
        $this->column->setScale($scale);
        return $this->getOptions();
    }

    public function string(?int $length = null, bool $fixed = false): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::STRING));
        $this->column->setLength($length);
        $this->column->setFixed($fixed);
        return $this->getOptions();
    }

    public function text(?int $length = null): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::TEXT));
        $this->column->setLength($length);
        return $this->getOptions();
    }

    public function guid(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::GUID));
        return $this->getOptions();
    }

    /**
     * From Doctrine DBAL 2.4+.
     */
    public function binary(?int $length = null, bool $fixed = false): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::BINARY));
        $this->column->setLength($length);
        $this->column->setFixed($fixed);
        return $this->getOptions();
    }

    public function blob(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::BLOB));
        return $this->getOptions();
    }

    public function boolean(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::BOOLEAN));
        return $this->getOptions();
    }

    public function date(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::DATE_MUTABLE));
        return $this->getOptions();
    }

    public function dateImmutable(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::DATE_IMMUTABLE));
        return $this->getOptions();
    }

    public function datetime(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::DATETIME_MUTABLE));
        return $this->getOptions();
    }

    public function datetimeImmutable(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::DATETIME_IMMUTABLE));
        return $this->getOptions();
    }

    public function datetimeTz(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::DATETIMETZ_MUTABLE));
        return $this->getOptions();
    }

    public function datetimeTzImmutable(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::DATETIMETZ_IMMUTABLE));
        return $this->getOptions();
    }

    public function time(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::TIME_MUTABLE));
        return $this->getOptions();
    }

    public function timeImmutable(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::TIME_IMMUTABLE));
        return $this->getOptions();
    }

    public function dateInterval(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::DATEINTERVAL));
        return $this->getOptions();
    }

    /**
     * @deprecated Use json() instead
     */
    public function array(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::ARRAY));
        return $this->getOptions();
    }

    public function simpleArray(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::SIMPLE_ARRAY));
        return $this->getOptions();
    }

    public function json(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::JSON));
        return $this->getOptions();
    }

    /**
     * @deprecated From DBAL 2.6, use json() instead.
     * @return FluidColumnOptions
     */
    public function jsonArray(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::JSON));
        return $this->getOptions();
    }

    /**
     * @deprecated Use json() instead
     */
    public function object(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Types::OBJECT));
        return $this->getOptions();
    }

    public function references(string $tableName, ?string $constraintName = null, string $onUpdate = 'RESTRICT', string $onDelete = 'RESTRICT'): FluidColumnOptions
    {
        $tableName = $this->namingStrategy->quoteIdentifier($tableName);

        $table = $this->fluidSchema->getDbalSchema()->getTable($tableName);

        $referencedColumns = $table->getPrimaryKey()->getColumns();

        if (count($referencedColumns) > 1) {
            throw new FluidSchemaException('You cannot reference a table with a primary key on several columns using FluidSchema. Use DBAL Schema methods instead.');
        }

        $referencedColumnName = $this->namingStrategy->quoteIdentifier($referencedColumns[0]);
        $referencedColumn = $table->getColumn($referencedColumnName);

        $this->column->setType($referencedColumn->getType());
        $this->column->setLength($referencedColumn->getLength());
        $this->column->setFixed($referencedColumn->getFixed());
        $this->column->setScale($referencedColumn->getScale());
        $this->column->setPrecision($referencedColumn->getPrecision());
        $this->column->setUnsigned($referencedColumn->getUnsigned());

        $this->table->addForeignKeyConstraint($table, [$this->namingStrategy->quoteIdentifier($this->column->getName())], $referencedColumns, [
            'onUpdate' => $onUpdate,
            'onDelete' => $onDelete
        ], $constraintName);
        return $this->getOptions();
    }

    private function getOptions(): FluidColumnOptions
    {
        return new FluidColumnOptions($this->fluidTable, $this->column, $this->namingStrategy);
    }

    /**
     * Returns the underlying DBAL column.
     * @return Column
     */
    public function getDbalColumn(): Column
    {
        return $this->column;
    }
}
