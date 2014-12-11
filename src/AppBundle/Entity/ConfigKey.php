<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class ConfigKey
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $file;

    /**
     * @var string
     */
    private $path;

    /**
     * @var ConfigValue[]
     */
    private $values;


    /**
     * @param $file
     * @param $path
     */
    public function __construct($file, $path)
    {
        $this->file = $file;
        $this->path = $path;

        $this->values = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @return ConfigValue[]
     */
    public function getValues()
    {
        return $this->values;
    }

    /**
     * @param ConfigValue[] $values
     * @return $this
     */
    public function setValues($values)
    {
        $this->values = $values;
        return $this;
    }

    /**
     * @param ConfigValue $value
     */
    public function addValue(ConfigValue $value)
    {
        $this->values[] = $value;
    }
}