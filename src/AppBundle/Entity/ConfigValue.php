<?php
namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;

class ConfigValue
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $value;

    /**
     * @var string
     */
    private $valueHash;

    /**
     * @var ConfigKey
     */
    private $configKey;

    /**
     * @var CustomerSystem[]
     */
    private $customerSystems;

    /**
     * @param ConfigKey $configKey
     * @param $value
     */
    public function __construct(ConfigKey $configKey, $value)
    {
        $this->configKey = $configKey;
        $this->setValue($value);
        $this->customerSystems = new ArrayCollection();
    }


    /**
     * @return ConfigKey
     */
    public function getConfigKey()
    {
        return $this->configKey;
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
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return CustomerSystem[]
     */
    public function getCustomerSystems()
    {
        return $this->customerSystems;
    }

    /**
     * @param CustomerSystem $customerSystem
     * @return $this
     */
    public function addCustomerSystem(CustomerSystem $customerSystem)
    {
        $this->customerSystems[] = $customerSystem;
        return $this;
    }

    /**
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        $this->valueHash = sha1($value);
        return $this;
    }

    /**
     * @return string
     */
    public function getValueHash()
    {
        return $this->valueHash;
    }
}
