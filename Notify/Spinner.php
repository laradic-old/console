<?php
namespace  Laradic\Console\Notify;

class Spinner extends \cli\notify\Spinner {
    protected $_chars = '-\|/';
    protected $_format = '{:msg} {:char}  ({:elapsed}, {:speed}/s)';
    protected $_iteration = 0;
}
