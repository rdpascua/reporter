<?php

namespace Rdpascua\Reporter;

use Exception;
use InvalidArgumentException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class JasperStarter
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string, string>
     */
    protected $mimes = [
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'html' => 'text/html',
        'xhtml' => 'application/xhtml+xml',
        'csv' => 'text/csv',
        'pdf' => 'application/pdf',
        'xml' => 'application/xml',
    ];

    protected array $parameters = [];

    protected array $connection = [];

    public function __construct(protected ?string $binary = null, protected ?string $jdbcPath = null, protected array $connections = [], protected ?string $resourcePath = null)
    {
        if (! file_exists($binary)) {
            $this->binary = __DIR__.'/../bin/jasperstarter/bin/jasperstarter';
        }

        if (! file_exists($jdbcPath)) {
            $this->jdbcPath = __DIR__.'/../bin/jasperstarter/jdbc';
        }
    }

    public function load(string $filePath, array $parameters = []): self
    {
        $this->file = $filePath;
        $this->parameters = $parameters;

        return $this;
    }

    public function connection(string $connection, array $options = []): self
    {
        if (! array_key_exists($connection, $this->connections)) {
            throw new InvalidArgumentException("Invalid connection: {$connection}. Please provide a valid connection.");
        }

        $this->connection = $this->connections[$connection];

        return $this;
    }

    public function compile(string $output = null): self
    {
        $command = $this->createCommand('compile', [
            'input' => $this->file,
            'output' => $output,
        ]);

        $this->runCommand($command);

        return $this;
    }

    public function getMime(string $fileExtension): string
    {
        return $this->mimes[$fileExtension];
    }

    private function getDriver(): string
    {
        return match ($this->connection['driver']) {
            'pgsql' => 'postgres',
            'mysql' => 'mysql',
            default => 'generic',
        };
    }

    public function process(string $output, array $options = []): string
    {
        $fileExtension = pathinfo($output, PATHINFO_EXTENSION);

        if (! array_key_exists($fileExtension, $this->mimes)) {
            throw new InvalidArgumentException("Invalid format: {$fileExtension}. Please provide a valid format.");
        }

        $options = array_merge($options, [
            'output' => str_replace(".{$fileExtension}", '', $output),
            'format' => $fileExtension,
        ]);

        $this->runCommand($this->createCommand('process', $options));

        return $output;
    }

    private function createCommand(string $action, array $options): array
    {
        $command = [$this->binary, $action];

        switch ($action) {
            case 'compile':
                $command[] = $options['input'];

                if ($options['output']) {
                    $output = str_replace('.jasper', '', $options['output']);
                    $command = array_merge($command, [
                        // '--jdbc-dir',
                        // $this->jdbcPath,
                        '-o',
                        $output
                    ]);
                }
                break;
            case 'process':
                $command = array_merge($command, [
                    $this->file,
                    '-f',
                    $options['format'],
                    '--jdbc-dir',
                    $this->jdbcPath,
                    '-o',
                    $options['output'],
                    '-r'
                ]);

                if (count($this->parameters)) {
                    $command[] = '-P';

                    foreach ($this->parameters as $key => $value) {
                        $command[] = "$key=$value";
                    }
                }

                if (count($this->connection)) {
                    $command = array_merge($command, [
                        '-t',
                        $this->getDriver(),
                        '-u',
                        $this->connection['username'],
                        '-p',
                        $this->connection['password'],
                        '-H',
                        $this->connection['host'],
                        '-n',
                        $this->connection['database'],
                        '--db-port',
                        $this->connection['port'],
                    ]);
                }

                break;
        }

        return $command;
    }

    private function runCommand(array $command): void
    {
        $process = new Process($command);

        try {
            $process->mustRun();
        } catch (ProcessFailedException $exception) {
            throw new Exception($exception->getMessage());
        }
    }
}
