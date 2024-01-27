<?php


namespace TheCodingMachine\FluidSchema;

use Doctrine\DBAL\Schema\Table;

class FluidTable
{
    /**
     * @var FluidSchema
     */
    private $schema;
    /**
     * @var Table
     */
    private $table;
    /**
     * @var FluidColumn[]
     */
    private $fluidColumns;
    /**
     * @var NamingStrategyInterface
     */
    private $namingStrategy;

    /**
     * @param FluidSchema $schema
     * @param Table $table
     */
    public function __construct(FluidSchema $schema, Table $table, NamingStrategyInterface $namingStrategy)
    {
        $this->schema = $schema;
        $this->table = $table;
        $this->namingStrategy = $namingStrategy;
    }

    public function column(string $name): FluidColumn
    {
        $name = $this->namingStrategy->quoteIdentifier($name);

        if (isset($this->fluidColumns[$name])) {
            return $this->fluidColumns[$name];
        }

        if ($this->table->hasColumn($name)) {
            $column = $this->table->getColumn($name);
        } else {
            $column = $this->table->addColumn($name, 'string');
        }

        $this->fluidColumns[$name] = new FluidColumn($this->schema, $this, $this->table, $column, $this->namingStrategy);
        return $this->fluidColumns[$name];
    }

    public function index(array $columnNames, ?string $indexName = null): FluidTable
    {
        $this->table->addIndex($this->quoteArray($columnNames), $indexName);
        return $this;
    }

    public function unique(array $columnNames, ?string $indexName = null): FluidTable
    {
        $this->table->addUniqueIndex($this->quoteArray($columnNames), $indexName);
        return $this;
    }

    public function primaryKey(array $columnNames, ?string $indexName = null): FluidTable
    {
        $newIndexName = $indexName ?: false;

        $this->table->setPrimaryKey($this->quoteArray($columnNames), $newIndexName);
        return $this;
    }

    private function quoteArray(array $columnNames): array
    {
        return array_map([$this->namingStrategy, 'quoteIdentifier'], $columnNames);
    }

    /**
     * Creates a "id" autoincremented primary key column.
     *
     * @return FluidTable
     */
    public function id(): FluidTable
    {
        $this->column('id')->integer()->primaryKey()->autoIncrement();
        return $this;
    }

    /**
     * Creates a "uuid" primary key column.
     *
     * @return FluidTable
     */
    public function uuid(): FluidTable
    {
        $this->column('uuid')->guid()->primaryKey();
        return $this;
    }

    /**
     * Creates "created_at" and "updated_at" columns.
     *
     * @return FluidTable
     */
    public function timestamps(): FluidTable
    {
        $this->column('created_at')->datetimeImmutable();
        $this->column('updated_at')->datetimeImmutable();
        return $this;
    }

    public function extends(string $tableName): FluidTable
    {
        $tableName = $this->namingStrategy->quoteIdentifier($tableName);

        $inheritedTable = $this->schema->getDbalSchema()->getTable($tableName);

        $pks = $inheritedTable->getPrimaryKey()->getColumns();

        if (count($pks) > 1) {
            throw new FluidSchemaException('You cannot inherit from a table with a primary key on several columns using FluidSchema. Use DBAL Schema methods instead.');
        }

        $pkName = $pks[0];
        $pk = $inheritedTable->getColumn($pkName);

        $this->column($pk->getName())->references($tableName)->primaryKey();
        return $this;
    }

    /**
     * Returns the underlying DBAL table.
     * @return Table
     */
    public function getDbalTable(): Table
    {
        return $this->table;
    }
}
