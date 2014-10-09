<?php
namespace Peridot\Core;

use Evenement\EventEmitterInterface;

trait HasEventEmitterTrait
{
    /**
     * @var \Evenement\EventEmitterInterface
     */
    protected $eventEmitter;

    /**
     * @param \Evenement\EventEmitterInterface $eventEmitter
     */
    public function setEventEmitter(EventEmitterInterface $eventEmitter)
    {
        $this->eventEmitter = $eventEmitter;

        return $this;
    }

    /**
     * @return \Evenement\EventEmitterInterface
     */
    public function getEventEmitter()
    {
        return $this->eventEmitter;
    }
}
