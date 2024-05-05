<?php declare(strict_types=1);

namespace QueryBuilder;

use Exception;
use QueryBuilder\Condition\ConditionFactory;

class UpdateQuery extends AbstractQuery
{
    /**
     * Error message for missing fields
     *
     * @var string
     */
    private const MISSING_FIELD_ERROR = 'You cannot create an update statement without fields and values to update';

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

        $query_string = "UPDATE $this->table SET ";
        foreach ($this->fields as $field) {
            $query_string .= "$field = ?, ";
        }

        $query_string = trim($query_string, ', ');
        if (!empty($this->conditions)) {
            $query_string .= ' ' . ConditionFactory::createWhere($this->conditions);
        }

        if (!empty($this->limit)) {
            $query_string .= ' ' . ConditionFactory::createLimit($this->limit);
        }

        return $query_string;
    }

    /**
     * @inheritDoc
     */
    public function params(): array
    {
        $conditions = [];
        foreach ($this->conditions as $condition) {
            if (is_array($condition)) {
                $conditions = array_merge($conditions, array_values($condition));
            } else {
                $conditions[] = $condition;
            }
        }

        return array_merge($this->values, $conditions);
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

    /**
     * Set the where conditions
     *
     * @param array $conditions List of conditions to filter the query by
     * @param bool $override Set to true to assign the conditions in stead of merging them
     * @return self
     */
    public function where(array $conditions, bool $override = false): self
    {
        return $this->assign('conditions', $conditions, $override);
    }

    /**
     * Limit the number of records returned
     *
     * @param int $limit The limit to assign
     * @return self
     */
    public function limit(int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }
}
