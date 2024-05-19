<?php

namespace Corals\Modules\Messaging\Events;

use Corals\Modules\Messaging\Models\Message;
use Corals\Modules\Messaging\Transformers\API\MessagePresenter;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageReceived implements ShouldBroadcast
{

    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * @var string
     */
    public $queue = 'messaging-queue';

    /**
     * Create a new event instance.
     */
    public function __construct(protected Message $message)
    {
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        $channels = [];

        foreach ($this->message->recipients as $participation) {
            $channels[] = new PrivateChannel(sprintf(
                'messages.%s',
                $participation->participable->id
            ));
        }

        return $channels;
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function broadcastWith(): array
    {
        return (new MessagePresenter)->present($this->message)['data'];
    }

    /**
     * @return string
     */
    public function broadcastAs(): string
    {
        return 'message.received';
    }

    /**
     * @return bool
     */
    public function broadcastWhen(): bool
    {
        return $this->message->status <> 'draft';
    }
}
