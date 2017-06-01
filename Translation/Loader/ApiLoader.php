<?php

namespace YB\Bundle\RemoteTranslationsBundle\Translation\Loader;

use Symfony\Component\Translation\Loader\LoaderInterface;
use Symfony\Component\Translation\MessageCatalogue;
use Symfony\Component\Translation\Exception\InvalidResourceException;
use Symfony\Component\Translation\Exception\NotFoundResourceException;
use YB\Bundle\RemoteTranslationsBundle\Translation\Loader\Client\ClientAdapterInterface;

/**
 * Class ApiLoader
 * @package YB\Bundle\RemoteTranslationsBundle\Translation\Loader
 */
class ApiLoader extends RemoteLoader
{
//    /**
//     * @var ClientAdapterInterface
//     */
//    private $client;
//
//    /**
//     * @param ClientAdapterInterface $client
//     */
//    public function __construct(ClientAdapterInterface $client)
//    {
//        $this->client = $client;
//    }
//
//    /**
//     * Loads a locale.
//     *
//     * @param mixed  $resource A resource
//     * @param string $locale   A locale
//     * @param string $domain   The domain
//     *
//     * @return MessageCatalogue A MessageCatalogue instance
//     *
//     * @throws NotFoundResourceException when the resource cannot be found
//     * @throws InvalidResourceException  when the resource cannot be loaded
//     */
//    public function load($resource, $locale, $domain = 'messages')
//    {
//        $catalogue = new MessageCatalogue($locale);
//
//        /** @var array $messages */
//        $messages = $this->client->load($locale, $domain);
//
//        if ($messages) {
//            $catalogue->add($messages, $domain);
//        }
//
//        return $catalogue;
//    }
}
