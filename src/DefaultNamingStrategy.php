<?php


namespace TheCodingMachine\FluidSchema;

use Doctrine\Common\Inflector\Inflector;
use Doctrine\DBAL\Platforms\AbstractPlatform;

class DefaultNamingStrategy implements NamingStrategyInterface
{
    /**
     * @var AbstractPlatform
     */
    private $platform;

    public function __construct(AbstractPlatform $platform = null)
    {
        $this->platform = $platform;
    }

    /**
     * Returns the name of the jointure table from the name of the joined tables.
     *
     * @param string $table1
     * @param string $table2
     * @return string
     */
    public function getJointureTableName(string $table1, string $table2): string
    {
        return $table1.'_'.$table2;
    }

    /**
     * Returns the name of a foreign key column based on the name of the targeted table.
     *
     * @param string $targetTable
     * @return string
     */
    public function getForeignKeyColumnName(string $targetTable): string
    {
        return $this->toSingular($targetTable).'_id';
    }

    /**
     * Put all the words of a string separated by underscores on singular.
     * Assumes the words are in English and in their plural form.
     *
     * @param $plural
     * @return string
     */
    private function toSingular($plural): string
    {
        $tokens = preg_split("/[_ ]+/", $plural);

        $strs = [];
        foreach ($tokens as $token) {
            $strs[] = Inflector::singularize($token);
        }

        return implode('_', $strs);
    }

    /**
     * Let's quote if a database platform has been provided to us!
     *
     * @param string $identifier
     * @return string
     */
    public function quoteIdentifier(string $identifier): string
    {
        return ($this->platform !== null) ? $this->platform->quoteIdentifier($identifier) : $identifier;
    }
}
