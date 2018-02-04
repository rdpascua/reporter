<?php

namespace Laboratory\Reporter;

use InvalidArgumentException;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class JasperStarter
{
    /**
     * string
     * @var [type]
     */
    protected $binary;

    /**
     * @var string
     */
    protected $resource;

    /**
     * @var array
     */
    protected $connection = [];

    /**
     * New instance of JasperStarter

     * @param string $binary
     * @param array  $connection
     */
    public function __construct($binary, $resource, $connection = [])
    {
        if (!file_exists($binary)) {
            throw new InvalidArgumentException('Binary path is invalid.');
        }

        $this->binary = $binary;
        $this->resource = $resource;
        $this->connection = $connection;

    }

    /**
     * Load the .jasper file

     * @param  string $file
     * @param  array  $data
     * @return string
     */
    public function load($file, $data = [])
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'REPORTER');
        $file = "{$this->resource}/{$file}";

        $command = sprintf('%s process %s -f pdf -o %s -r',
            $this->binary,
            $file,
            $tempFile,
            $this->resource
        );

        if (count($this->connection)) {
            $command .= sprintf(' -t %s -u %s -p %s -H %s -n %s --db-port %s',
                $this->connection['driver'],
                $this->connection['username'],
                $this->connection['password'],
                $this->connection['host'],
                $this->connection['database'],
                $this->connection['port']
            );
        }

        if (count($data)) {
            $command .= ' -P';

            foreach($data as $key => $value) {
                if (is_array($value))  {
                    $value = implode(',', $value);
                }

                if (strpos($value, ' ') !== false) {
                    $command .= " {$key}=\"{$value}\"";
                    continue;
                }

                $command .= " {$key}={$value}";
            }
        }

        $this->exec($command);

        return $tempFile;
    }

    /**
     * Execute the jasperstarter command
     *
     * @param  string $command
     */
    protected function exec($command)
    {
        $process = new Process($command);

        $process->run();

        if (!$process->isSuccessful())  {
            throw new ProcessFailedException($process);
        }
    }
}