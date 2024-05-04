<?php


namespace Corals\Modules\Messaging\Http\Controllers\API;

use Corals\Foundation\Http\Controllers\APIBaseController;
use Corals\Modules\Messaging\Http\Requests\MessageRequest;
use Corals\Modules\Messaging\Models\Message;
use Corals\Modules\Messaging\Services\MessageService;
use Corals\Modules\Messaging\Transformers\API\MessagePresenter;
use Illuminate\Http\Request;

class MessageController extends APIBaseController
{
    /**
     * MessageController constructor.
     * @param MessageService $messageService
     */
    public function __construct(protected MessageService $messageService)
    {
        $this->messageService->setPresenter(new MessagePresenter);
        parent::__construct();
    }

    /**
     * @param MessageRequest $request
     * @param Message $message
     * @return \Illuminate\Http\JsonResponse|mixed
     */
    public function fetchMoreMessages(MessageRequest $request, Message $message)
    {
        try {
            return $this->messageService->fetchMoreMessages($message);
        } catch (\Exception $e) {
            return apiExceptionResponse($e);
        }
    }

    /**
     * @param MessageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(MessageRequest $request)
    {
        try {

            $message = $this->messageService->store($request, Message::class);

            return apiResponse($this->messageService->getModelDetails(), trans('Corals::messages.success.created', [
                'item' => 'message'
            ]));

        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }

    /**
     * @param Request $request
     * @param Message $message
     * @return \Illuminate\Http\JsonResponse
     */
    public function broadcastMessage(Request $request, Message $message)
    {
        try {
            return apiResponse($this->messageService->broadcastMessage($message));
        } catch (\Exception $e) {
            return apiExceptionResponse($e);
        }
    }
}
