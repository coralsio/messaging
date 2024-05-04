<?php

namespace Corals\Modules\Messaging\Transformers\API;

use Corals\Foundation\Transformers\FractalPresenter;

class DiscussionPresenter extends FractalPresenter
{
    /**
     * @return DiscussionTransformer
     */
    public function getTransformer()
    {
        return new DiscussionTransformer;
    }
}
