<?php

namespace YB\Bundle\RemoteTranslationsBundle\Translation\Loader\Client\Adapter;

use Aws\Result as AwsResult;
use Aws\S3\S3Client as AwsS3Client;
use Aws\S3\S3ClientInterface as AwsS3ClientInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use YB\Bundle\RemoteTranslationsBundle\Translation\Loader\Client\ClientAdapterInterface;
use YB\Bundle\RemoteTranslationsBundle\Translation\Exception\InvalidOptionException;

/**
 * Class AwsS3Adapter
 * @package YB\Bundle\RemoteTranslationsBundle\Translation\Loader\Client\Adapter
 */
class AwsS3Adapter implements ClientAdapterInterface
{
    /**
     * @var AwsS3ClientInterface|AwsS3Client
     */
    private $client;

    /**
     * @var string
     */
    private $bucket;

    /**
     * @var string
     */
    private $filename;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param AwsS3ClientInterface $client
     * @param array $options
     * @throws InvalidOptionException
     */
    public function __construct(AwsS3ClientInterface $client, array $options = [])
    {
        $this->client = $client;

        $this->bucket = isset($options['bucket']) ? $options['bucket'] : $this->bucket;
        $this->filename = isset($options['filename']) ? $options['filename'] : $this->filename;

        if (empty($this->bucket)) {
            throw new InvalidOptionException('Option "bucket" is required');
        }

        if (empty($this->filename)) {
            throw new InvalidOptionException('Option "filename" is required');
        }
    }

    /**
     * @inheritdoc
     */
    public function load($locale, $domain)
    {
        $messages = [];

        try {
            $key = str_replace(['%locale%', '%domain%'], [$locale, $domain], $this->filename);

            /** @var AwsResult $result */
            $result = $this->client->getObject([
                'Bucket' => $this->bucket,
                'Key' => $key,
            ]);

            $data = (string)$result['Body'];
            if ($data) {
                $temp = new \SplTempFileObject();
                $temp->fwrite($data);
                $temp->setFlags(\SplFileObject::READ_CSV | \SplFileObject::SKIP_EMPTY | \SplFileObject::READ_AHEAD);
                foreach ($temp as $row) {
                    $messages[$row[0]] = $row[1];
                }
            }

            if ($this->logger) {
                $msg = sprintf('%d messages were uploaded via AWS S3 with locale "%s" and domain "%s"', count($messages), $locale, $domain);
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
