<?php

use PHPUnit\Framework\TestCase;
use Laboratory\Reporter\Reporter;
use Laboratory\Reporter\JasperStarter;
use Symfony\Component\HttpFoundation\Response;

class ReporterTest extends TestCase
{
    protected $jasperStarter;
    protected $reporter;

    public function setUp()
    {
        parent::setUp();

        $this->jasperStarter = new JasperStarter(
            __DIR__ . '/../vendor/bin/jasperstarter',
            __DIR__ . '/../vendor/rdpascua/jasperstarter/jdbc',
            __DIR__ . '/../stubs',
            []
        );

        $this->reporter = new Reporter($this->jasperStarter);
    }

    public function testShouldGenerateTemporaryFile()
    {
        $file = $this
            ->jasperStarter
            ->load('Basic', [], true)
            ->exec('sample.pdf');

        $this->assertTrue(file_exists($file['tempFile']));
    }

    public function testShouldRespond()
    {
        $reporter = $this->reporter->load('Basic', [], true);

        $this->assertInstanceOf(Response::class, $reporter->inline('sample.pdf'));
        $this->assertInstanceOf(Response::class, $reporter->download('sample.pdf'));
    }

    public function testShouldAcceptParameter()
    {
        $file = $this->jasperStarter->load('BasicWithParameter', [
            'Parameter1' => 'Somebody touch my spaget'
        ], true)
        ->exec('sample.pdf');

        $this->assertTrue(file_exists($file['tempFile']));
    }
}
