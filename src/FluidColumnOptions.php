<?php


namespace TheCodingMachine\FluidSchema;

use Doctrine\DBAL\Schema\Column;
use Doctrine\DBAL\Types\Type;

class FluidColumnOptions
{
    /**
     * @var FluidTable
     */
    private $fluidTable;
    /**
     * @var Column
     */
    private $column;
    /**
     * @var NamingStrategyInterface
     */
    private $namingStrategy;

    /**
     * FluidColumn constructor.
     * @param FluidTable $fluidTable
     * @param Column $column
     */
    public function __construct(FluidTable $fluidTable, Column $column, NamingStrategyInterface $namingStrategy)
    {
        $this->fluidTable = $fluidTable;
        $this->column = $column;
        $this->namingStrategy = $namingStrategy;
    }

    /**
     * Makes the column not nullable.
     * @return FluidColumnOptions
     */
    public function notNull(): FluidColumnOptions
    {
        $this->column->setNotnull(true);
        return $this;
    }

    /**
     * Makes the column nullable.
     * @return FluidColumnOptions
     */
    public function null(): FluidColumnOptions
    {
        $this->column->setNotnull(false);
        return $this;
    }

    /**
     * Automatically add a unique constraint for the column.
     *
     * @return FluidColumnOptions
     */
    public function unique(): FluidColumnOptions
    {
        $this->column->setCustomSchemaOption('unique', true);
        return $this;
    }

    /**
     * Automatically add an index for the column.
     *
     * @return FluidColumnOptions
     */
    public function index(?string $indexName = null): FluidColumnOptions
    {
        $this->fluidTable->index([$this->namingStrategy->quoteIdentifier($this->column->getName())], $indexName);
        return $this;
    }
    public function comment(string $comment): FluidColumnOptions
    {
        $this->column->setComment($comment);
        return $this;
    }

    public function autoIncrement(): FluidColumnOptions
    {
        $this->column->setAutoincrement(true);
        return $this;
    }

    public function primaryKey(?string $indexName = null): FluidColumnOptions
    {
        $newIndexName = $indexName ?: false;

        $this->fluidTable->primaryKey([$this->namingStrategy->quoteIdentifier($this->column->getName())], $newIndexName);
        return $this;
    }

    public function default($defaultValue): FluidColumnOptions
    {
        $this->column->setDefault($defaultValue);
        return $this;
    }

    public function then(): FluidTable
    {
        return $this->fluidTable;
    }

    public function column($name): FluidColumn
    {
        return $this->fluidTable->column($name);
    }
}
