<?php
/**
 * Created by PhpStorm.
 * User: Nam Ngo
 * Date: 2020-01-16
 * Time: 14:38
 */
namespace StCommonService\Service\JWTAuth;

use Zend\Authentication\Storage\StorageInterface;
use Doctrine\ORM\EntityManager;
use Zend\Http\Request;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\ValidationData;
use StCommonService\Config\JWTConfig;
use StCommonService\Service\ApiAcl\Acl;

class Storage implements StorageInterface
{
    /**
     * Request instance
     *
     * @var Request
     */
    protected $request;

    /**
     * @var EntityManager
     */
    protected $em;


    /**
     * @var JWTConfig
     */
    protected $config;

    /**
     * @var
     */
    protected $identityEntity;

    /**
     * @var Token
     */
    protected $jwt;

    public function __construct(Request $request, EntityManager $em)
    {
        $this->request = $request;
        $this->em      = $em;
    }

    /**
     * @param mixed $contents
     */
    public function write($contents)
    {
//        trigger_error('Storage does not support this write method', E_USER_WARNING);
        return;
    }

    public function isEmpty()
    {
        return empty($this->jwt);
    }

    public function read()
    {
        if ($this->isEmpty())
            return false;

        // get the uuid
        if (empty($this->identityEntity)) {
            $uuid = $this->jwt->hasClaim('uuid')
                ? $this->jwt->getClaim('uuid')
                : false;

            if (!$uuid) {
                $this->clear();
                return false;
            }

            $repo = $this->em->getRepository(
                $this->getConfig('identity_class')
            );

            if (empty($repo))
                return false;

            $this->identityEntity = $repo->findOneById($uuid);
        } else {
            // client still provides a valid JWT token, it just doesnt have the uuid claim (not login)
            // so we create a default role for that
            $this->identityEntity = new \stdClass();
            $this->identityEntity->getRole = function() {return Acl::DEFAULT_ROLE;};
        }

        return $this->identityEntity;
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
     * Return false if it doesnt pass the validate
     * Token instance otherwise
     *
     * @param false|Token $jwt
     */
    protected function validateJWT($jwt)
    {
        try {
            if (is_string($jwt))
                $jwt = (new Parser())->parse($jwt);

            if (!($jwt instanceof Token))
                return false;

            // validate UUID field
            if (!$jwt->hasClaim('uuid'))
                return false;

            $verification = $jwt->verify($this->config->getConfig('signer'), $this->config->getConfig('key'));

            if (!$verification)
                return false;

            $validationData = new ValidationData(time(), 10);


            // if the claim is not in JWT Token, it still passes the validate method, even though
            // we put it in ValidationData, thats why we have to write this validation
            $validatableClaims = ['iss' => 'issuer', 'aud' => 'audience', 'jti' => 'id', 'sub' => 'subject'];
            foreach ($validatableClaims as $claim => $claimName) {
                $claimConfig = $this->getConfig($claimName);

                if (empty($claimConfig))
                    continue;

                // JWT Token doesnt have this claim, fail the validation chain
                if (!$jwt->hasClaim($claim))
                    return false;

                // set the claim to ValidationData to validate it further
                $validationData->{'set' . ucfirst($claimName)}($claimConfig);
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