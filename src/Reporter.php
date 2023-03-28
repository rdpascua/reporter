<?php

namespace Reporter;

use SplFileInfo;
use Symfony\Component\HttpFoundation\Response;

class Reporter
{
    /**
     * @var Reporter\JasperStarter
     */
    protected $jasperStarter;

    /**
     * @var string
     */
    protected $report;

    /**
     * [$connection description]
     *
     * @var [type]
     */
    protected $connection;

    /**
     * Create an instance of Reporter
     */
    public function __construct(JasperStarter $jasperStarter)
    {
        $this->jasperStarter = $jasperStarter;
    }

    /**
     * Load the file to jasperstarter
     *
     * @param  string  $file
     * @param  array  $data
     * @return $this
     */
    public function load($filename, $data = [], $standalone = false)
    {
        $info = new SplFileInfo($filename);

        if ($info->getExtension() !== 'jasper') {
            $filename .= '.jasper';
        }

        $this
            ->jasperStarter
            ->connection($this->connection)
            ->load($filename, $data, $standalone);

        return $this;
    }

    /**
     * [connection description]
     *
     * @return [type] [description]
     */
    public function connection($connection)
    {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Download the generated file
     *
     * @param  string  $name
     * @return  \Illuminate\Http\Response
     */
    public function download($filename)
    {
        $fileinfo = $this->jasperStarter->exec($filename);

        return new Response(file_get_contents($fileinfo['tempFile']), 200, [
            'Content-Type' => $fileinfo['mimeType'],
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /**
     * Stream the generated file in the browser
     *
     * @param  string  $name
     * @return  \Illuminate\Http\Response
     */
    public function inline($filename)
    {
        $fileinfo = $this->jasperStarter->exec($filename);

        return new Response(file_get_contents($fileinfo['tempFile']), 200, [
            'Content-Type' => $fileinfo['mimeType'],
            'Content-Disposition' => 'inline; filename="'.$filename.'"',
        ]);
    }

    /**
     * Save the generated file to disk
     *
     * @param  string  $name
     * @return bool
     */
    public function save($filename)
    {
        $fileinfo = $this->jasperStarter->exec($filename);

        if (rename($fileinfo['tempFile'], $fileinfo['filename'])) {
            return $fileinfo['filename'];
        }

        throw new Exception(sprintf('Unable to write on directory %s', $$fileinfo['filename']));
    }
}
