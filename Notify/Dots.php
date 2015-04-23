<?php
namespace  Laradic\Console\Notify;

class Dots extends \cli\notify\Dots {
	protected $_dots;
	protected $_format = '{:msg}{:dots}'; //({:elapsed}, {:speed}/s)
	protected $_iteration;

    protected $started;

    public function start(&$started)
    {
        $this->started = true;
        while($started === true)
        {
            $this->tick();
        }
    }

    public function stop()
    {
        $this->started = false;
        $this->finish();
    }
}
