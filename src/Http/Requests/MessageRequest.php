<?php

namespace Corals\Modules\Messaging\Http\Requests;

use Corals\Foundation\Http\Requests\BaseRequest;
use Corals\Modules\Messaging\Models\Message;
use Illuminate\Support\Arr;

class MessageRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        $this->setModel(Message::class);

        return $this->isAuthorized();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $this->setModel(Message::class);
        $rules = parent::rules();

        if ($this->isUpdate() || $this->isStore()) {
            $rules = array_merge($rules, [
                'body' => 'nullable|max:500',
                'files.*' => 'required|mimes:jpg,jpeg,png,rar,zip,txt,pdf,docs,xls,xlsx,doc|max:' . maxUploadFileSize(),
            ]);

            if (is_api_request()) {
                $rules['second_participation_id'] = 'required';
            }
        }


        return $rules;
    }

    protected function getValidatorInstance()
    {
        if ($this->isUpdate() || $this->isStore()) {
            $data = $this->all();

            $this->getInputSource()->replace(array_merge($data, [
                'participable_type' => getMorphAlias(user()),
                'participable_id' => user()->id,
            ]));
        }

        return parent::getValidatorInstance(); // TODO: Change the autogenerated stub
    }
}
