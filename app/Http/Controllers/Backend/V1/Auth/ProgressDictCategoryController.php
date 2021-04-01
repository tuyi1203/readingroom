<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use Illuminate\Http\Request;

use App\Services\ProgressDictCategoryService as Service;

class ProgressDictCategoryController extends APIBaseController
{
  public function index(Request $request)
  {
    $categoryList = (new Service())->getDictCategoryList($this->getParams($request));
    return $this->success($categoryList);
  }
}
