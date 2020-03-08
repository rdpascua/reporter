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
    protected $jdbcPath;

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
     * [$command description]
     * @var [type]
     */
    protected $command = [];

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
    public function __construct($binary, $jdbcPath, $resource, $connections = [], $connection = 'pgsql')
    {
        if (!file_exists($binary)) {
            throw new InvalidArgumentException('Binary path is invalid.');
        }

        if (!file_exists($jdbcPath)) {
            throw new InvalidArgumentException('JDBC path is invalid.');
        }

        $this->binary = $binary;
        $this->jdbcPath = $jdbcPath;
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
    public function load($file, $data = [], $standalone = false)
    {
        $this->tempFile = tempnam(sys_get_temp_dir(), 'REPORTER');
        $this->reportFile = "{$this->resource}/{$file}";
        $this->data = $data;
        $this->standalone = $standalone;

        return $this;
    }

    /**
     * Returns the temporary report file
     *
     * @return string
     */
    protected function getReportFile()
    {
        return $this->reportFile;
    }

    /**
     * Returns the binary path
     *
     * @return [type] [description]
     */
    protected function getBinaryPath()
    {
        return $this->binary;
    }

    /**
     * Returns the jdbc path
     *
     * @return [type] [description]
     */
    protected function getJdbcPath()
    {
        return $this->jdbcPath;
    }

    /**
     * Returns the data parameters
     *
     * @return [type] [description]
     */
    protected function getDataParameters()
    {
        $this->command[] = '-P';

        foreach($this->data as $key => $value) {
            if (is_array($value))  {
                $value = implode(',', $value);
            }

            if (strpos($value, ' ') !== false) {
                $this->command[] = "{$key}=\"{$value}\"";
                continue;
            }

            $this->command[] = "{$key}={$value}";
        }
    }

    /**
     * Returns connection parameter to be executed
     * @return [type] [description]
     */
    protected function getConnectionParameters()
    {
        $connection = $this->connections[$this->connection];

        if ($this->connection) {
            $this->command = array_merge($this->command, [
                '-t',
                $connection['driver'],
                '-u',
                $connection['username'],
                '-p',
                $connection['password'],
                '-H',
                $connection['host'],
                '-n',
                $connection['database'],
                '--db-port',
                $connection['port']
            ]);
        }
    }

    /**
     * Execute the jasperstarter command
     *
     * @param  string $command
     */
    public function exec($filename)
    {
        $fileinfo = $this->getFileInfo($filename);

        $this->command = [
            $this->binary,
            'process',
            $this->reportFile,
            '-f',
            $fileinfo['ext'],
            '--jdbc-dir',
            $this->jdbcPath,
            '-o',
            $this->tempFile,
            '-r'
        ];

        if (!$this->standalone) {
            $this->getConnectionParameters();
        }

        if (count($this->data)) {
            $this->getDataParameters();
        }

        $process = new Process($this->command);
        $process->run();

        if (!$process->isSuccessful())  {
            throw new Exception($process->getErrorOutput());
        }

        return $fileinfo;
    }
}