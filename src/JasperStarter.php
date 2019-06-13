<?php

namespace Laboratory\Reporter;

use Exception;
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
     * string
     * @var [type]
     */
    protected $tempFile;

    /**
     * string
     * @var [type]
     */
    protected $reportFile;

    /**
     * @var string
     */
    protected $resource;

    /**
     * @var array
     */
    protected $connections = [];

    /**
     * [$connection description]
     * @var [type]
     */
    protected $connection;

    /**
     * [$overrideConnection description]
     * @var boolean
     */
    protected $overrideConnection = false;

    /**
     * [$connection description]
     * @var [type]
     */
    protected $data = [];

    /**
     * [$connection description]
     * @var [type]
     */
    protected $dataParameters;

    /**
     * [$allowedMimes description]
     * @var [type]
     */
    protected $allowedMimes = [
        'xls' => 'application/vnd.ms-excel',
        'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        'odt' => 'application/vnd.oasis.opendocument.text',
        'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
        'html' => 'text/html',
        'pdf' => 'application/pdf'
    ];

    /**
     * New instance of JasperStarter

     * @param string $binary
     * @param array  $connection
     */
    public function __construct($binary, $resource, $connections = [], $connection = 'pgsql')
    {
        if (!file_exists($binary)) {
            throw new InvalidArgumentException('Binary path is invalid.');
        }

        $this->binary = $binary;
        $this->resource = $resource;
        $this->connection = $connection;
        $this->connections = $connections;
    }

    /**
     * [connection description]
     * @param  [type] $connection [description]
     * @return [type]             [description]
     */
    public function connection($connection = null)
    {
        if ($connection) {
            $this->connection = $connection;
        }

        return $this;
    }

    /**
     * Returns the appropriate file extension and mime type
     *
     * @param  string $filename
     * @return string
     */
    private function getFileInfo($filename)
    {
        [$basename, $extension] = explode('.', $filename);

        return [
            'ext' => $extension,
            'filename' => $filename,
            'tempFile' => implode('.', [$this->tempFile, $extension]),
            'mimeType' => $this->allowedMimes[$extension]
        ];
    }

    /**
     * Load the .jasper file

     * @param  string $file
     * @param  array  $data
     * @return string
     */
    public function load($file, $data = [], $overrideConnection = false)
    {
        $this->tempFile = tempnam(sys_get_temp_dir(), 'REPORTER');
        $this->reportFile = "{$this->resource}/{$file}";
        $this->data = $data;
        $this->overrideConnection = $overrideConnection;

        return $this;
    }

    /**
     * Returns the data parameters
     *
     * @return [type] [description]
     */
    protected function getDataParameters($command)
    {
        if (count($this->data)) {
            $command = ' -P';

            foreach($this->data as $key => $value) {
                if (is_array($value))  {
                    $value = implode(',', $value);
                }

                if (strpos($value, ' ') !== false) {
                    $command .= " {$key}=\"{$value}\"";
                    continue;
                }

                $command .= " {$key}={$value}";
            }

            return $command;
        }

        return false;
    }

    /**
     * Returns connection parameter to be executed
     * @return [type] [description]
     */
    protected function getConnectionParameters()
    {
        $connection = $this->connections[$this->connection];

        if ($this->connection && $this->overrideConnection) {
            return sprintf(' -t %s -u %s -p %s -H %s -n %s --db-port %s',
                $connection['driver'],
                $connection['username'],
                $connection['password'],
                $connection['host'],
                $connection['database'],
                $connection['port']
            );
        }

        return false;
    }

    /**
     * Execute the jasperstarter command
     *
     * @param  string $command
     */
    public function exec($filename)
    {
        $fileinfo = $this->getFileInfo($filename);

        $command = sprintf('%s process %s -f %s -o %s -r',
            $this->binary,
            $this->reportFile,
            $fileinfo['ext'],
            $this->tempFile,
            $this->resource
        );

        $command .= $this->getConnectionParameters();
        $command .= $this->getDataParameters($command);

        $process = new Process($command);
        $process->run();

        if (!$process->isSuccessful())  {
            throw new Exception($process->getErrorOutput());
        }

        return $fileinfo;
    }
}