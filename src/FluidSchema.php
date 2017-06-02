<?php
namespace TheCodingMachine\FluidSchema;

use Doctrine\DBAL\Schema\Schema;

/**
 * This class wraps a DBAL schema and offers a fluid syntax on top of it.
 */
class FluidSchema
{
    /**
     * @var Schema
     */
    private $schema;

    /**
     * @var FluidTable[]
     */
    private $fluidTables;
    /**
     * @var NamingStrategyInterface
     */
    private $namingStrategy;

    /**
     * @param Schema $schema
     * @param null|NamingStrategyInterface $namingStrategy
     */
    public function __construct(Schema $schema, ?NamingStrategyInterface $namingStrategy = null)
    {
        $this->schema = $schema;
        $this->namingStrategy = $namingStrategy ?: new DefaultNamingStrategy();
    }

    public function table(string $name): FluidTable
    {
        if (isset($this->fluidTables[$name])) {
            return $this->fluidTables[$name];
        }

        if ($this->schema->hasTable($name)) {
            $table = $this->schema->getTable($name);
        } else {
            $table = $this->schema->createTable($name);
        }

        $this->fluidTables[$name] = new FluidTable($this, $table);
        return $this->fluidTables[$name];
    }

    /**
     * Creates a table joining 2 other tables through a foreign key.
     *
     * @param string $table1
     * @param string $table2
     * @return FluidSchema
     */
    public function junctionTable(string $table1, string $table2): FluidSchema
    {
        $tableName = $this->namingStrategy->getJointureTableName($table1, $table2);
        $column1 = $this->namingStrategy->getForeignKeyColumnName($table1);
        $column2 = $this->namingStrategy->getForeignKeyColumnName($table2);

        $this->table($tableName)
            ->column($column1)->references($table1)->then()
            ->column($column2)->references($table2)->then()
            ->primaryKey([$column1, $column2]);

        return $this;
    }
    
    /**
     * Returns the underlying schema.
     * @return Schema
     */
    public function getDbalSchema(): Schema
    {
        return $this->schema;
    }

}
