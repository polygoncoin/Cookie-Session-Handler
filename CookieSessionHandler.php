<?php
/**
 * Class for using Cookie to managing session data with encryption.
 * 
 * @category   Session
 * @package    Cookie based Session Handler
 * @author     Ramesh Narayan Jangid
 * @copyright  Ramesh Narayan Jangid
 * @version    Release: @1.0.0@
 * @since      Class available since Release 1.0.0
 */
class CookieSessionHandler implements SessionHandlerInterface, SessionUpdateTimestampHandlerInterface
{
    /**
     * 256-bit key
     *
     * @var string
     */
    private $key;

    /**
     * 128-bit IV
     *
     * @var string
     */
    private $iv;

    /**
     * Session cookie name
     *
     * @var string
     */
    private $sessionName;

    /**
     * Session data cookie name
     *
     * @var string
     */
    private $sessionDataName = 'PHPSESSDATA';

    /**
     * Constructor
     */
    function __construct()
    {
        $key_base64 = BASE64_KEY;
        $iv_base64 = BASE64_IV;
        $this->key = base64_decode($key_base64);
        $this->iv = base64_decode($iv_base64);
    }

    /**
     * Encryption
     *
     * @param string $plaintext
     * @return string ciphertext
     */
    function encryptSess($plaintext)
    {
        return openssl_encrypt($plaintext, 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA, $this->iv);	
    }

    /**
     * Decryption
     *
     * @param string $ciphertext
     * @return string plaintext
     */
    function decryptSess($ciphertext)
    {
        return openssl_decrypt($ciphertext, 'AES-256-CBC', $this->key, OPENSSL_RAW_DATA, $this->iv);
    }

    /**
     * A callable with the following signature
     *
     * @param string $savePath
     * @param string $sessionName
     * @return boolean true for success or false for failure
     */
    public function open($savePath, $sessionName): bool
    {
        ob_start(); // Turn on output buffering
        $this->sessionName = $sessionName;
        return true;
    }

    /**
     * A callable with the following signature
     *
     * @return boolean true for success or false for failure
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * A callable with the following signature
     *
     * @param string $sessionId
     * @return string the session data or an empty string
     */
    #[\ReturnTypeWillChange]
    public function read($sessionId)
    {
        if (isset($_COOKIE[$this->sessionDataName]) && !empty($_COOKIE[$this->sessionDataName])) {
            return (string)$this->decryptSess(base64_decode($_COOKIE[$this->sessionDataName]));
        } else {
            return '';
        }
    }

    /**
     * A callable with the following signature
     *
     * @param string $sessionId
     * @param string $sessionData
     * @return boolean true for success or false for failure
     */
    public function write($sessionId, $sessionData): bool
    {
        $encryptedData = base64_encode($this->encryptSess($sessionData));
        setcookie($this->sessionDataName, $encryptedData, time() + (ini_get("session.gc_maxlifetime")), '/');
        ob_end_flush(); //Flush (send) the output buffer and turn off output buffering
        return true;
    }

    /**
     * A callable with the following signature
     *
     * @param string $sessionId
     * @return boolean true for success or false for failure
     */
    public function destroy($sessionId): bool
    {
        setcookie($this->sessionName, '', 1);
        setcookie($this->sessionDataName, '', 1);
        return true;
    }

    /**
     * A callable with the following signature
     *
     * @param integer $maxlifetime
     * @return boolean true for success or false for failure
     */
    #[\ReturnTypeWillChange]
    public function gc($maxlifetime)
    {
        return true;
    }

    /**
     * A callable with the following signature
     * Invoked internally when a new session id is needed
     *
     * @return string should be new session id
     */
    public function create_sid()
    {
        return '';
    }

    /**
     * A callable with the following signature
     *
     * @param string $sessionId
     * @return string true if the session id is valid otherwise false
     */
    #[\ReturnTypeWillChange]
    public function validateId($sessionId)
    {
        return true;
    }

    /**
     * A callable with the following signature
     *
     * @param string $sessionId
     * @param string $sessionData
     * @return boolean true for success or false for failure
     */
    #[\ReturnTypeWillChange]
    public function updateTimestamp($sessionId, $sessionData)
    {
        return true;
    }
}
session_set_save_handler(new CookieSessionHandler(), true);
