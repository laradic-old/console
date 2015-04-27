<?php
/**
 * Part of the Radic packages.
 */
namespace Laradic\Console;

use Illuminate\Console\Command as BaseCommand;
use Laradic\Console\Notify\Dots;
use Laradic\Console\Notify\Spinner;
use Laradic\Console\Progress\Bar;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\VarDumper;

/**
 * The abstract Command class. Other commands can extend this class to benefit from a larger toolset
 *
 * @package     Laradic\Console
 * @author      Robin Radic
 * @license     MIT
 * @copyright   2011-2015, Robin Radic
 * @link        http://radic.mit-license.org
 */
abstract class Command extends BaseCommand
{

    /**
     * @var bool
     */
    protected $allowSudo = false;

    /**
     * @var bool
     */
    protected $requireSudo = false;

    /**
     * @var \Laradic\Support\ConsoleColor
     */
    protected $colors;

    /**
     * Instanciates the class
     */
    public function __construct()
    {
        parent::__construct();
        $this->colors = new ConsoleColor();
    }

    /**
     * The fire method will be called when the command is invoked
     *
     * @return void
     */
    abstract public function fire();

    /**
     * @param $styles
     * @param $text
     * @return string
     * @throws \JakubOnderka\PhpConsoleColor\InvalidStyleException
     * @internal param array|string $style
     */
    public function colorize($styles, $text)
    {
        return $this->style($styles, $text);
    }

    /**
     * style
     *
     * @param $styles
     * @param $str
     * @return string
     * @throws \JakubOnderka\PhpConsoleColor\InvalidStyleException
     */
    protected function style($styles, $str)
    {
        return $this->colors->apply($styles, $str);
    }

    /**
     * Get the Laravel application instance.
     *
     * @return \Illuminate\Foundation\Application
     */
    public function getLaravel()
    {
        return $this->laravel;
    }

    /**
     * hasRootAccess
     *
     * @return bool
     */
    public function hasRootAccess()
    {
        $path = '/root/.' . md5('_radic-cli-perm-test' . time());
        $root = (@file_put_contents($path, '1') === false ? false : true);
        if ( $root !== false )
        {
            $this->getLaravel()->make('files')->delete($path);
        }

        return $root !== false;
    }

    /**
     * execute
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     * @return mixed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $method = method_exists($this, 'handle') ? 'handle' : 'fire';
        if ( ! $this->allowSudo and ! $this->requireSudo and $this->hasRootAccess() )
        {
            $this->error('Cannot execute this command with root privileges');
            exit;
        }

        if ( $this->requireSudo and ! $this->hasRootAccess() )
        {
            $this->error('This command requires root privileges');
            exit;
        }
        $this->getLaravel()->make('events')->fire('command.firing', $this->name);
        $fire = $this->fire();
        $this->getLaravel()->make('events')->fire('command.fired', $this->name);

        return $fire;
    }

    /**
     * @param mixed
     */
    public function dump($dump)
    {
        VarDumper::dump(func_get_args());
    }

    /**
     * select
     *
     * @param       $question
     * @param array $choices
     * @param null  $default
     * @param null  $attempts
     * @param null  $multiple
     * @return int|string
     */
    public function select($question, array $choices, $default = null, $attempts = null, $multiple = null)
    {
        $question = $this->style([ 'bg_light_gray', 'dark_gray', 'bold' ], " $question ");
        if ( isset($default) )
        {
            $question .= $this->style(
                [ 'bg_dark_gray', 'light_gray' ],
                " [" . $default . "] "
            );
        }

        $choice = $this->choice($question, $choices, $default, $attempts, $multiple);
        foreach ( $choices as $k => $v )
        {
            if ( $choice === $v )
            {
                return $k;
            }
        }
    }

    /**
     * confirm
     *
     * @param string $question
     * @param bool   $default
     * @return bool
     */
    public function confirm($question, $default = false)
    {
        $question = $this->style([ 'bg_light_gray', 'dark_gray', 'bold' ], " $question ");
        $question .= $this->style([ 'bg_dark_gray', 'light_gray' ], " [" . ($default === false ? 'y/N' : 'Y/n') . "] ");

        return parent::confirm($question, $default);
    }

    /**
     * ask
     *
     * @param string $question
     * @param null   $default
     * @return string
     */
    public function ask($question, $default = null)
    {
        $question = $this->style([ 'bg_light_gray', 'dark_gray', 'bold' ], " $question ");
        if ( isset($default) )
        {
            $question .= $this->style([ 'bg_dark_gray', 'light_gray' ], " $default ");
        }

        return parent::ask($question, $default);
    }

    /**
     * dots
     *
     * @param     $message
     * @param int $dots
     * @param int $interval
     * @return Dots
     */
    public function dots($message, $dots = 3, $interval = 100)
    {
        return new Dots($message, $dots, $interval);
    }

    /**
     * spinner
     *
     * @param     $message
     * @param int $interval
     * @return Spinner
     */
    public function spinner($message, $interval = 100)
    {
        return new Spinner($message, $interval);
    }

    /**
     * progressbar
     *
     * @param     $msg
     * @param     $total
     * @param int $interval
     * @return Bar
     */
    public function progressbar($msg, $total, $interval = 100)
    {
        return new Bar($msg, $total, $interval);
    }

    /**
     * arrayTable
     *
     * @param       $arr
     * @param array $header
     */
    protected function arrayTable($arr, array $header = ['Key', 'Value'])
    {

        $rows = [ ];
        foreach ( $arr as $key => $val )
        {
            if ( is_array($val) )
            {
                $val = print_r(array_slice($val, 0, 5), true);
            }
            $rows[ ] = [ (string)$key, (string)$val ];
        }
        $this->table($header, $rows);
    }
}
