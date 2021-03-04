<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PublicBundle\EventListener;

use Doctrine\ORM\EntityManager;
use PublicBundle\Event\FormEvent;
use PublicBundle\Event\GetResponseUserEvent;
use PublicBundle\PublicEvents;
use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ResettingListener implements EventSubscriberInterface
{

    private $em;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    /**
     * @var int
     */
    private $tokenTtl;

    /**
     * ResettingListener constructor.
     *
     * @param UrlGeneratorInterface $router
     * @param int $tokenTtl
     */
    public function __construct(EntityManager $em, UrlGeneratorInterface $router, $tokenTtl)
    {
        $this->em = $em;
        $this->router = $router;
        $this->tokenTtl = $tokenTtl;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            PublicEvents::RESETTING_RESET_INITIALIZE => 'onResettingResetInitialize',
            PublicEvents::RESETTING_RESET_SUCCESS => 'onResettingResetSuccess',
            PublicEvents::RESETTING_RESET_REQUEST => 'onResettingResetRequest',
        );
    }

    /**
     * @param GetResponseUserEvent $event
     */
    public function onResettingResetInitialize(GetResponseUserEvent $event)
    {
            $event->setResponse(new RedirectResponse($this->router->generate('public_user_resetting_request')));
    }

    /**
     * @param FormEvent $event
     */
    public function onResettingResetSuccess(Event $event)
    {

        $token = $event->getRequest()->get('_token');

        $user = $this->em->getRepository('AdministracionBundle:Usuario')->findOneBy(array('confirmationToken' => $token));

        $user->setConfirmationToken(null);
        $user->setPasswordRequestedAt(null);
    }

    /**
     * @param GetResponseUserEvent $event
     */
    public function onResettingResetRequest(GetResponseUserEvent $event)
    {
        $event->setResponse(new RedirectResponse($this->router->generate('public_user_resetting_request')));
    }
}
