<?php

namespace YB\Bundle\RemoteTranslationsBundle\Translation\Loader\Client\Adapter;

use Exception;
use PDO;
use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use YB\Bundle\RemoteTranslationsBundle\Translation\Exception\InvalidOptionException;
use YB\Bundle\RemoteTranslationsBundle\Translation\Loader\Client\ClientAdapterInterface;

/**
 * Class PdoAdapter
 * @package YB\Bundle\RemoteTranslationsBundle\Translation\Loader\Client\Adapter
 */
class PdoAdapter implements ClientAdapterInterface
{
    /**
     * @var PDO
     */
    private $client;

    /**
     * @var string
     */
    private $table;

    /**
     * @var string
     */
    private $localeCol;

    /**
     * @var string
     */
    private $domainCol;

    /**
     * @var string
     */
    private $keyCol;

    /**
     * @var string
     */
    private $valueCol;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param PDO $client
     * @param array $options
     * @throws InvalidOptionException
     */
    public function __construct(PDO $client, array $options = [])
    {
        $this->client = $client;

        $this->table = isset($options['table']) ? $options['table'] : $this->table;
        $this->localeCol = isset($options['locale_col']) ? $options['locale_col'] : $this->localeCol;
        $this->domainCol = isset($options['domain_col']) ? $options['domain_col'] : $this->domainCol;
        $this->keyCol = isset($options['key_col']) ? $options['key_col'] : $this->keyCol;
        $this->valueCol = isset($options['value_col']) ? $options['value_col'] : $this->valueCol;

        if (empty($this->table)) {
            throw new InvalidOptionException('Option "table" is required');
        }

        if (empty($this->localeCol)) {
            throw new InvalidOptionException('Option "locale_col" is required');
        }

        if (empty($this->domainCol)) {
            throw new InvalidOptionException('Option "domain_col" is required');
        }

        if (empty($this->keyCol)) {
            throw new InvalidOptionException('Option "key_col" is required');
        }

        if (empty($this->valueCol)) {
            throw new InvalidOptionException('Option "value_col" is required');
        }
    }

    /**
     * @inheritdoc
     */
    public function load($locale, $domain)
    {
        $messages = [];

        try {
            $selectSql = sprintf(
                'SELECT %s, %s FROM %s WHERE %s = :locale AND %s = :domain',
                $this->keyCol,
                $this->valueCol,
                $this->table,
                $this->localeCol,
                $this->domainCol
            );

            $selectStmt = $this->client->prepare($selectSql);
            $selectStmt->bindParam(':locale', $locale, PDO::PARAM_STR);
            $selectStmt->bindParam(':domain', $domain, PDO::PARAM_STR);
            $selectStmt->execute();

            $data = $selectStmt->fetchAll(PDO::FETCH_ASSOC);
            if ($data) {
                $messages = array_column($data, $this->valueCol, $this->keyCol);
            }

            if ($this->logger) {
                $msg = sprintf('%d messages were uploaded via PDO with locale "%s" and domain "%s"', count($messages), $locale, $domain);
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
