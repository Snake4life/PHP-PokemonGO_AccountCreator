<?php
/**
 * @author Christoph Bessei
 * @version
 */

namespace CB\Mail;


use CB\bDOMDocument;
use Curl\Curl;

class Fyii extends TrashMailer
{
    /** @var string */
    protected static $inboxUrl = "https://www.fyii.de/";
    protected static $contentUrl = "https://www.fyii.de/mail.php";
    /** @var  string */
    protected static $domain = "fyii.de";

    /**
     * @param string $subject
     * @param bool $exactMatch
     * @return null|string
     */
    public function getMailContentBySubject($subject, $exactMatch = false)
    {
        $curl = new Curl();
        $inboxHtml = $curl->get(self::$inboxUrl, array("search" => $this->username));
        $dom = new bDOMDocument();
        @$dom->loadHTML($inboxHtml);
        $elements = $dom->getElementsByAttribute("class", "email");
        $length = $elements->length;
        for ($i = 0; $i < $length; $i++) {
            $element = $elements->item($i);
            $subjectElements = $dom->getElementsByAttribute("class", "pointer", $element);
            $subjectElementLength = $subjectElements->length;
            for ($j = 0; $j < $subjectElementLength; $j++) {
                $subjectElement = $subjectElements->item($j);
                if (false !== stripos($subjectElement->textContent, $subject) && $subjectElement instanceof \DOMElement) {
                    $loadMailAttribute = $subjectElement->getAttribute("onclick");
                    $loadMailParams = $this->getLoadMailParams($loadMailAttribute);
                    $curl = new Curl();
                    $content = $curl->get(self::$contentUrl, $loadMailParams);
                    if (!$curl->curlError && !empty($content)) {
                        return $content;
                    }
                }
            }
        }
        return null;
    }

    protected function getLoadMailParams($loadMailAttribute)
    {
        if (preg_match('#\(\'([^\']+)\'\s*,\s*([^)]+)\)#is', $loadMailAttribute, $matches) && 3 === count($matches)) {

            return array(
                "search" => $matches[1],
                "nr" => $matches[2]
            );
        }
        return null;
    }
}