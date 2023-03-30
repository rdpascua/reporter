<?php

namespace Rdpascua\Reporter;

use Symfony\Component\HttpFoundation\StreamedResponse;

class Reporter
{
    public function __construct(protected JasperStarter $jasperStarter)
    {
    }

    public function load(string $filePath, array $parameters = []): self
    {
        $this->jasperStarter->load($filePath, $parameters);

        return $this;
    }

    public function withDataSource(string $connection, array $options = []): self
    {
        $this->jasperStarter->connection($connection, $options);

        return $this;
    }

    public function compile(string $output = null): self
    {
        $this->jasperStarter->compile($output);

        return $this;
    }

    public function stream(string $filename = 'document.pdf'): StreamedResponse
    {
        $tempFile = tempnam(sys_get_temp_dir(), 'reporter');

        $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);

        $output = $this->jasperStarter->process("$tempFile.$fileExtension");

        $stream = function () use ($output) {
            $handle = fopen($output, 'rb');
            while (! feof($handle)) {
                echo fread($handle, 8192);
                ob_flush();
                flush();
            }
            fclose($handle);
        };

        return new StreamedResponse($stream, 200, [
            'Content-Type' => $this->jasperStarter->getMime($fileExtension),
            'Content-Disposition' => "inline; filename=$filename",
        ]);
    }

    public function save(string $filename): self
    {
        $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);

        $this->jasperStarter->process("$filename.$fileExtension");

        return $this;
    }
}
