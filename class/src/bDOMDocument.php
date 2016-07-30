<?php

namespace CB;

use DOMDocument;
use DOMException;
use DOMXPath;

/**
 * @author Christoph Bessei
 * @version
 */
class bDOMDocument extends DOMDocument
{
    /** @var DOMXPath|null */
    protected $xpath = null;


    /**
     * @param $name
     * @param $value
     * @param null $contextNode
     * @return \DOMNodeList
     */
    public function getElementsByAttribute($name, $value, $contextNode = null)
    {
        $this->getDOMXPath();
        if (null === $contextNode) {
            $queryString = '//*[contains(@' . $name . ',"' . $value . '")]';
            return $this->xpath->query($queryString);
        } else {
            return $this->xpath->query('.//*[contains(@' . $name . ',"' . $value . '")]', $contextNode);
        }
    }

    /**
     * @param $name
     * @param $value
     * @param null $contextNode
     * @return \DOMNode|null
     */
    public function getOneElementByAttribute($name, $value, $contextNode = null)
    {
        $elements = $this->getElementsByAttribute($name, $value, $contextNode);
        if ($elements->length > 0) {
            return $elements->item(0);
        }
        return null;
    }

    /**
     * Get Instance of DOMXPath Object for current DOM
     *
     * @throws DOMException
     *
     * @return DOMXPath
     */
    public function getDOMXPath()
    {
        if (is_null($this->xpath)) {
            $this->xpath = new DOMXPath($this);
        }
        if (!$this->xpath) {
            throw new DOMException('creating DOMXPath object failed.');
        }
        return $this->xpath;
    }
}