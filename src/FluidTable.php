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
     * @param FluidSchema $schema
     * @param Table $table
     */
    public function __construct(FluidSchema $schema, Table $table)
    {
        $this->schema = $schema;
        $this->table = $table;
    }

    public function column(string $name): FluidColumn
    {
        if (isset($this->fluidColumns[$name])) {
            return $this->fluidColumns[$name];
        }

        if ($this->table->hasColumn($name)) {
            $column = $this->table->getColumn($name);
        } else {
            $column = $this->table->addColumn($name, 'string');
        }

        $this->fluidColumns[$name] = new FluidColumn($this->schema, $this, $this->table, $column);
        return $this->fluidColumns[$name];
    }

    public function index(array $columnNames): FluidTable
    {
        $this->table->addIndex($columnNames);
        return $this;
    }

    public function unique(array $columnNames): FluidTable
    {
        $this->table->addUniqueIndex($columnNames);
        return $this;
    }

    public function primaryKey(array $columnNames, ?string $indexName = null): FluidTable
    {
        $newIndexName = $indexName ?: false;

        $this->table->setPrimaryKey($columnNames, $newIndexName);
        return $this;
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
}
