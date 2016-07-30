<?php

namespace CB\Mail;

use CB\bDOMDocument;
use Curl\Curl;

/**
 * @author Christoph Bessei
 * @version
 */
class Byom extends TrashMailer
{

    /** @var string */
    protected static $inboxUrl = "https://www.byom.de/nachrichten/server-hosting";
    /** @var  string */
    protected static $domain = "byom.de";

    public function getMailContentBySubject($subject, $exactMatch = false)
    {
        //TODO Implement
    }
}