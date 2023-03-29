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

    protected array $data = [];

    public function __construct(protected string $binary, protected string $jdbcPath, protected array $connection = [])
    {
        if (! file_exists($binary)) {
            $this->binary = __DIR__.'/../bin/jasperstarter/bin/jasperstarter';
        }

        if (! file_exists($jdbcPath)) {
            $this->jdbcPath = __DIR__.'/../bin/jasperstarter/jdbc';
        }
    }

    public function load(string $filePath, array $data = []): self
    {
        $this->file = $filePath;
        $this->data = $data;

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

       $command = $this->createCommand('process', $options);
       $this->runCommand($command);

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
                   $command = array_merge($command, ['-o', $output]);
               }
               break;
           case 'process':
               $command = array_merge($command, [
                   $this->file,
                   '-f',
                   $options['format'],
                   '-o',
                   $options['output'],
                   '--jdbc-dir',
                   $this->jdbcPath,
               ]);

               if (count($this->data)) {
                   $command[] = '-P';

                   foreach ($this->data as $key => $value) {
                       $command[] = "$key=$value";
                   }
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
