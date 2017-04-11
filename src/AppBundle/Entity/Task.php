<?php

namespace AppBundle\Entity;

/**
 * Task
 */
class Task
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var int
     */
    private $number;

    /**
     * @var string
     */
    private $nullable;

    /**
     * @var string
     */
    private $uniqueValue;

    /**
     * @var \DateTime
     */
    private $dateTime;

    /**
     * @var \DateTime
     */
    private $time;

    /**
     * @var float
     */
    private $floatValue;

    /**
     * @var string
     */
    private $decimalValue;

    /**
     * @var array
     */
    private $array;

    /**
     * @var array
     */
    private $simpleArray;

    /**
     * @var array
     */
    private $jsonArray;

    /**
     * @var bool
     */
    private $boolean;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Task
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set number
     *
     * @param integer $number
     *
     * @return Task
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * Get number
     *
     * @return int
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Set nullable
     *
     * @param string $nullable
     *
     * @return Task
     */
    public function setNullable($nullable)
    {
        $this->nullable = $nullable;

        return $this;
    }

    /**
     * Get nullable
     *
     * @return string
     */
    public function getNullable()
    {
        return $this->nullable;
    }

    /**
     * Set uniqueValue
     *
     * @param string $uniqueValue
     *
     * @return Task
     */
    public function setUniqueValue($uniqueValue)
    {
        $this->uniqueValue = $uniqueValue;

        return $this;
    }

    /**
     * Get uniqueValue
     *
     * @return string
     */
    public function getUniqueValue()
    {
        return $this->uniqueValue;
    }

    /**
     * Set dateTime
     *
     * @param \DateTime $dateTime
     *
     * @return Task
     */
    public function setDateTime($dateTime)
    {
        $this->dateTime = $dateTime;

        return $this;
    }

    /**
     * Get dateTime
     *
     * @return \DateTime
     */
    public function getDateTime()
    {
        return $this->dateTime;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     *
     * @return Task
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * Set floatValue
     *
     * @param float $floatValue
     *
     * @return Task
     */
    public function setFloatValue($floatValue)
    {
        $this->floatValue = $floatValue;

        return $this;
    }

    /**
     * Get floatValue
     *
     * @return float
     */
    public function getFloatValue()
    {
        return $this->floatValue;
    }

    /**
     * Set decimalValue
     *
     * @param string $decimalValue
     *
     * @return Task
     */
    public function setDecimalValue($decimalValue)
    {
        $this->decimalValue = $decimalValue;

        return $this;
    }

    /**
     * Get decimalValue
     *
     * @return string
     */
    public function getDecimalValue()
    {
        return $this->decimalValue;
    }

    /**
     * Set array
     *
     * @param array $array
     *
     * @return Task
     */
    public function setArray($array)
    {
        $this->array = $array;

        return $this;
    }

    /**
     * Get array
     *
     * @return array
     */
    public function getArray()
    {
        return $this->array;
    }

    /**
     * Set simpleArray
     *
     * @param array $simpleArray
     *
     * @return Task
     */
    public function setSimpleArray($simpleArray)
    {
        $this->simpleArray = $simpleArray;

        return $this;
    }

    /**
     * Get simpleArray
     *
     * @return array
     */
    public function getSimpleArray()
    {
        return $this->simpleArray;
    }

    /**
     * Set jsonArray
     *
     * @param array $jsonArray
     *
     * @return Task
     */
    public function setJsonArray($jsonArray)
    {
        $this->jsonArray = $jsonArray;

        return $this;
    }

    /**
     * Get jsonArray
     *
     * @return array
     */
    public function getJsonArray()
    {
        return $this->jsonArray;
    }

    /**
     * Set boolean
     *
     * @param boolean $boolean
     *
     * @return Task
     */
    public function setBoolean($boolean)
    {
        $this->boolean = $boolean;

        return $this;
    }

    /**
     * Get boolean
     *
     * @return bool
     */
    public function getBoolean()
    {
        return $this->boolean;
    }
}
