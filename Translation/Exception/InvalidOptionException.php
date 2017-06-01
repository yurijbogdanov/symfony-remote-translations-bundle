<?php

namespace YB\Bundle\RemoteTranslationsBundle\Translation\Exception;

use Symfony\Component\Translation\Exception\ExceptionInterface;

/**
 * Class InvalidOptionException
 * @package YB\Bundle\RemoteTranslationsBundle\Translation\Exception
 */
class InvalidOptionException extends \InvalidArgumentException implements ExceptionInterface
{
}
