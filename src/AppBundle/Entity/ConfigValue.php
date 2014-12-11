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
     * @var ConfigKey
     */
    private $configKey;

    /**
     * @var CustomerSystem[]
     */
    private $customerSystems;

    /**
     * @param $configKey
     * @param $value
     */
    function __construct($configKey, $value)
    {
        $this->configKey = $configKey;
        $this->value = $value;

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
     * @param ConfigKey $configKey
     * @return $this
     */
    public function setConfigKey(ConfigKey $configKey)
    {
        $this->configKey = $configKey;
        return $this;
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
     * @param string $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
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
}
