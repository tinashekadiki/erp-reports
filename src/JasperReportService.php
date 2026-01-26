<?php

namespace Nexterp\JasperReports;

use PHPJasper\PHPJasper;
use Illuminate\Support\Facades\Config;

class JasperReportService
{
    protected $jasper;

    public function __construct()
    {
        $this->jasper = new PHPJasper();
    }

    /**
     * Generate a Jasper Report
     *
     * @param string $input Path to .jrxml or .jasper file
     * @param string $output Output path (without extension)
     * @param array $format Output formats
     * @param array $parameters Parameters to pass to the report
     * @param array $dbConnection Database connection details
     * @return string Path to the generated file
     */
    public function generateReport($input, $output, $format = ['pdf'], $parameters = [], $dbConnection = [])
    {
        $options = [
            'format' => $format,
            'locale' => 'en',
            'params' => $parameters,
            'db_connection' => $dbConnection ?: $this->getDbConnection()
        ];

        $this->jasper->process($input, $output, $options)->execute();

        return $output . '.' . $format[0];
    }

    /**
     * Get database connection details from Laravel config
     *
     * @return array
     */
    protected function getDbConnection()
    {
        $connection = Config::get('database.default');
        $config = Config::get("database.connections.{$connection}");

        return [
            'driver' => $config['driver'] === 'mariadb' ? 'mysql' : $config['driver'],
            'username' => $config['username'],
            'password' => $config['password'],
            'host' => $config['host'],
            'database' => $config['database'],
            'port' => $config['port'],
        ];
    }

    /**
     * Compile a .jrxml file to .jasper
     *
     * @param string $input Path to .jrxml file
     * @return void
     */
    public function compile($input)
    {
        $this->jasper->compile($input)->execute();
    }
}
