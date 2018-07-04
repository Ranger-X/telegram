<?php

namespace NotificationChannels\Telegram;

use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\Exceptions\CouldNotSendNotification;
use Log;

class TelegramChannel
{
    /**
     * @var Telegram
     */
    protected $telegram;

    /**
     * Channel constructor.
     *
     * @param Telegram $telegram
     */
    public function __construct(Telegram $telegram)
    {
        $this->telegram = $telegram;
    }

    /**
     * Send the given notification.
     *
     * @param mixed        $notifiable
     * @param Notification $notification
     */
    public function send($notifiable, Notification $notification)
    {
        $message = $notification->toTelegram($notifiable);

        if (is_string($message)) {
            $message = TelegramMessage::create($message);
        }

        if ($message->toNotGiven()) {
            if (!$to = $notifiable->routeNotificationFor('telegram')) {
                throw CouldNotSendNotification::chatIdNotProvided();
            }

            $message->to($to);
        }

        $params = $message->toArray();

        $response = $this->telegram->sendMessage($params);

        if ($message->pinned()) {
            $telegram_msg = json_decode($response->getBody(), true);

            $params = [];
            $params['chat_id']    = $message->getTo();
            $params['message_id'] = $telegram_msg['result']['message_id'];
            $params['disable_notification'] = $message->pinNotificationDisabled();

            $this->telegram->pinChatMessage($params);
        }
    }
}
