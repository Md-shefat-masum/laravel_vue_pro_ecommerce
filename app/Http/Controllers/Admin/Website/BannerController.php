<?php

namespace App\Http\Controllers\Admin\Website;

use App\Http\Controllers\Controller;
use App\Models\WebsiteBanner;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;

class BannerController extends Controller
{
    public function all()
    {
        $paginate = (int) request()->paginate;
        $orderBy = request()->orderBy;
        $orderByType = request()->orderByType;

        $status = 1;
        if (request()->has('status')) {
            $status = request()->status;
        }

        $query = WebsiteBanner::where('status', $status)->orderBy($orderBy, $orderByType);

        if (request()->has('search_key')) {
            $key = request()->search_key;
            $query->where(function ($q) use ($key) {
                return $q->where('id', $key)
                    ->orWhere('name', $key)
                    ->orWhere('name', 'LIKE', '%' . $key . '%');
            });
        }

        $users = $query->paginate($paginate);
        return response()->json($users);
    }

    public function show($id)
    {
        $data = WebsiteBanner::where('id', $id)->first();
        if (!$data) {
            return response()->json([
                'err_message' => 'not found',
                'errors' => ['role' => ['data not found']],
            ], 422);
        }
        return response()->json($data, 200);
    }

    public function save_image($image)
    {
        $file = $image;
        $extension = $file->getClientOriginalExtension();
        $temp_name  = uniqid(10) . time();
        $image = Image::make($file);
        $width = 1060;
        $height = 400;
        $canvas = Image::canvas($width, $height);
        $image->fit($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });
        $canvas->insert($image);
        // $canvas->insert(interImage::make(public_path('ilogo.png')), 'bottom-right');
        if (!file_exists(public_path('uploads/banner'))) {
            mkdir(public_path('uploads/banner'));
        }
        $path = 'uploads/banner/banner_1060x400_' . $temp_name . '.' . $extension;
        $canvas->save($path);
        return $path;
    }

    public function store(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'image' => ['required']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $image = '';
        if (request()->hasFile('image')) {
            try {
                $image = $this->save_image(request()->image);
            } catch (\Throwable $th) {
                return response()->json([
                    'err_message' => 'validation error',
                    'errors' => ['image' => ['this image cannot be upload', $th->getMessage()]],
                ], 422);
            }
        }

        $data = new WebsiteBanner();
        $data->title = $request->title;
        $data->image = $image;
        $data->save();

        return response()->json($data, 200);
    }

    public function canvas_store(Request $request)
    {
        $validator = Validator::make(request()->all(), [
            'name' => ['required', 'unique:brands']
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = new WebsiteBanner();
        $data->name = $request->name;
        $data->creator = Auth::user()->id;
        $data->save();
        $data->slug = $data->id . uniqid(5);
        $data->save();

        return response()->json($data, 200);
    }

    public function update()
    {
        $data = WebsiteBanner::find(request()->id);
        if (!$data) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => ['name' => ['data not found by given id ' . (request()->id ? request()->id : 'null')]],
            ], 422);
        }

        if(!file_exists(public_path($data->image))){
            $validator = Validator::make(request()->all(), [
                'image' => ['required']
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'err_message' => 'validation error',
                    'errors' => $validator->errors(),
                ], 422);
            }
        }

        $image = $data->image;
        if (request()->hasFile('image')) {
            try {
                if(file_exists(public_path($data->image))){
                    unlink(public_path($data->image));
                }
                $image = $this->save_image(request()->image);
            } catch (\Throwable $th) {
                return response()->json([
                    'err_message' => 'validation error',
                    'errors' => ['image' => ['this image cannot be upload', $th->getMessage()]],
                ], 422);
            }
        }

        $data->title = request()->title;
        $data->image = $image;
        $data->save();

        return response()->json($data, 200);
    }

    public function toggle_status()
    {
        $data = WebsiteBanner::find(request()->id);
        if (!$data) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => ['name' => ['data not found by given id ' . (request()->id ? request()->id : 'null')]],
            ], 422);
        }

        $data->show = $data->show ? 0 : 1;
        $data->save();

        return response()->json($data->show);
    }

    public function canvas_update()
    {
        $data = WebsiteBanner::find(request()->id);
        if (!$data) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => ['name' => ['user_role not found by given id ' . (request()->id ? request()->id : 'null')]],
            ], 422);
        }

        $validator = Validator::make(request()->all(), [
            'name' => ['required'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data->name = request()->name;
        $data->save();

        return response()->json($data, 200);
    }

    public function soft_delete()
    {
        $validator = Validator::make(request()->all(), [
            'id' => ['required', 'exists:website_banners,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = WebsiteBanner::find(request()->id);
        $data->status = 0;
        $data->save();

        return response()->json([
            'result' => 'deactivated',
        ], 200);
    }

    public function destroy()
    {
    }

    public function restore()
    {
        $validator = Validator::make(request()->all(), [
            'id' => ['required', 'exists:website_banners,id'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        $data = WebsiteBanner::find(request()->id);
        $data->status = 1;
        $data->save();

        return response()->json([
            'result' => 'activated',
        ], 200);
    }

    public function bulk_import()
    {
        $validator = Validator::make(request()->all(), [
            'data' => ['required', 'array'],
        ]);

        if ($validator->fails()) {
            return response()->json([
                'err_message' => 'validation error',
                'errors' => $validator->errors(),
            ], 422);
        }

        foreach (request()->data as $item) {
            $item['created_at'] = $item['created_at'] ? Carbon::parse($item['created_at']) : Carbon::now()->toDateTimeString();
            $item['updated_at'] = $item['updated_at'] ? Carbon::parse($item['updated_at']) : Carbon::now()->toDateTimeString();
            $item = (object) $item;
            $check = WebsiteBanner::where('id', $item->id)->first();
            if (!$check) {
                try {
                    WebsiteBanner::create((array) $item);
                } catch (\Throwable $th) {
                    return response()->json([
                        'err_message' => 'validation error',
                        'errors' => $th->getMessage(),
                    ], 400);
                }
            }
        }

        return response()->json('success', 200);
    }
}
