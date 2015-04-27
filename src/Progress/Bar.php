<?php
/**
 * Part of the Robin Radic's PHP packages.
 *
 * MIT License and copyright information bundled with this package
 * in the LICENSE file or visit http://radic.mit-license.com
 */
namespace Laradic\Console\Progress;

/**
 * A progress bar class based on wp-cli/php-cli-tools.
 * Modifies a few properties/values to alter the displayed stuff.
 *
 * @package        Laradic\Console
 * @version        1.0.0
 * @author         Robin Radic
 * @license        MIT License
 * @copyright      2015, Robin Radic
 * @link           https://github.com/robinradic
 */
class Bar extends \cli\progress\Bar
{

    protected $_bars = '=>';

    protected $_formatMessage = '{:msg}  {:percent}% [';

    protected $_formatTiming = '] {:elapsed} / {:estimated}';

    protected $_format = '{:msg}{:bar}{:timing}';

}
