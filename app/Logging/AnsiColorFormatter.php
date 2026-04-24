<?php
namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\LogRecord;

class AnsiColorFormatter extends LineFormatter
{
    protected string $outputFormat;

    public function __construct()
    {
        $this->outputFormat = "[%datetime%] %level_name% : %message%\n";

        parent::__construct(
            $this->outputFormat,
            'd-m-Y H:i:s',
            true,
            true,
            false
        );
    }

    public function format(LogRecord $record): string
    {
        $context = $record->context;
        $extra   = $record->extra;

        unset($context['exception'], $context['trace']);
        unset($extra['exception'], $extra['trace']);

        $record = $record->with(
            context: $context,
            extra: $extra
        );

        $colorMap = [
            'DEBUG'     => '36',
            'INFO'      => '32',
            'NOTICE'    => '34',
            'WARNING'   => '33',
            'ERROR'     => '31',
            'CRITICAL'  => '35',
            'ALERT'     => '95',
            'EMERGENCY' => '41',
        ];

        $level     = $record->level->getName();
        $colorCode = $colorMap[$level] ?? '0';

        $line = parent::format($record);

        if (! empty($context)) {
            $line .= "\nData : " . json_encode(
                $context,
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE
            );
        }

        return "\033[{$colorCode}m{$line}\033[0m\n";
    }

}
