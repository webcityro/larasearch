<?php
namespace Webcityro\Larasearch\Http\Requests;

use Webcityro\Larasearch\Search\Params;
use Webcityro\Larasearch\Search\OrderBy;

interface SearchFormRequest {

    public function requestParams(): Params;

    public function requestOrder(): OrderBy;
}
