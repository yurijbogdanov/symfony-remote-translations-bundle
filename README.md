# Symfony bundle for using translations stored remotely (API, AWS S3, Google Sheets, PDO)


[![Build Status](https://travis-ci.org/yurijbogdanov/symfony-remote-translations-bundle.svg?branch=master)](https://travis-ci.org/yurijbogdanov/symfony-remote-translations-bundle)

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/9865c5f5-0fb0-45ea-ba18-b096e292648d/big.png)](https://insight.sensiolabs.com/projects/9865c5f5-0fb0-45ea-ba18-b096e292648d)


## Installation

The YBRemoteTranslationsBundle can be installed:

* via Terminal by executing command:
```bash
composer require yurijbogdanov/remote-translations-bundle
```

* via [Composer](http://getcomposer.org) by requiring the `yurijbogdanov/remote-translations-bundle` package in your project's `composer.json`:

```json
{
    "require": {
        "yurijbogdanov/remote-translations-bundle": "~1.0"
    }
}
```

and adding an instance of `YB\Bundle\RemoteTranslationsBundle\YBRemoteTranslationsBundle` to your application's kernel:

```php
class AppKernel extends Kernel
{
    public function registerBundles()
    {
        return [
            ...
            new YB\Bundle\RemoteTranslationsBundle\YBRemoteTranslationsBundle(),
        ];
    }
    ...
}
```


## Configuration

Minimum configurations `config.yml` with only required options:
```yaml
yb_remote_translations:
    api:
        client:
            endpoint: 'http://example.com/no-auth/translations/{domain}/{locale}/'

    awss3:
        client:
            region: 'my-region'
            bucket: 'my-translations-bucket'
            credentials:
                key: 'my-aws-s3-key'
                secret: 'my-aws-s3-secret'

    googlesheets:
        client:
            spreadsheet_id: 'my-google-spreadsheet-id'
            credentials: '%kernel.root_dir%/../var/credentials/google/my-google-project-credentials.json'

    pdo: ~
```


Full configurations `config.yml`:
```yaml
yb_remote_translations:
    api:
        loader:
            class: YB\Bundle\RemoteTranslationsBundle\Translation\Loader\ApiLoader
        client:
            endpoint: 'http://example.com/basic-auth/translations/{domain}/{locale}/'
            class: GuzzleHttp\Client
            method: 'GET'
            auth: ['my-username', 'my-password']
            headers:
                My-Custom-Header-Key: 'My-Custom-Header-Value'
        logger: logger

    awss3:
        loader:
            class: YB\Bundle\RemoteTranslationsBundle\Translation\Loader\AwsS3Loader
        client:
            region: 'my-region'
            bucket: 'my-translations-bucket'
            credentials:
                key: 'my-aws-s3-key'
                secret: 'my-aws-s3-secret'
            class: Aws\S3\S3Client
            version: 'latest'
            file_name_format: '{domain}.{locale}.csv'
        logger: logger

    googlesheets:
        loader:
            class: YB\Bundle\RemoteTranslationsBundle\Translation\Loader\GoogleSheetsLoader
        client:
            spreadsheet_id: 'my-google-spreadsheet-id'
            credentials: '%kernel.root_dir%/../var/credentials/google/my-google-project-credentials.json'
            class: Google_Service_Sheets
            sheet_name_format: '{domain}.{locale}'
            sheet_range: 'A1:B'
        logger: logger

    pdo:
        loader:
            class: YB\Bundle\RemoteTranslationsBundle\Translation\Loader\PdoLoader
        client:
            dsn: 'mysql:host=%database_host%;port=%database_port%;dbname=%database_name%'
            user: '%database_user%'
            password: '%database_password%'
            class: PDO
            table: 'translations'
            locale_col: 'locale'
            domain_col: 'domain'
            key_col: 'key'
            value_col: 'value'
        logger: logger
```


## Usage

Create empty files in your Resources/translations/ folder using format %domain%.%locale%.%loader%:
* for using API create new file with extension `.api` (e.g. messages.en.api)
* for using AWS S3 create new file with extension `.awss3` (e.g. messages.en.awss3)
* for using Google Sheets create new file with extension `.googlesheets` (e.g. messages.en.googlesheets)
* for using PDO create new file with extension `.pdo` (e.g. messages.en.pdo)


### ApiLoader
Dependencies:
* [Guzzle, PHP HTTP client](https://github.com/guzzle/guzzle)


### AwsS3Loader
Dependencies:
* [AWS SDK for PHP](https://github.com/aws/aws-sdk-php) 


### GoogleSheetsLoader
Dependencies:
* [Google APIs Client Library for PHP](https://github.com/google/google-api-php-client)


### PdoLoader
Dependencies:

##### MySQL
```mysql
CREATE TABLE translations (
    id INTEGER UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    locale VARCHAR(128) NOT NULL,
    domain VARCHAR(128) NOT NULL,
    key VARCHAR(128) NOT NULL,
    value TEXT NOT NULL
);
```

##### PostgreSQL
```mysql
CREATE TABLE translations (
  id SERIAL NOT NULL,
  locale VARCHAR(128) NOT NULL,
  domain VARCHAR(128) NOT NULL,
  key VARCHAR(128) NOT NULL,
  value TEXT NOT NULL
);
```
