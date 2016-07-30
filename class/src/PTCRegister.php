<?php
/**
 * @author Christoph Bessei
 * @version
 */

namespace CB;


use CB\Log\Logger;
use CB\Mail\TrashMailer;
use Curl\Curl;
use DOMElement;

class PTCRegister
{
    protected static $firstPageUrl = "https://club.pokemon.com/us/pokemon-trainer-club/sign-up/";
    protected static $secondPageUrl = "https://club.pokemon.com/us/pokemon-trainer-club/parents/sign-up";
    protected static $thirdPageUrl = "https://club.pokemon.com/us/pokemon-trainer-club/parents/email";

    protected static $defaultSettings = array(
        "public_profile_opt_in" => "False",
        "terms" => "on",
        "screen_name" => ""
    );


    /** @var  array */
    protected $cookies = array();

    /** @var  PTCAccount */
    protected $account;

    protected function loadFirstPage()
    {
        $curl = $this->getCurl();
        $html = $curl->get(self::$firstPageUrl);
        $this->cookies = array_merge($this->cookies, $curl->getResponseCookies());
        $doc = new bDOMDocument();
        @$doc->loadHTML($html);
        $this->logHtml($html);
        $params = array();

        $element = $doc->getOneElementByAttribute("name", "csrfmiddlewaretoken");
        if ($element instanceof DOMElement) {
            $params["csrfmiddlewaretoken"] = $element->getAttribute("value");
        }
        $params["country"] = $this->account->country;
        $params["dob"] = $this->account->dateOfBirth->format("Y-m-d");
        return $params;
    }

    public function loadSecondPage(array $params)
    {
        //Set additional cookies
        $this->cookies["dob"] = $this->account->dateOfBirth->format("Y-m-d");
        $curl = $this->getCurl(false, self::$firstPageUrl);
        $curl->post(self::$firstPageUrl, $params);
        $this->cookies = array_merge($this->cookies, $curl->getResponseCookies());

        $curl = $this->getCurl();
        $curl->get(self::$secondPageUrl, $params);
        $this->cookies = array_merge($this->cookies, $curl->getResponseCookies());

        return $params;

    }

    protected function loadThirdPage(array $params)
    {
        $newParams = array_merge(self::$defaultSettings, $params);
        $newParams["username"] = $this->account->username;
        $newParams["password"] = $this->account->username;
        $newParams["confirm_password"] = $this->account->username;
        $newParams["email"] = $this->account->email->getEMail();
        $newParams["confirm_email"] = $this->account->email->getEMail();

        $curl = $this->getCurl(false, self::$secondPageUrl);
        $html = $curl->post(self::$secondPageUrl, $newParams);
        if (302 === $curl->httpStatusCode) {
            return true;
        }
        return false;
    }

    /**
     * @param PTCAccount $account
     * @return bool
     */
    public function registerAccount(PTCAccount $account)
    {
        $this->account = $account;
        $params = $this->loadFirstPage();
        $params = $this->loadSecondPage($params);
        if (!$this->loadThirdPage($params)) {
            return false;
        }

        //Get confirmation mail
        for ($i = 0; $i < 15; $i++) {
            $content = $this->account->email->getMailContentBySubject("Trainer Club Activation", false);
            if (empty($content) || !preg_match('#href="(https?:\/\/[^"]+\/activated\/[^"]+)"#is', $content, $matches)) {
                //Wait for confirmation mail
                sleep(1);
            } else {
                $curl = new Curl();
                $curl->get($matches[1]);
                if (!$curl->curlError) {
                    return true;
                }
            }
        }


        return false;
    }

    protected function logHtml($html)
    {

        if (\Monolog\Logger::toMonologLevel(LOG_LEVEL) <= \Monolog\Logger::DEBUG) {
            $trace = debug_backtrace();
            $caller = $trace[1];

            Logger::logHTML($caller["function"] . ".html", $html);
        }
    }

    /**
     * @param bool $followRedirect
     * @param null $referer
     * @return Curl
     */
    protected function getCurl($followRedirect = false, $referer = null)
    {
        $curl = new Curl();
        $curl->setUserAgent("Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:47.0) Gecko/20100101 Firefox/47.0");
        $curl->setHeader("Accept", "text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8");
        $curl->setHeader("Accept-Language", "de,en;q=0.5");
        $curl->setHeader("Pragma", "no-cache");
        $curl->setHeader("Cache-Control", "no-cache");
        $curl->setHeader("Connection", "keep-alive");
        if (null !== $referer) {
            $curl->setHeader("Referer", $referer);
        }
        $curl->setOpt(CURLOPT_FOLLOWLOCATION, $followRedirect);

        foreach ($this->cookies as $key => $value) {
            $curl->setCookie($key, $value);
        }
        return $curl;
    }
}