<?php

namespace Lsg\AutoScreen\Support;

use App\Http\Requests\Support\BaseRequest;

class MakeValidateRequest extends BaseRequest
{
    /**
     * rules
     *
     * @return array
     */
    public function rules()
    {
        return GlobalParams::getValidatorRule();
    }

    /**
     * attributes
     *
     * @return array
     */
    public function attributes()
    {
        return GlobalParams::getValidatorAttr();
    }
}
