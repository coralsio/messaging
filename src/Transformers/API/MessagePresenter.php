<?php

namespace Corals\Modules\Messaging\Transformers\API;

use Corals\Foundation\Transformers\FractalPresenter;

class MessagePresenter extends FractalPresenter
{
    /**
     * @return MessageTransformer|\League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new MessageTransformer();
    }
}
