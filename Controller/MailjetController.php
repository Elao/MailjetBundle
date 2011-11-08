<?php

namespace Elao\MailJetBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

use Symfony\Component\HttpFoundation\Response;

use Elao\MailJetBundle\Event\MailjetEvent;

class MailjetController extends Controller
{

    /**
     * Controller to handle trigger api
     */
    public function triggerAction()
    {
        $data = $this->getRequest()->request->all();

        $event_type = $data['event'];

        $event = new MailjetEvent($data);

        // handle all event
        $this->get('event_dispatcher')->dispatch('elao.mailjet.trigger');

        // handle specific event
        $this->get('event_dispatcher')->dispatch('elao.mailjet.trigger.' . $event_type);

        return new Response('', 200);
    }

}
