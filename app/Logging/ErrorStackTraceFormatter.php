<?php
namespace App\Logging;

use Monolog\Formatter\LineFormatter;
use Monolog\LogRecord;
use Throwable;

class ErrorStackTraceFormatter extends LineFormatter
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
        try {
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

            $exception = $record->context['exception'] ?? null;

            if ($exception instanceof Throwable) {
                $line .= $this->formatException($exception);
            }

            return "\033[" . $colorCode . "m" . $line . "\033[0m\n";

        } catch (Throwable $e) {
            return "[LOGGER ERROR] " . $e->getMessage() . "\n";
        }
    }

    protected function formatException(Throwable $e, int $depth = 0): string
    {
        $indent = str_repeat('  ', $depth);

        $class = $this->shortClass(get_class($e));

        $output = "\n{$indent}Exception:\n";
        $output .= sprintf(
            "%s%s: %s\n",
            $indent,
            $class,
            $e->getMessage()
        );

        $output .= sprintf(
            "%sin %s:%d\n",
            $indent,
            $this->shortPath($e->getFile()),
            $e->getLine()
        );

        $output .= "{$indent}Stack trace (top 5):\n";

        foreach (array_slice($e->getTrace(), 0, 5) as $i => $frame) {
            $output .= sprintf(
                "%s#%d %s:%s %s()\n",
                $indent,
                $i,
                isset($frame['file']) ? $this->shortPath($frame['file']) : 'N/A',
                $frame['line'] ?? 'N/A',
                $frame['function'] ?? 'N/A'
            );
        }

        if ($e->getPrevious()) {
            $output .= "\n{$indent}Caused by:\n";
            $output .= $this->formatException($e->getPrevious(), $depth + 1);
        }

        return $output;
    }

    protected function shortClass(string $class): string
    {
        return basename(str_replace('\\', '/', $class));
    }

    protected function shortPath(string $path): string
    {
        $basePath = base_path();

        if (str_starts_with($path, $basePath)) {
            return str_replace($basePath . DIRECTORY_SEPARATOR, '', $path);
        }

        if (str_contains($path, 'vendor')) {
            return 'vendor/' . basename($path);
        }

        return $path;
    }
}
