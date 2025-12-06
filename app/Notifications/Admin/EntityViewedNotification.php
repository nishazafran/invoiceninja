<?php

/**
 * Invoice Ninja (https://invoiceninja.com).
 *
 * @link https://github.com/invoiceninja/invoiceninja source repository
 *
 * @copyright Copyright (c) 2025. Invoice Ninja LLC (https://invoiceninja.com)
 *
 * @license https://www.elastic.co/licensing/elastic-license
 */

namespace App\Notifications\Admin;

use App\Utils\Number;
use Illuminate\Notifications\Messages\SlackMessage;
use Illuminate\Notifications\Notification;

class EntityViewedNotification extends Notification
{
    /**
     * Create a new notification instance.
     *
     * @return void
     * @
     */
    protected $invitation;

    protected $entity_name;

    protected $entity;

    protected $company;

    protected $settings;

    public $method;

    protected $contact;

    public $is_system;

    public function __construct($invitation, $entity_name, $is_system = false, $settings = null)
    {
        $this->entity_name = $entity_name;
        $this->entity = $invitation->{$entity_name};
        $this->contact = $invitation->contact;
        $this->company = $invitation->company;
        $this->settings = $invitation->contact->client->getMergedSettings();
        $this->is_system = $is_system;
        $this->invitation = $invitation;
        $this->method = null;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return $this->method ?: [];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     *
     */
    public function toMail($notifiable)
    {
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
 }

    
