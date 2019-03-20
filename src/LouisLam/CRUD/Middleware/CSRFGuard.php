<?php
/**
 * CSRF Guard
 *
 * Use this middleware with your Slim Framework application
 * to protect you from CSRF attacks.
 *
 * USAGE
 *
 * $app = new \Slim\Slim();
 * $app->add(new \Slim\Extras\Middleware\CsrfGuard());
 *
 */

namespace LouisLam\CRUD\Middleware;


class CSRFGuard extends \Slim\Middleware
{
    
    public static $token = null;
    protected static $active = true;
    
    /**
     * CSRF token key name.
     *
     * @var string
     */
    protected $key;
    /**
     * Constructor.
     *
     * @param string    $key        The CSRF token key name.
     * @return void
     */
    public function __construct($key = 'csrf_token')
    {
        if (! is_string($key) || empty($key) || preg_match('/[^a-zA-Z0-9\-\_]/', $key)) {
            throw new \OutOfBoundsException('Invalid CSRF token key "' . $key . '"');
        }
        $this->key = $key;
    }
    
    public static function inputTag() {
        $t = self::$token;
        return "<input type='hidden' name='csrf_token' value='$t' />";
    }
    
    /**
     * Call middleware.
     *
     * @return void
     */
    public function call()
    {
        // Attach as hook.
        $this->app->hook('slim.before', array($this, 'check'));
        // Call next middleware.
        $this->next->call();
    }
    
    /**
     * Check CSRF token is valid.
     * Note: Also checks POST data to see if a Moneris RVAR CSRF token exists.
     *
     * @return void
     * @throws \Exception
     */
    public function check() {
        if (! self::$active) {
            return;
        }
        
        // Check sessions are enabled.
        if (session_id() === '') {
            throw new \Exception('Sessions are required to use the CSRF Guard middleware.');
        }
        
        if (isset($_SESSION["csrf_time"])) {
            $expired = (time() - $_SESSION["csrf_time"]) > 3600;
        } else {
            $expired = true;
        }
        
        if (isset($_GET["expire_token"])) {
            $_SESSION["csrf_time"] = time() - 10000;
        }
        
        if (! isset($_SESSION[$this->key]) || $expired) {
            $_SESSION[$this->key] = sha1(serialize($_SERVER) . rand(0, 0xffffffff));
            $_SESSION["csrf_time"] = time();
        }
        
        $token = $_SESSION[$this->key];
        // Validate the CSRF token.
        if (in_array($this->app->request()->getMethod(), array('POST', 'PUT', 'DELETE'))) {
            $userToken = $this->app->request()->post($this->key);
       
            if ($token !== $userToken) {
                $this->app->halt(400, 'Invalid or missing or expired CSRF token.');
            }
        }
        // Assign CSRF token key and value to view.
        self::$token = $token;
    }
    
    public function setActive($v) {
        self::$active = $v;
    }
}