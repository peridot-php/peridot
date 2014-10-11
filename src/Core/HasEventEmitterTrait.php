<?php
namespace Peridot\Core;

use Evenement\EventEmitterInterface;

/**
 * Trait indicating an object supports an EventEmitter via composition.
 *
 * @package Peridot\Core
 */
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
