<?php
declare(strict_types=1);

namespace CakePhp2IdeHelper\Exception;

use Throwable;

class FailedUpdatingPhpDocException extends \RuntimeException
{
    private $filePath;

    public function __construct(string $filePath, string $message = '', int $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->filePath = $filePath;
    }

    public function __toString(): string
    {
        return 'Updating PHPDoc is failed: ' . $this->filePath;
    }
}
