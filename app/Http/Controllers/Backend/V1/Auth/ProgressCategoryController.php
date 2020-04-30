<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use Illuminate\Http\Request;
use App\Models\Backend\ProgressDictCategory;
use App\Models\Backend\ProgressDict;

class ProgressCategoryController extends APIBaseController
{
  public function index(Request $request)
  {
    $categoryList = [];
    if ($request->has('category_name')) {
      $categoryList = ProgressDictCategory::where('category_name',$request->input('category_name'))->get()->toArray();
    }
    return $this->success($categoryList);
  }
}
