<?php

namespace App\Http\Controllers\Backend\V1\Auth;

use App\Http\Controllers\Backend\V1\APIBaseController;
use App\Models\Backend\ExtendRole as Role;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\Backend\FileConf;
use App\Models\Backend\FileInfo;
use Illuminate\Support\Str;

class FileController extends APIBaseController
{
  /**
   * @param Request $request
   * @return JsonResponse
   */
  public function index(Request $request)
  {
    $list = FileInfo::where('bize_type', $request->bize_type)->where('bize_id', $request->bize_id)
      ->orderby('id', 'asc')->get()->toArray();
    return $this->success($list);
  }

  /**
   * 删除文件
   * @param Request $request
   * @param $id
   * @return JsonResponse
   */
  public function destroy(Request $request, $id)
  {
    $fileInfo = FileInfo::where('id', $id)->firstOrFail();

    //删除磁盘上的文件
    Storage::disk('upload')->delete($fileInfo->file_path . '/' . $fileInfo->new_name);

    $result = $fileInfo->delete();
    if (!$result) {
      $this->failed('Delete Failed.');
    }
    return $this->success($fileInfo->new_name, 'Delete succeed.');
  }

  /**
   * 文件上传接口
   * @param Request $request
   * @return JsonResponse
   */
  public function store(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'bize_type' => 'required|string',
    ]);

    if ($validator->fails()) {
      return $this->validateError($validator->errors()->first());
    }

    $file = $request->file('avatar');

    if (!$request->hasFile('avatar')) {
      return $this->failed('请选择上传的文件');
    }

    if (!$file->isValid()) {
      return $this->failed('文件上传失败');
    }

    $fileConf = FileConf::where('bize_type', $request->input('bize_type'))->where('enabled', 1)->first();

    $fileSize = $file->getClientSize();
    if ($fileConf->file_size_limit < $fileSize) {
      return $this->failed('文件尺寸过大，请重新上传');
    }

    $fileMimeType = $file->getClientMimeType();
    if (strpos($fileConf->file_type_limit, $fileMimeType) === false) {
      return $this->failed('请上传指定类型的文件');
    }

    $ext = $file->getClientOriginalExtension();     // 扩展名
    $realPath = $file->getRealPath();   //临时文件的绝对路径
    $newFileName = (string)Str::uuid(32)->getHex() . '.' . $ext; // 文件新名称
    // 使用我们新建的uploads本地存储空间（目录）
    $newPath = $fileConf->path . date("Y_m_d_H");
    $bool = Storage::disk('upload')->put($newPath . '/' . $newFileName, file_get_contents($realPath));
    if (!$bool) {
      return $this->failed('文件上传失败');
    }

    $originalName = $file->getClientOriginalName(); // 文件原名

    $fileInfo = FileInfo::create([
      'bize_type' => $request->input('bize_type'),
      'original_name' => $originalName,
      'new_name' => $newFileName,
      'file_type' => $fileMimeType,
      'file_size' => $fileSize,
      'file_path' => $newPath,
      'relative_path' => $fileConf->resource_realm,
      'del_flg' => 0,
    ]);

    return $this->success($fileInfo->id);
  }
}
