<?php

namespace YB\Bundle\RemoteTranslationsBundle\Translation\Loader\Client\Adapter;

use Exception;
use Google_Service_Sheets;
use Google_Service_Sheets_ValueRange;
use Psr\Log\LoggerInterface;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use YB\Bundle\RemoteTranslationsBundle\Translation\Loader\Client\ClientAdapterInterface;
use YB\Bundle\RemoteTranslationsBundle\Translation\Exception\InvalidOptionException;

/**
 * Class GoogleSheetsAdapter
 * @package YB\Bundle\RemoteTranslationsBundle\Translation\Loader\Client\Adapter
 */
class GoogleSheetsAdapter implements ClientAdapterInterface
{
    /**
     * @var Google_Service_Sheets
     */
    protected $client;

    /**
     * @var string
     */
    protected $spreadsheetId;

    /**
     * @var string
     */
    protected $sheetNameFormat;

    /**
     * @var string
     */
    protected $sheetRange;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param Google_Service_Sheets $client
     * @param array $options
     * @throws InvalidOptionException
     */
    public function __construct(Google_Service_Sheets $client, array $options = [])
    {
        $this->client = $client;

        $this->spreadsheetId = isset($options['spreadsheet_id']) ? $options['spreadsheet_id'] : $this->spreadsheetId;
        $this->sheetNameFormat = isset($options['sheet_name_format']) ? $options['sheet_name_format'] : $this->sheetNameFormat;
        $this->sheetRange = isset($options['sheet_range']) ? $options['sheet_range'] : $this->sheetRange;

        if (empty($this->spreadsheetId)) {
            throw new InvalidOptionException('Option "spreadsheet_id" is required');
        }

        if (empty($this->sheetNameFormat)) {
            throw new InvalidOptionException('Option "sheet_name_format" is required');
        }

        if (empty($this->sheetRange)) {
            throw new InvalidOptionException('Option "sheet_range" is required');
        }
    }

    /**
     * @inheritdoc
     */
    public function load($locale, $domain)
    {
        $messages = [];

        try {
            $spreadsheetName = str_replace(['%locale%', '%domain%'], [$locale, $domain], $this->sheetNameFormat);
            $range = $this->sheetRange ? $spreadsheetName . '!' . $this->sheetRange : $spreadsheetName;

            /** @var Google_Service_Sheets_ValueRange $response */
            $response = $this->client->spreadsheets_values->get($this->spreadsheetId, $range);

            /** @var array $data */
            $data = $response->getValues();
            if ($data) {
                $messages = array_column($data, 1, 0);
            }

            if ($this->logger) {
                $msg = sprintf('%d messages were uploaded via Google Sheets with locale "%s" and domain "%s"', count($messages), $locale, $domain);
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
