<?php
namespace  Laradic\Console\Progress;

class Bar extends \cli\progress\Bar
{

    protected $_bars = '=>';

    protected $_formatMessage = '{:msg}  {:percent}% [';

    protected $_formatTiming = '] {:elapsed} / {:estimated}';

    protected $_format = '{:msg}{:bar}{:timing}';

}
