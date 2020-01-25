<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2020-01-25
 * Time: 16:27
 */

namespace StCommonService\Service\JWTAuth;

use Zend\Authentication\Adapter\AbstractAdapter;
use Zend\Authentication\Result as AuthenticationResult;
use Doctrine\ORM\EntityManager;
use StCommonService\Config\JWTConfig;
use Doctrine\Common\Inflector\Inflector;

class JWTAuthAdapter extends AbstractAdapter
{

    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var JWTConfig
     */
    protected $config;

    /**
     * Contains the authentication results.
     *
     * @var array
     */
    protected $authenticationResultInfo = null;


    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /*
     * {@inheritDoc}
     */
    public function authenticate()
    {
        $this->setup();

        $identity = $this->em->getRepository(
            $this->getConfig('identity_class')
        )->findOneBy([
            $this->getConfig('identity_property') => $this->identity
        ]);

        if (empty($identity)) {
            $this->authenticationResultInfo['code']       = AuthenticationResult::FAILURE_IDENTITY_NOT_FOUND;
            $this->authenticationResultInfo['messages'][] = 'A record with the supplied identity could not be found.';

            return $this->createAuthenticationResult();
        }

        $authResult = $this->validateIdentity($identity);

        return $authResult;
    }

    /**
     * This method attempts to validate that the record in the resultset is indeed a
     * record that matched the identity provided to this adapter.
     *
     * @param  object                              $identity
     * @throws \UnexpectedValueException
     * @return AuthenticationResult
     */
    protected function validateIdentity($identity)
    {
        $credentialProperty = $this->getConfig('credential_property');
        $getter             = 'get' . Inflector::classify($credentialProperty);
        $documentCredential = null;

        if (method_exists($identity, $getter)) {
            $documentCredential = $identity->$getter();
        } elseif (property_exists($identity, $credentialProperty)) {
            $documentCredential = $identity->{$credentialProperty};
        } else {
            throw new \UnexpectedValueException(
                sprintf(
                    'Property (%s) in (%s) is not accessible. You should implement %s::%s()',
                    $credentialProperty,
                    get_class($identity),
                    get_class($identity),
                    $getter
                )
            );
        }

        $credentialValue = $this->credential;
        $callable        = $this->getConfig('credential_callable');

        if ($callable) {
            $credentialValue = call_user_func($callable, $identity, $credentialValue);
        }

        if ($credentialValue !== true && $credentialValue !== $documentCredential) {
            $this->authenticationResultInfo['code']       = AuthenticationResult::FAILURE_CREDENTIAL_INVALID;
            $this->authenticationResultInfo['messages'][] = 'Supplied credential is invalid.';

            return $this->createAuthenticationResult();
        }

        $this->authenticationResultInfo['code']     = AuthenticationResult::SUCCESS;
        $this->authenticationResultInfo['identity'] = $identity;
        $this->authenticationResultInfo['messages'] = [
            'jwt' => 'hello',
            'Authentication successful.',
        ];

        return $this->createAuthenticationResult();
    }

    /**
     * This method abstracts the steps involved with making sure that this adapter was
     * indeed setup properly with all required pieces of information.
     *
     * @throws \RuntimeException- in the event that setup was not done properly
     */
    protected function setup()
    {
        if (empty($this->identity))
            throw new \RuntimeException(
                'A value for the identity was not provided prior to authentication with JWTAuthAdapter '
                . 'authentication adapter'
            );

        if (empty($this->credential))
            throw new \RuntimeException(
                'A credential value was not provided prior to authentication with JWTAuthAdapter'
                . ' authentication adapter'
            );


    }

    /**
     * Creates a Zend\Authentication\Result object from the information that has been collected
     * during the authenticate() attempt.
     *
     * @return AuthenticationResult
     */
    protected function createAuthenticationResult()
    {
        return new AuthenticationResult(
            $this->authenticationResultInfo['code'],
            $this->authenticationResultInfo['identity'],
            $this->authenticationResultInfo['messages']
        );
    }

    /**
     * @param EntityManager $em
     */
    public function setEm(EntityManager $em): void
    {
        $this->em = $em;
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