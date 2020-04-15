<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use App\Models\Backend\ProgressDict;
use App\Models\Backend\ProgressDictCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class ProgressDictController extends APIBaseController
{
  public function index(Request $request)
  {
    $dictList = [];
    if ($request->has('category_name')) {
      $categoryNames = $request->input('category_name');
      if (!is_array($request->input('category_name'))) {
        $categoryNames = [$request->input('category_name')];
      }
      $categoriesCollection = ProgressDictCategory::whereIn('category_name', $categoryNames)->get();
      $categoryIds =
        $categoriesCollection->map(function ($record) {
          return $record->id;
        });
      $categories = $categoriesCollection->toArray();
      $dictList = ProgressDict::whereIn('dict_category', $categoryIds)
        ->orderby('dict_category', 'asc')
        ->orderby('order_sort', 'asc')
        ->get()
        ->groupBy(function($dictItem, $dictKey) use ($categories) {
          return Arr::first($categories, function ($cateItem, $cateKey) use ($dictItem, $dictKey) {
            return $dictItem['dict_category'] === $cateItem['id'];
          })['category_name'];
        })
        ->toArray();
    } else {
      $categories = ProgressDictCategory::all()->toArray();
      $dictList = ProgressDict::orderby('dict_category', 'asc')
        ->orderby('order_sort', 'asc')
        ->get()
        ->groupBy(function ($dictItem, $dictKey) use ($categories) {
          return Arr::first($categories, function ($cateItem, $cateKey) use ($dictItem, $dictKey) {
            return $dictItem['dict_category'] === $cateItem['id'];
          })['category_name'];
        })
        ->toArray();
    }

    return $this->success($dictList);

  }
}
