<?php
namespace Profilsoft\XmlDiff;

// TODO: make the XmlDiff a standalone library and import it here via composer

/**
 * Convert XML file to a flat array
 * Use full XPath as Key
 */
class XmlToFlatArrayConverter
{
    /**
     * Result array
     * @var array
     */
    protected $nodesArray = array();

    /**
     * Main conversion method
     * @param string $xml
     * @return array
     * @throws \RuntimeException
     */
    public function convert($xml)
    {
        // Reset results
        $this->nodesArray = array();

        $doc = new \DOMDocument();
        if (! @$doc->loadXML($xml)) {
            throw new \RuntimeException('Invalid XML');
        }

        $root = $doc->documentElement;

        $this->processNode($root, '/'.$root->tagName);

        return $this->nodesArray;
    }

    /**
     * Process the given node and returns sub array
     * Updates $this->nodesArray
     * Recursive method
     * @param \DOMNode $node Node to process
     * @param string $nodeXPath Current node Xpath
     */
    protected function processNode(\DOMNode $node, $nodeXPath)
    {
        /**
         * Adds this node to the result array
         * Empty value for the moment
         * We'll change it later if we have a content
         */
        $this->nodesArray[$nodeXPath] = '';

        // Attributes
        foreach ($node->attributes as $attr) {
            $this->nodesArray[$nodeXPath.'/@'.$attr->name] = $attr->value;
        }

        // We must count children to increment index
        $childrenCount = array();

        /**
         * Note: Comments are relative to what's following
         * So we can not create them directly but must wait for the next sibling
         * We use an array to stack them meanwhile
         */
        $commentStack = array();

        /**
         * Comments at the end are not related to a preceding sibling
         * We attach then as following sibling of the last node
         * So we must store its path
         */
        $lastNodePath = '';

        // Recursivly reads children
        foreach ($node->childNodes as $child) {

            switch ($child->nodeType) {
                case XML_COMMENT_NODE:
                    // We only stack comments for the moment (see explanation before)
                    $commentStack[] = trim($child->nodeValue);
                    break;
                case XML_ELEMENT_NODE:
                    if (! isset($childrenCount[$child->tagName])) {
                        $childrenCount[$child->tagName] = 1;
                    } else {
                        $childrenCount[$child->tagName]++;
                    }

                    $childNodePath = $nodeXPath.'/'.$child->tagName.'['.$childrenCount[$child->tagName].']';

                    // Related comments (be carefull: preceding-sibling are counted in reverse order)
                    $commentCount = count($commentStack);
                    foreach ($commentStack as $commentIndex => $comment) {
                        $path = $childNodePath
                            . '/preceding-sibling::comment()[' . ($commentCount - ($commentIndex)) . ']';
                        $this->nodesArray[$path] = $comment;
                    }
                    // Reset stack
                    $commentStack = array();

                    // One level deeper
                    $this->processNode($child, $childNodePath);

                    // Stores node name for later
                    $lastNodePath = $childNodePath;

                    break;
                case XML_TEXT_NODE:
                    if (! isset($this->nodesArray[$nodeXPath]) || $this->nodesArray[$nodeXPath] === '') {
                        $this->nodesArray[$nodeXPath] = trim($child->nodeValue);
                    }
                    break;
                case XML_CDATA_SECTION_NODE:
                    $this->nodesArray[$nodeXPath] = '<![CDATA['.$child->nodeValue.']]>';
                    break;
            }
        }

        /**
         * Fallback if still some stacked comments
         * It means that they are at the end
         * We use the [last()] keyword
         */
        foreach ($commentStack as $commentIndex => $comment) {
            $path = $lastNodePath . '/following-sibling::comment()[' . ($commentIndex + 1) . ']';
            $this->nodesArray[$path] = $comment;
        }
    }
}