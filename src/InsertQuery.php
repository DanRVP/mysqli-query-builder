<?php declare(strict_types=1);

namespace QueryBuilder;

use Exception;
use QueryBuilder\Condition\ConditionFactory;

class InsertQuery extends AbstractQuery
{
    /**
     * Error message for missing fields
     *
     * @var string
     */
    private const MISSING_FIELD_ERROR = 'You cannot create an insert statement without fields and values to insert';

    /**
     * Update keys
     *
     * @var array
     */
    protected array $fields = [];

    /**
     * Update values
     *
     * @var array
     */
    protected array $values = [];

    /**
     * Where conditions
     *
     * @var array
     */
    protected array $conditions = [];

    /**
     * Query limit
     *
     * @var int|null
     */
    protected ?int $limit = null;

    /**
     * Constructor
     *
     * @param string $table Table name
     * @param array $values Associative array of values to update
     * @throws Exception
     */
    public function __construct(protected string $table, array $values)
    {
        if (empty($values)) {
            throw new Exception(self::MISSING_FIELD_ERROR);
        }

        $this->values($values, true);
    }

    /**
     * @inheritDoc
     * @throws Exception
     */
    public function sql(): string
    {
        if (empty($this->fields) || empty($this->values)) {
            // Somehow managed to null out value. Throw error
            throw new Exception(self::MISSING_FIELD_ERROR);
        }

        return "INSERT INTO $this->table ("
            . implode(', ', $this->fields)
            . ") VALUES ("
            . implode(', ', array_pad([], count($this->fields), '?'))
            . ")";
    }

    /**
     * @inheritDoc
     */
    public function params(): array
    {
        return $this->values;
    }

    /**
     * Set the fields and values to be updated
     *
     * @param array $values Associative array of keys and values to update
     * @param bool $override Set to true to assign the values instead of merging them
     * @return self
     */
    public function values(array $values, bool $override = false): self
    {
        $keys = array_keys($values);
        $values = array_values($values);

        $this->assign('values', $values, $override);
        $this->assign('fields', $keys, $override);

        return $this;
    }
}
