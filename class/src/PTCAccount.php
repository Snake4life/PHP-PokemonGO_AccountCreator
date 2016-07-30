<?php
/**
 * @author Christoph Bessei
 * @version
 */

namespace CB;


use CB\Mail\TrashMailer;
use DateTime;

class PTCAccount
{
    /** @var string */
    public $username;
    /** @var string */
    public $password;
    /** @var \DateTime */
    public $dateOfBirth;
    /** @var string */
    public $country;
    /** @var TrashMailer */
    public $email;

    /**
     * PTCAccount constructor.
     * @param $username
     * @param $password
     * @param $dateOfBirth
     * @param $email
     * @param $country
     */
    public function __construct($username = "", $password = "", $dateOfBirth = null, $country = "", $email = null)
    {
        $this->username = $username;
        $this->password = $password;
        $this->dateOfBirth = $dateOfBirth;
        $this->country = $country;
        $this->email = $email;
    }

    /**
     * @return DateTime
     */
    public static function generateDateOfBirth()
    {
        $year = mt_rand(1950, 1980);
        $month = mt_rand(1, 12);
        $day = mt_rand(1, 28);
        return DateTime::createFromFormat("Y-n-j", $year . "-" . $month . "-" . $day);
    }

    /**
     * Username
     * @param $prefix
     * @return string
     */
    public static function generateUsername($prefix)
    {
        while (strlen($prefix) <= 10) {
            $prefix .= substr(sha1(mt_rand(0, PHP_INT_MAX)), mt_rand(0, 39), 1);
        }
        return $prefix;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return array(
            $this->username,
            $this->password,
            $this->dateOfBirth->format("Y-m-d"),
            $this->country,
            $this->email->getEMail()
        );
    }
}

