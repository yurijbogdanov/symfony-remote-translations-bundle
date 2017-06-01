<?php

namespace YB\Bundle\RemoteTranslationsBundle\Translation\Loader\Client;

/**
 * Interface ClientAdapterInterface
 * @package YB\Bundle\RemoteTranslationsBundle\Translation\Loader\Client
 */
interface ClientAdapterInterface
{
    /**
     * @param $locale
     * @param $domain
     * @return array
     */
    public function load($locale, $domain);
}
