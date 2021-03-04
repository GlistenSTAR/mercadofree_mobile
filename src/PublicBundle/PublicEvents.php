<?php
/**
 * Created by PhpStorm.
 * User: adrian
 * Date: 4/01/19
 * Time: 19:48
 */

namespace PublicBundle;


class PublicEvents
{
    const RESETTING_RESET_REQUEST = 'public_user.resetting.reset.request';

    /**
     * The RESETTING_RESET_INITIALIZE event occurs when the resetting process is initialized.
     *
     * This event allows you to set the response to bypass the processing.
     *
     * @Event("PublicBundle\Event\GetResponseUserEvent")
     */
    const RESETTING_RESET_INITIALIZE = 'public_user.resetting.reset.initialize';

    /**
     * The RESETTING_RESET_SUCCESS event occurs when the resetting form is submitted successfully.
     *
     * This event allows you to set the response instead of using the default one.
     *
     * @Event("PublicBundle\Event\FormEvent ")
     */
    const RESETTING_RESET_SUCCESS = 'public_user.resetting.reset.success';

    /**
     * The RESETTING_RESET_COMPLETED event occurs after saving the user in the resetting process.
     *
     * This event allows you to access the response which will be sent.
     *
     * @Event("PublicBundle\Event\FilterUserResponseEvent")
     */
    const RESETTING_RESET_COMPLETED = 'public_user.resetting.reset.completed';

    /**
     * The RESETTING_SEND_EMAIL_INITIALIZE event occurs when the send email process is initialized.
     *
     * This event allows you to set the response to bypass the email confirmation processing.
     * The event listener method receives a FOS\UserBundle\Event\GetResponseNullableUserEvent instance.
     *
     * @Event("FOS\UserBundle\Event\GetResponseNullableUserEvent")
     */
    const RESETTING_SEND_EMAIL_INITIALIZE = 'public_user.resetting.send_email.initialize';

    /**
     * The RESETTING_SEND_EMAIL_CONFIRM event occurs when all prerequisites to send email are
     * confirmed and before the mail is sent.
     *
     * This event allows you to set the response to bypass the email sending.
     * The event listener method receives a FOS\UserBundle\Event\GetResponseUserEvent instance.
     *
     * @Event("PublicBundle\Event\GetResponseUserEvent")
     */
    const RESETTING_SEND_EMAIL_CONFIRM = 'public_user.resetting.send_email.confirm';

    /**
     * The RESETTING_SEND_EMAIL_COMPLETED event occurs after the email is sent.
     *
     * This event allows you to set the response to bypass the the redirection after the email is sent.
     * The event listener method receives a FOS\UserBundle\Event\GetResponseUserEvent instance.
     *
     * @Event("PublicBundle\Event\GetResponseUserEvent")
     */
    const RESETTING_SEND_EMAIL_COMPLETED = 'public_user.resetting.send_email.completed';



}