<?php

namespace YB\Bundle\RemoteTranslationsBundle\Translation\Loader\Client\Adapter;

use Exception;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\ClientInterface as GuzzleClientInterface;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use YB\Bundle\RemoteTranslationsBundle\Translation\Loader\Client\ClientAdapterInterface;
use YB\Bundle\RemoteTranslationsBundle\Translation\Exception\InvalidOptionException;

/**
 * Class ApiAdapter
 * @package YB\Bundle\RemoteTranslationsBundle\Translation\Loader\Client\Adapter
 */
class ApiAdapter implements ClientAdapterInterface
{
    /**
     * @var GuzzleClientInterface|GuzzleClient
     */
    private $client;

    /**
     * @var string
     */
    protected $endpoint;

    /**
     * @var string
     */
    protected $method;

    /**
     * @var array
     */
    protected $auth = [];

    /**
     * @var array
     */
    protected $headers = [];

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param GuzzleClientInterface $client
     * @param array $options
     * @throws InvalidOptionException
     */
    public function __construct(GuzzleClientInterface $client, array $options = [])
    {
        $this->client = $client;

        $this->endpoint = isset($options['endpoint']) ? $options['endpoint'] : $this->endpoint;
        $this->method = isset($options['method']) ? $options['method'] : $this->method;
        $this->auth = isset($options['auth']) ? $options['auth'] : $this->auth;
        $this->headers = isset($options['headers']) ? $options['headers'] : $this->headers;

        if (empty($this->endpoint)) {
            throw new InvalidOptionException('Option "endpoint" is required');
        }

        if (empty($this->method)) {
            throw new InvalidOptionException('Option "method" is required');
        }
    }

    /**
     * @inheritdoc
     */
    public function load($locale, $domain)
    {
        $messages = [];

        try {
            $uri = str_replace(['%locale%', '%domain%'], [$locale, $domain], $this->endpoint);

            $options = [];
            if ($this->auth) {
                $options['auth'] = $this->auth;
            }
            if ($this->headers) {
                $options['headers'] = $this->headers;
            }

            /** @var GuzzleResponse $response */
            $response = $this->client->request($this->method, $uri, $options);

            $data = json_decode($response->getBody(), true);
            if (is_array($data)) {
                $messages = $data;
            }

            if ($this->logger) {
                $msg = sprintf('%d messages were uploaded via API with locale "%s" and domain "%s"', count($messages), $locale, $domain);
                $this->logger->debug($msg);
            }
        } catch (Exception $e) {
            if ($this->logger) {
                $msg = $e->getMessage();
                $this->logger->critical($msg);
                var_dump($msg);exit();
            }
        }

        return $messages;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
