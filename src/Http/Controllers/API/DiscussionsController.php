<?php

namespace Corals\Modules\Messaging\Http\Controllers\API;

use Corals\Foundation\Http\Controllers\APIBaseController;
use Corals\Modules\Messaging\DataTables\DiscussionsDataTable;
use Corals\Modules\Messaging\Http\Requests\DiscussionRequest;
use Corals\Modules\Messaging\Models\Discussion;
use Corals\Modules\Messaging\Services\DiscussionService;
use Corals\Modules\Messaging\Transformers\API\DiscussionPresenter;
use Illuminate\Http\Request;

class DiscussionsController extends APIBaseController
{
    /**
     * DiscussionsController constructor.
     * @param DiscussionService $discussionService
     */
    public function __construct(protected DiscussionService $discussionService)
    {
        $this->discussionService->setPresenter(new DiscussionPresenter);
        parent::__construct();
    }

    /**
     * @param DiscussionRequest $request
     * @param DiscussionsDataTable $dataTable
     * @return mixed
     */
    public function index(DiscussionRequest $request, DiscussionsDataTable $dataTable)
    {

        $query = $dataTable->query(new Discussion, $request->get('last_msg_created_at'));

        return $this->discussionService->index($query, $dataTable);
    }

    /**
     * @param DiscussionRequest $request
     * @param Discussion $discussion
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(DiscussionRequest $request, Discussion $discussion)
    {
        try {
            return apiResponse($this->discussionService->getModelDetails($discussion));
        } catch (\Exception $e) {
            return apiExceptionResponse($e);
        }
    }

    /**
     * @param Request $request
     * @param Discussion $discussion
     * @return \Illuminate\Http\JsonResponse
     */
    public function markAsRead(Request $request, Discussion $discussion)
    {
        try {
            return apiResponse($this->discussionService->markAsRead($discussion));
        } catch (\Exception $e) {
            return apiExceptionResponse($e);
        }
    }

    /**
     * @param Request $request
     * @param Discussion $discussion
     * @return \Illuminate\Http\JsonResponse
     */
    public function deleteConversation(Request $request, Discussion $discussion)
    {
        try {

            $this->discussionService->deleteConversation($discussion);

            return apiResponse([], trans('Corals::messages.success.deleted', ['item' => 'Discussion']));
        } catch (\Exception $exception) {
            return apiExceptionResponse($exception);
        }
    }

    /**
     * @param Request $request
     * @return mixed
     */
    public function unreadMessagesCount(Request $request)
    {
        try {
            return apiResponse($this->discussionService->unreadMessagesCount());
        } catch (\Exception $e) {
            return apiExceptionResponse($e);
        }
    }
}
