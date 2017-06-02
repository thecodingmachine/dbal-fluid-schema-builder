<?php

namespace TheCodingMachine\FluidSchema;

interface NamingStrategyInterface
{
    /**
     * Returns the name of the jointure table from the name of the joined tables.
     *
     * @param string $table1
     * @param string $table2
     * @return string
     */
    public function getJointureTableName(string $table1, string $table2): string;

    /**
     * Returns the name of a foreign key column based on the name of the targeted table.
     *
     * @param string $targetTable
     * @return string
     */
    public function getForeignKeyColumnName(string $targetTable): string;
}