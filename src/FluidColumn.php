<?php


namespace TheCodingMachine\FluidSchema;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Schema\Table;
use Doctrine\DBAL\Types\Type;

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
     * FluidColumn constructor.
     * @param FluidSchema $fluidSchema
     * @param FluidTable $fluidTable
     * @param Table $table
     * @param Column $column
     */
    public function __construct(FluidSchema $fluidSchema, FluidTable $fluidTable, Table $table, Column $column)
    {
        $this->fluidSchema = $fluidSchema;
        $this->fluidTable = $fluidTable;
        $this->column = $column;
        $this->table = $table;
    }

    public function integer(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::INTEGER));
        return $this->getOptions();
    }

    public function smallInt(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::SMALLINT));
        return $this->getOptions();
    }

    public function bigInt(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::BIGINT));
        return $this->getOptions();
    }

    public function decimal(int $precision = 10, int $scale = 0): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::DECIMAL));
        $this->column->setPrecision($precision);
        $this->column->setScale($scale);
        return $this->getOptions();
    }

    public function float(int $precision = 10, int $scale = 0): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::FLOAT));
        $this->column->setPrecision($precision);
        $this->column->setScale($scale);
        return $this->getOptions();
    }

    public function string(?string $length = null, bool $fixed = false): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::STRING));
        $this->column->setLength($length);
        $this->column->setFixed($fixed);
        return $this->getOptions();
    }

    public function text(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::TEXT));
        return $this->getOptions();
    }

    public function guid(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::GUID));
        return $this->getOptions();
    }

    /**
     * From Doctrine DBAL 2.4+.
     *
     * @param null|string $length
     * @param bool $fixed
     * @return FluidColumnOptions
     */
    public function binary(?string $length = null, bool $fixed = false): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::BINARY));
        $this->column->setLength($length);
        $this->column->setFixed($fixed);
        return $this->getOptions();
    }

    public function blob(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::BLOB));
        return $this->getOptions();
    }

    public function boolean(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::BOOLEAN));
        return $this->getOptions();
    }

    public function date(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::DATE));
        return $this->getOptions();
    }

    public function dateImmutable(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::DATE_IMMUTABLE));
        return $this->getOptions();
    }

    public function datetime(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::DATETIME));
        return $this->getOptions();
    }

    public function datetimeImmutable(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::DATETIME_IMMUTABLE));
        return $this->getOptions();
    }

    public function datetimeTz(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::DATETIMETZ));
        return $this->getOptions();
    }

    public function datetimeTzImmutable(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::DATETIMETZ_IMMUTABLE));
        return $this->getOptions();
    }

    public function time(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::TIME));
        return $this->getOptions();
    }

    public function timeImmutable(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::TIME_IMMUTABLE));
        return $this->getOptions();
    }

    public function dateInterval(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::DATEINTERVAL));
        return $this->getOptions();
    }

    public function array(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::TARRAY));
        return $this->getOptions();
    }

    public function simpleArray(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::SIMPLE_ARRAY));
        return $this->getOptions();
    }

    public function json(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::JSON));
        return $this->getOptions();
    }

    /**
     * @deprecated From DBAL 2.6, use json() instead.
     * @return FluidColumnOptions
     */
    public function jsonArray(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::JSON_ARRAY));
        return $this->getOptions();
    }

    public function object(): FluidColumnOptions
    {
        $this->column->setType(Type::getType(Type::OBJECT));
        return $this->getOptions();
    }

    public function references(string $tableName, ?string $constraintName = null, string $onUpdate = 'RESTRICT', string $onDelete = 'RESTRICT'): FluidColumnOptions
    {
        $table = $this->fluidSchema->getDbalSchema()->getTable($tableName);

        $referencedColumns = $table->getPrimaryKeyColumns();

        if (count($referencedColumns) > 1) {
            throw new FluidSchemaException('You cannot reference a table with a primary key on several columns using FluidSchema. Use DBAL Schema methods instead.');
        }

        $referencedColumnName = $referencedColumns[0];
        $referencedColumn = $table->getColumn($referencedColumnName);

        $this->column->setType($referencedColumn->getType());
        $this->column->setLength($referencedColumn->getLength());
        $this->column->setFixed($referencedColumn->getFixed());
        $this->column->setScale($referencedColumn->getScale());
        $this->column->setPrecision($referencedColumn->getPrecision());
        $this->column->setUnsigned($referencedColumn->getUnsigned());

        $this->table->addForeignKeyConstraint($table, [$this->column->getName()], $referencedColumns, [
            'onUpdate' => $onUpdate,
            'onDelete' => $onDelete
        ], $constraintName);
        return $this->getOptions();
    }

    private function getOptions(): FluidColumnOptions
    {
        return new FluidColumnOptions($this->fluidTable, $this->column);
    }
}
