<?php
namespace Luceo\Zcd\DataProvider\CustomerSystem;

use AppBundle\Entity\CustomerSystem;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\Message\ResponseInterface;


/**
 * Uses HAL Api to retrieve customers
 * @package Luceo\Zcd\DataProvider\CustomerSystem
 */
class HalDataProvider implements DataProviderInterface
{
    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @param ClientInterface $httpClient
     */
    function __construct(ClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * {@inheritDoc}
     */
    public function getAllSystems()
    {
        /** @var ResponseInterface $res */
        $res = $this->httpClient->get(
            'http://hal.profilsoft.com/customer-systems?platform=0',
            array(
                'headers' => array(
                    'Accept' => 'application/json'
                )
            )
        );

        if ($res->getStatusCode() !== 200) {
            throw new \RuntimeException('Could not retrieve customer systems list from HAL');
        }

        $data = $res->json();

        $customerSystems = array();

        foreach ($data['data'] as $csData) {
            if (! $csData['active']) {
                continue;
            }

            $customerSystems[] = new CustomerSystem($csData['code'], $csData['version']);
        }

        return $customerSystems;
    }
}