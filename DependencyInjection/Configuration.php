<?php

namespace YB\Bundle\RemoteTranslationsBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 * @package YB\Bundle\RemoteTranslationsBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * @inheritdoc
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();

        $rootNode = $treeBuilder->root('yb_remote_translations');

        $this->addApiSection($rootNode);
        $this->addAwsS3Section($rootNode);
        $this->addGooleSheetsSection($rootNode);
        $this->addPdoSection($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addApiSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('api')
                    ->children()
                        // loader
                        ->scalarNode('loader')->defaultValue('YB\\Bundle\\RemoteTranslationsBundle\\Translation\\Loader\\RemoteLoader')->end()

                        // client
                        ->arrayNode('client')
                            ->children()
                                ->scalarNode('endpoint')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('class')->defaultValue('GuzzleHttp\\Client')->end()
                                ->scalarNode('method')->defaultValue('GET')->end()
                                ->variableNode('auth')->defaultNull()->end()
                                ->variableNode('headers')->defaultNull()->end()
                            ->end()
                        ->end()

                        // logger
                        ->scalarNode('logger')->defaultValue('logger')->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addAwsS3Section(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('awss3')
                    ->children()
                        // loader
                        ->scalarNode('loader')->defaultValue('YB\\Bundle\\RemoteTranslationsBundle\\Translation\\Loader\\RemoteLoader')->end()

                        // client
                        ->arrayNode('client')
                            ->children()
                                ->scalarNode('bucket')->isRequired()->cannotBeEmpty()->end()
                                ->arrayNode('credentials')
                                    ->children()
                                        ->scalarNode('key')->isRequired()->cannotBeEmpty()->end()
                                        ->scalarNode('secret')->isRequired()->cannotBeEmpty()->end()
                                    ->end()
                                ->end()
                                ->scalarNode('region')->defaultValue('us-east-1')->end()
                                ->scalarNode('class')->defaultValue('Aws\\S3\\S3Client')->end()
                                ->scalarNode('version')->defaultValue('latest')->end()
                                ->scalarNode('filename')->defaultValue('%%domain%%.%%locale%%.csv')->end()
                            ->end()
                        ->end()

                        // logger
                        ->scalarNode('logger')->defaultValue('logger')->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addGooleSheetsSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('googlesheets')
                    ->children()
                        // loader
                        ->scalarNode('loader')->defaultValue('YB\\Bundle\\RemoteTranslationsBundle\\Translation\\Loader\\RemoteLoader')->end()

                        // client
                        ->arrayNode('client')
                            ->children()
                                ->scalarNode('spreadsheet_id')->isRequired()->cannotBeEmpty()->end()
                                ->scalarNode('credentials')->defaultValue('%kernel.root_dir%/../var/credentials/google/project.json')->end()
                                ->scalarNode('class')->defaultValue('Google_Service_Sheets')->end()
                                ->scalarNode('sheet_name_format')->defaultValue('%%domain%%.%%locale%%')->end()
                                ->scalarNode('sheet_range')->defaultValue('A1:B')->end()
                            ->end()
                        ->end()

                        // logger
                        ->scalarNode('logger')->defaultValue('logger')->end()
                    ->end()
                ->end()
            ->end();
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function addPdoSection(ArrayNodeDefinition $rootNode)
    {
        $rootNode
            ->children()
                ->arrayNode('pdo')
                    ->children()
                        // loader
                        ->scalarNode('loader')->defaultValue('YB\\Bundle\\RemoteTranslationsBundle\\Translation\\Loader\\RemoteLoader')->end()

                        // client
                        ->arrayNode('client')
                            ->addDefaultsIfNotSet()
                            ->children()
                                ->scalarNode('dsn')->defaultValue('mysql:host=%database_host%;port=%database_port%;dbname=%database_name%')->end()
                                ->scalarNode('user')->defaultValue('%database_user%')->end()
                                ->scalarNode('password')->defaultValue('%database_password%')->end()
                                ->scalarNode('class')->defaultValue('PDO')->end()
                                ->scalarNode('table')->defaultValue('translations')->end()
                                ->scalarNode('locale_col')->defaultValue('locale')->end()
                                ->scalarNode('domain_col')->defaultValue('domain')->end()
                                ->scalarNode('key_col')->defaultValue('key')->end()
                                ->scalarNode('value_col')->defaultValue('value')->end()
                            ->end()
                        ->end()

                        // logger
                        ->scalarNode('logger')->defaultValue('logger')->end()
                    ->end()
                ->end()
            ->end();
    }
}
