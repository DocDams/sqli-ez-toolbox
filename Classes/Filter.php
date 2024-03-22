<?php

declare(strict_types=1);

namespace SQLI\EzToolboxBundle\Classes;

class Filter
{
    public const OPERANDS_MAPPING = [
        "=" => "EQ",
        "!=" => "NEQ",
        "<" => "LT",
        "<=" => "LTE",
        ">" => "GT",
        ">=" => "GTE",
        "LIKE" => "LIKE",
        "NOT LIKE" => "NLIKE",
    ];

    protected mixed $columnName;
    protected mixed $operand;
    protected mixed $value;

    public static function create($columnName, $operand, $value): ?self
    {
        if (array_search($operand, self::OPERANDS_MAPPING)) {
            $filter = new self();

            $filter->columnName = $columnName;
            $filter->operand = $operand;
            $filter->value = $value;

            return $filter;
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function getColumnName(): mixed
    {
        return $this->columnName;
    }

    /**
     * @param mixed $columnName
     */
    public function setColumnName(mixed $columnName): void
    {
        $this->columnName = $columnName;
    }

    /**
     * @return mixed
     */
    public function getOperand(): mixed
    {
        return $this->operand;
    }

    /**
     * @param mixed $operand
     */
    public function setOperand(mixed $operand): void
    {
        $this->operand = $operand;
    }

    /**
     * @return mixed
     */
    public function getValue(): mixed
    {
        return $this->value;
    }

    /**
     * @param mixed $value
     */
    public function setValue(mixed $value): void
    {
        $this->value = $value;
    }

    private function getOperandsValues(): array
    {
        return array_values(self::OPERANDS_MAPPING);
    }
}
