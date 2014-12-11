<?php
namespace AppBundle\Controller;

use AppBundle\Entity\ConfigKeyRepository;
use AppBundle\Entity\ConfigValue;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ReportController extends Controller
{
    public function fileAction($file)
    {
        /** @var ConfigKeyRepository $repository */
        $repository = $this->getDoctrine()->getRepository('AppBundle:ConfigKey');

        $data = $repository->getValueCountByKey($file . '.xml');

        $configKey = $repository->findOneBy(array(
            'file' => $file . '.xml'
        ));

        return $this->render(
            'report/file.html.twig',
            array(
                'configKey' => $configKey,
                'data' => $data,
            )
        );
    }

    public function valuesAction($keyId)
    {
        $repository = $this->getDoctrine()->getRepository('AppBundle:ConfigValue');

        $data = $repository->getCustomerSystemCountByValue($keyId);

        $configKey = $this->getDoctrine()->getRepository('AppBundle:ConfigKey')->findOneBy(array(
            'id' => $keyId
        ));

        return $this->render(
            'report/values.html.twig',
            array(
                'configKey' => $configKey,
                'data' => $data,
            )
        );
    }

    public function customersAction($valueId)
    {
        /** @var ConfigValue $configValue */
        $configValue = $this->getDoctrine()->getRepository('AppBundle:ConfigValue')->findOneBy(array(
            'id' => $valueId
        ));

        return $this->render(
            'report/customers.html.twig',
            array('configValue' => $configValue)
        );
    }

}
