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

class Storage implements StorageInterface
{
    /**
     * Request instance
     *
     * @var Request
     */
    protected $request;

    /**
     * @var string
     */
    protected $jwt;

    public function __construct(Request $request)
    {
        $this->request = $request;

        $this->fetchJWT();
    }

    public function write($contents)
    {
        return;
    }

    public function isEmpty()
    {
        return empty($this->jwt);
    }

    public function read()
    {
        // TODO: Implement read() method.
    }

    public function clear()
    {
        $this->jwt = false;
    }

    protected function fetchJWT(): void
    {
        $token = $this->fetchFromHeader();

        if (empty($token))
            $token = $this->fetchFromQuery();

        if (empty($token)) {
            $this->jwt = null;

            return;
        }


    }

    protected function fetchFromHeader(): string
    {
        $token = $this->request->getHeader('Authorization');

        if (empty($token) || !is_string($token))
            return null;

        return str_replace('Bearer ', '', $token);
    }

    protected function fetchFromQuery(): string
    {
        $token = $this->request->getQuery('token', false);

        return (empty($token) || !is_string($token)) ? null : $token;
    }
}