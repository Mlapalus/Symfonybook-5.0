<?php

namespace App\EventSubscriber;

use App\Repository\ConferenceRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use App\Renderer\Renderer;

class TwigEventSubscriber implements EventSubscriberInterface
{
    private Renderer $renderer;
    private ConferenceRepository $conferenceRepository;
    public function __construct(Renderer $renderer, ConferenceRepository $conferenceRepository)
    {
        $this->renderer = $renderer;
        $this->conferenceRepository =$conferenceRepository;        
    }
    public function onControllerEvent(ControllerEvent $event)
    {
        $this->renderer->addGlobal('conferences', $this->conferenceRepository->findAll());
    }

    public static function getSubscribedEvents()
    {
        return [
            ControllerEvent::class => 'onControllerEvent',
        ];
    }
}