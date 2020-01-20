<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2020-01-16
 * Time: 14:38
 */
namespace StCommonService\Service\JWTAuth;

use Zend\Authentication\Storage\StorageInterface;
use Zend\Http\Request;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use StCommonService\Config\JWTConfig;

class Storage implements StorageInterface
{
    /**
     * Request instance
     *
     * @var Request
     */
    protected $request;


    /**
     * @var JWTConfig
     */
    protected $config;


    /**
     * @var Token
     */
    protected $jwt;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * @param mixed $contents
     */
    public function write($contents)
    {
        trigger_error('Storage does not support this write method', E_USER_WARNING);
        return;
    }

    public function isEmpty()
    {
        return empty($this->jwt);
    }

    public function read()
    {
        return $this->jwt;
    }

    public function clear()
    {
        $this->jwt = false;
    }

    /**
     * @throws \Exception
     */
    public function fetchJWT(): void
    {
        if (empty($this->config))
            throw new \Exception('Storage\'s Config not found');

        $token = $this->fetchFromHeader();

        if (empty($token))
            $token = $this->fetchFromQuery();

        if (empty($token)) {
            $this->jwt = false;

            return;
        }


        // validate the JWT token
        $jwt = $this->validateJWT($token);

        if (empty($jwt) || !($jwt instanceof Token)) {
            $this->jwt = false;
            return;
        }

        $this->jwt = $jwt;
    }

    /**
     * Fetch JWT token from Authorization header
     *
     * @return string|null
     */
    protected function fetchFromHeader(): ?string
    {
        $token = $this->request->getHeader('Authorization');

        if (empty($token) || !is_string($token))
            return null;

        return str_replace('Bearer ', '', $token);
    }

    /**
     * Fetch JWT token from Token query param
     *
     * @return string|null
     */
    protected function fetchFromQuery(): ?string
    {
        $token = $this->request->getQuery('token', false);

        return (empty($token) || !is_string($token)) ? null : $token;
    }

    /**
     * @param string|Token $jwt
     */
    protected function validateJWT($jwt)
    {
        try {
            if (is_string($jwt))
                $jwt = (new Parser())->parse($jwt);

            if (!($jwt instanceof Token))
                return false;

            $verification = $jwt->verify($this->config->getConfig('signer'), $this->config->getConfig('key'));

            if (!$verification)
                return false;

            $validationData = new ValidationData(time(), 10);


            $validatableClaims = ['iss', 'aud', 'jti', 'sub'];
            foreach ($validatableClaims as $claim) {
                $claimConfig = $this->getConfig($claim);

                if (empty($claimConfig))
                    continue;

                if (!$jwt->hasClaim($claim))
                    return false;

                $validationData->{'set' . ucfirst($claim)}($claimConfig);
            }

            return $jwt->validate($validationData) ? $jwt : false;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function setConfig(JWTConfig $config)
    {
        $this->config = $config;
    }

    public function getConfig(string $key)
    {
        if (empty($this->config))
            return $this->config;

        return $this->config->getConfig($key);
    }
}