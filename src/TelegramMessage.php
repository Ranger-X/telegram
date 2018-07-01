<?php

namespace NotificationChannels\Telegram;

class TelegramMessage
{
    /**
     * @var array Params payload.
     */
    public $payload = [];

    /**
     * @var array Inline Keyboard Buttons.
     */
    protected $buttons = [];

    /**
     * @var bool Pin this message to a channel
     */
    protected $pinMessage = false;

    /**
     * @var bool disable notifications at message pin
     */
    protected $pinMessageDisableNotification = true;

    /**
     * @param string $content
     *
     * @return static
     */
    public static function create($content = '')
    {
        return new static($content);
    }

    /**
     * Message constructor.
     *
     * @param string $content
     */
    public function __construct($content = '')
    {
        $this->content($content);
        $this->payload['parse_mode'] = 'Markdown';
    }

    /**
     * Recipient's Chat ID.
     *
     * @param $chatId
     *
     * @return $this
     */
    public function to($chatId)
    {
        $this->payload['chat_id'] = $chatId;

        return $this;
    }

    /**
     * Recipient's Chat ID.
     *
     * @return int|string
     */
    public function getTo()
    {
        return $this->payload['chat_id'];
    }

    /**
     * Notification message (Supports Markdown).
     *
     * @param $content
     *
     * @return $this
     */
    public function content($content)
    {
        $this->payload['text'] = $content;

        return $this;
    }

    /**
     * Add an inline button.
     *
     * @param string $text
     * @param string $url
     *
     * @return $this
     */
    public function button($text, $url)
    {
        $this->buttons[] = compact('text', 'url');

        $replyMarkup['inline_keyboard'] = array_chunk($this->buttons, 2);
        $this->payload['reply_markup'] = json_encode($replyMarkup);

        return $this;
    }

    /**
     * Additional options to pass to sendMessage method.
     *
     * @param array $options
     *
     * @return $this
     */
    public function options(array $options)
    {
        $this->payload = array_merge($this->payload, $options);

        return $this;
    }

    /**
     * Pin this message to a channel.
     *
     * @param boolean $disable_notification Pass True, if it is not necessary to send a notification to all chat members
     *                                      about the new pinned message. Notifications are always disabled in channels.
     *
     * @return $this
     */
    public function pin($disable_notification = true)
    {
        $this->pinMessage = true;
        $this->pinMessageDisableNotification = $disable_notification;

        return $this;
    }

    /**
     * Determine if we need to pin this message.
     *
     * @return bool
     */
    public function pinned()
    {
        return $this->pinMessage;
    }

    /**
     * Determine if we need disable notification on pin.
     *
     * @return bool
     */
    public function pinNotificationDisabled()
    {
        return $this->pinMessageDisableNotification;
    }

    /**
     * Determine if chat id is not given.
     *
     * @return bool
     */
    public function toNotGiven()
    {
        return !isset($this->payload['chat_id']);
    }

    /**
     * Returns params payload.
     *
     * @return array
     */
    public function toArray()
    {
        return $this->payload;
    }
}
