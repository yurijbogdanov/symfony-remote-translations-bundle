<?php

namespace YB\Bundle\RemoteTranslationsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class YBRemoteTranslationsExtension
 * @package YB\Bundle\RemoteTranslationsBundle\DependencyInjection
 */
class YBRemoteTranslationsExtension extends Extension
{
    /**
     * @inheritdoc
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        $config = $processor->processConfiguration($configuration, $configs);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));

        $this->registerApiConfiguration($config, $container, $loader);
        $this->registerAwsS3Configuration($config, $container, $loader);
        $this->registerGoogleSheetsConfiguration($config, $container, $loader);
        $this->registerPdoConfiguration($config, $container, $loader);
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @param LoaderInterface $loader
     */
    private function registerApiConfiguration(array $config, ContainerBuilder $container, LoaderInterface $loader)
    {
        if (isset($config['api'])) {
            $container->setParameter('yb_remote_translations.api.loader.class', $config['api']['loader']);

            $container->setAlias('yb_remote_translations.api.logger', $config['api']['logger']);

            $container->setParameter('yb_remote_translations.api.client.endpoint', $config['api']['client']['endpoint']);
            $container->setParameter('yb_remote_translations.api.client.class', $config['api']['client']['class']);
            $container->setParameter('yb_remote_translations.api.client.method', $config['api']['client']['method']);
            $container->setParameter('yb_remote_translations.api.client.auth', $config['api']['client']['auth']);
            $container->setParameter('yb_remote_translations.api.client.headers', $config['api']['client']['headers']);

            $loader->load('api.yml');
        }
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @param LoaderInterface $loader
     */
    private function registerAwsS3Configuration(array $config, ContainerBuilder $container, LoaderInterface $loader)
    {
        if (isset($config['awss3'])) {
            $container->setParameter('yb_remote_translations.awss3.loader.class', $config['awss3']['loader']);

            $container->setAlias('yb_remote_translations.awss3.logger', $config['awss3']['logger']);

            $container->setParameter('yb_remote_translations.awss3.client.region', $config['awss3']['client']['region']);
            $container->setParameter('yb_remote_translations.awss3.client.bucket', $config['awss3']['client']['bucket']);
            $container->setParameter('yb_remote_translations.awss3.client.credentials', $config['awss3']['client']['credentials']);
            $container->setParameter('yb_remote_translations.awss3.client.class', $config['awss3']['client']['class']);
            $container->setParameter('yb_remote_translations.awss3.client.version', $config['awss3']['client']['version']);
            $container->setParameter('yb_remote_translations.awss3.client.filename', $config['awss3']['client']['filename']);

            $loader->load('awss3.yml');
        }
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @param LoaderInterface $loader
     */
    private function registerGoogleSheetsConfiguration(array $config, ContainerBuilder $container, LoaderInterface $loader)
    {
        if (isset($config['googlesheets'])) {
            $container->setParameter('yb_remote_translations.googlesheets.loader.class', $config['googlesheets']['loader']);

            $container->setAlias('yb_remote_translations.googlesheets.logger', $config['googlesheets']['logger']);

            $container->setParameter('yb_remote_translations.googlesheets.client.spreadsheet_id', $config['googlesheets']['client']['spreadsheet_id']);
            $container->setParameter('yb_remote_translations.googlesheets.client.credentials', $config['googlesheets']['client']['credentials']);
            $container->setParameter('yb_remote_translations.googlesheets.client.class', $config['googlesheets']['client']['class']);
            $container->setParameter('yb_remote_translations.googlesheets.client.sheet_name_format', $config['googlesheets']['client']['sheet_name_format']);
            $container->setParameter('yb_remote_translations.googlesheets.client.sheet_range', $config['googlesheets']['client']['sheet_range']);

            $loader->load('googlesheets.yml');
        }
    }

    /**
     * @param array $config
     * @param ContainerBuilder $container
     * @param LoaderInterface $loader
     */
    private function registerPdoConfiguration(array $config, ContainerBuilder $container, LoaderInterface $loader)
    {
        if (isset($config['pdo'])) {
            $container->setParameter('yb_remote_translations.pdo.loader.class', $config['pdo']['loader']);

            $container->setAlias('yb_remote_translations.pdo.logger', $config['pdo']['logger']);

            $container->setParameter('yb_remote_translations.pdo.client.dsn', $config['pdo']['client']['dsn']);
            $container->setParameter('yb_remote_translations.pdo.client.user', $config['pdo']['client']['user']);
            $container->setParameter('yb_remote_translations.pdo.client.password', $config['pdo']['client']['password']);
            $container->setParameter('yb_remote_translations.pdo.client.class', $config['pdo']['client']['class']);
            $container->setParameter('yb_remote_translations.pdo.client.table', $config['pdo']['client']['table']);
            $container->setParameter('yb_remote_translations.pdo.client.locale_col', $config['pdo']['client']['locale_col']);
            $container->setParameter('yb_remote_translations.pdo.client.domain_col', $config['pdo']['client']['domain_col']);
            $container->setParameter('yb_remote_translations.pdo.client.key_col', $config['pdo']['client']['key_col']);
            $container->setParameter('yb_remote_translations.pdo.client.value_col', $config['pdo']['client']['value_col']);

            $loader->load('pdo.yml');
        }
    }
}
