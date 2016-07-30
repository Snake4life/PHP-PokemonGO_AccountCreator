<?php
namespace CB\Mail;
/**
 * @author Christoph Bessei
 * @version
 */
abstract class TrashMailer
{
    /** @var  string */
    protected static $inboxUrl;
    /** @var  string */
    protected $username;
    /** @var string */
    protected static $domain;

    public function __construct($username)
    {
        $this->username = $username;
    }

    /**
     * @param string $subject
     * @param bool $exactMatch
     * @return string
     */
    abstract public function getMailContentBySubject($subject, $exactMatch = false);

    /**
     * @return string
     */
    public function getEMail()
    {
        return $this->username . "@" . static::$domain;
    }

    /**
     * @param string $username
     */
    public function setUsername($username)
    {
        $this->username = $username;
    }
}