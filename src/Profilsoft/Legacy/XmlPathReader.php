<?php
namespace Profilsoft\Legacy;

/**
 * This is the class used by Luceo to read configuration
 * I just made some var renaming (removed hungarian notation) and CS
 */
class XmlPathReader
{
    /**
     * @param $xml
     * @return array
     */
    public function getArray($xml)
    {
        $result = array();

        $doc = new \DOMDocument();
        $doc->preserveWhiteSpace = false;

        if (! @$doc->loadXML($xml)) {
            throw new \RuntimeException('Invalid Xml');
        }

        $root = $doc->documentElement;

        $this->readNode($result, $root, '');

        return $result;
    }


    /**
     * @param array $result
     * @param \DomNode $node
     * @param $currentPath
     */
    private function readNode(array &$result, \DomNode $node, $currentPath)
    {
        if ($node->attributes) {
            foreach ($node->attributes as $attr) {
                $this->addNode($result, $currentPath . '?' . $attr->name, $attr->value);
            }
        }

        if ($node->childNodes) {
            foreach ($node->childNodes as $child) {
                switch ($child->nodeType) {
                    case XML_ELEMENT_NODE:
                        if ($currentPath) {
                            $strNewPath = $currentPath . '/' . $child->tagName;
                        } else {
                            $strNewPath = $child->tagName;
                        }
                        $this->readNode($result, $child, $strNewPath);

                        break;
                    case XML_ATTRIBUTE_NODE:
                        $this->addNode($result, $currentPath . '?' . $child->name, $child->nodeValue);

                        break;
                    case XML_TEXT_NODE:
                        if ($currentPath && $child->nodeValue) {
                            $this->addNode($result, $currentPath, $child->nodeValue);
                        }
                        break;
                    case XML_CDATA_SECTION_NODE:
                        if ($currentPath && $child->nodeValue) {
                            $this->addNode($result, $currentPath, $child->nodeValue);
                        }
                        break;
                }
            }
        }
    }

    /**
     * @param array $result
     * @param $key
     * @param $value
     */
    private function addNode(array &$result, $key, $value)
    {
        $key = str_replace('__', '/', $key);

        if (!isset($result[$key])) {
            $result[$key] = $value;
        }
    }
}