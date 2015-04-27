<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Console;

use App;
use ErrorException;
use Illuminate\Support\ServiceProvider as BaseServiceProvider;

/**
 * Provides command registration functionality
 *
 * @package        Laradic\Console
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
abstract class AggregateConsoleProvider extends BaseServiceProvider
{


    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * The namespace where the commands are
     *
     * @var string
     */
    protected $namespace;

    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands;

    /**
     * Register the service provider.
     *
     * @throws ErrorException
     */
    public function register()
    {
        $errorMsg = "Your ConsoleServiceProvider(AbstractConsoleProvider) requires property";
        if (!isset($this->namespace) or !is_string($this->namespace))
        {
            throw new ErrorException("$errorMsg \$namespace to be an string");
        }
        if (!isset($this->commands) or !is_array($this->commands))
        {
            throw new ErrorException("$errorMsg \$commands to be an array");
        }

        $bindings = [];
        foreach ($this->commands as $command => $binding)
        {
            $bindings[] = $binding;
            $this->{"registerCommand"}($command, $binding);
        }

        $this->commands($bindings);
    }

    /**
     * Register the command.
     *
     * @param $command
     * @param $binding
     * @throws ErrorException
     */
    protected function registerCommand($command, $binding)
    {
        $class = $this->namespace . '\\' . $command . 'Command';
        if (!class_exists($class))
        {
            throw new ErrorException("Your ConsoleServiceProvider(AbstractConsoleProvider)->registerCommand($command, $binding) could not find $class");
        }
        $this->app->singleton($binding, $class);
    }
    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return array_values($this->commands);
    }
}
