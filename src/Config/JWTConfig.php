<?php
/**
 * Created by PhpStorm.
 * User: apple
 * Date: 2020-01-20
 * Time: 10:38
 */

namespace StCommonService\Config;

use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;

class JWTConfig
{
    /**
     * @var array
     */
    private $_config = [];


    protected $allowedConfigKeys = [
        'signer', 'key',
        'iss', 'issuer',
        'aud', 'audience',
        'sub', 'subject',
        'jti', 'id'
    ];

    /**
     * JWTConfig constructor.
     *
     * @param array $config
     * @throws \Exception
     */
    public function __construct(array $config = [])
    {
        $this->initFromArray($config);
    }

    /**
     * @param array $config
     * @throws \Exception
     */
    protected function initFromArray(array $config): void
    {
        if (empty($config)) {
            $config = $this->getDefaultConfig();
        } else {
            // only allow keys in $allowedConfigKeys and also add default value for missing key(s)
            $config = array_intersect_key($config, array_flip($this->allowedConfigKeys));
            $config = array_merge($this->getDefaultConfig(), $config);
        }

        if (empty($config['key']))
            throw new \Exception('JWT Key is not defined!');
        else
            $config['key'] = new Key($config['key']);

        if (isset($config['iss'])) {
            $config['issuer'] = $config['iss'];
            unset($config['iss']);
        }

        if (isset($config['aud'])) {
            $config['audience'] = $config['aud'];
            unset($config['aud']);
        }

        if (isset($config['sub'])) {
            $config['subject'] = $config['sub'];
            unset($config['sub']);
        }

        if (isset($config['jti'])) {
            $config['id'] = $config['jti'];
            unset($config['jti']);
        }

        $this->_config = $config;
    }


    /**
     * @return array|mixed
     */
    public function getConfig(string $key = '')
    {
        if (empty($key))
            return $this->_config;

        return array_key_exists($key, $this->_config) ? $this->_config[$key] : null;
    }

    public function getDefaultConfig()
    {
        return [
            'signer' => new Sha256(),
        ];
    }
}