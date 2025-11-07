<?php

namespace App\Http\Controllers;

use App\Models\Camera;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Enums\ResponseMessage;
use App\Traits\ResponseAPI;

class CameraController extends Controller
{
    use ResponseAPI;

    public function cameraCount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'nullable|exists:businesses,id',
            'location_id' => 'nullable|exists:locations,id'
        ], $this->validationMessage());

        if ($validator->fails()) {
            return $this->validationResponse($validator->errors());
        }
        $wh = [];
        if (!empty($request->business_id)) {
            $wh[] = ['business_id', $request->business_id];
        }
        if (!empty($request->location_id)) {
            $wh[] = ['location_id', $request->location_id];
        }
        $cameras = Camera::where($wh)->get();
        $enabled = $cameras->where('status', 'enabled')->count();
        $disabled = $cameras->where('status', 'disabled')->count();
        $online = $cameras->where('status', 'online')->count();
        $offline = $cameras->where('status', 'offline')->count();
        $unstable = $cameras->where('status', 'unstable')->count();

        $data = [
            'enabled' => $enabled,
            'disabled' => $disabled,
            'online' => $online,
            'offline' => $offline,
            'unstable' => $unstable,
        ];
        return $this->success($data, ResponseMessage::FETCHED, 200);
    }
    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'business_id' => 'nullable|exists:businesses,id',
            'location_id' => 'nullable|exists:locations,id'
        ], $this->validationMessage());

        if ($validator->fails()) {
            return $this->validationResponse($validator->errors());
        }
        $wh = [];
        if (!empty($request->business_id)) {
            $wh[] = ['business_id', $request->business_id];
        }
        if (!empty($request->location_id)) {
            $wh[] = ['location_id', $request->location_id];
        }
        $cameras = Camera::with(['business', 'location'])->where($wh)->get();
        return $this->success($cameras, ResponseMessage::FETCHED, 200);
    }
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:199|string',
            'ip_address' => 'required|max:100|string|unique:cameras,ip_address',
            'protocol' => 'required|string|max:50',
            'manufacturer' => 'required|string|max:50',
            'location' => 'required|string|max:199',
            'longitude' => 'required|string|max:199',
            'latitude' => 'required|string|max:199',
            'business_id' => 'required|exists:businesses,id',
            'location_id' => 'required|exists:businesses,id',
        ], $this->validationMessage());

        if ($validator->fails()) {
            return $this->validationResponse($validator->errors());
        }
        DB::beginTransaction();

        try {
            $slug = 'cam_' . (Camera::count() + 1);
            $obj = [
                "name"          => $request->name,
                "slug"          => $slug,
                "ip_address"    => $request->ip_address,
                "protocol"      => $request->protocol,
                "manufacturer"  => $request->manufacturer,
                "stream_url"    => $request->stream_url,
                "location"      => $request->location,
                "longitude"     => $request->longitude,
                "latitude"      => $request->latitude,
                "port"          => $request->port ?? null,
                "username"      => $request->username ?? null,
                "password"      => $request->password ?? null,
                "business_id"   => $request->business_id,
                "location_id"   => $request->location_id,
                'createdby_id'  => auth()->user()->id
            ];
            $camera = Camera::create($obj);
            $data = Camera::with(['business', 'location'])->find($camera->id);
            DB::commit();
            return $this->success(
                $data,
                ResponseMessage::SAVE,
                200
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }
    public function getById($camera_id)
    {
        $camera = Camera::with(['business', 'location'])->find($camera_id);
        return $this->success($camera, ResponseMessage::FETCHED_DETAIL, 200);
    }


    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:199|string',
            'ip_address' => 'required|max:100|string|unique:cameras,ip_address' . $request->id,
            'protocol' => 'required|string|max:50',
            'manufacturer' => 'required|string|max:50',
            'location' => 'required|string|max:199',
            'longitude' => 'required|string|max:199',
            'latitude' => 'required|string|max:199',
            'business_id' => 'required|exists:businesses,id',
            'location_id' => 'required|exists:businesses,id',
        ], $this->validationMessage());

        if ($validator->fails()) {
            return $this->validationResponse($validator->errors());
        }
        DB::beginTransaction();

        try {
            $slug = 'cam_' . $request->id;
            $obj = [
                "name"          => $request->name,
                "slug"          => $slug,
                "ip_address"    => $request->ip_address,
                "protocol"      => $request->protocol,
                "manufacturer"  => $request->manufacturer,
                "stream_url"    => $request->stream_url,
                "location"      => $request->location,
                "longitude"     => $request->longitude,
                "latitude"      => $request->latitude,
                "port"          => $request->port ?? null,
                "username"      => $request->username ?? null,
                "password"      => $request->password ?? null,
                "business_id"   => $request->business_id,
                "location_id"   => $request->location_id,
                'updatedby_id'  => auth()->user()->id
            ];
            $camera = Camera::where('id', $request->id)->update($obj);
            $data = Camera::with(['business', 'location'])->find($request->id);
            DB::commit();
            return $this->success(
                $data,
                ResponseMessage::UPDATE,
                200
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }
    public function status(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:cameras,id',
            'status' => 'required|string|in:active,disabled,online,offline,unstable'
        ], $this->validationMessage());
        if ($validator->fails()) {
            return $this->validationResponse($validator->errors());
        }
        $camera = Camera::find($request->id);
        $camera->status = $camera->status;
        $camera->updatedby_id = auth()->user()->id;
        $camera->update();
        return $this->success($camera, ResponseMessage::UPDATE_STATUS, 200);
    }
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:cameras,id',
        ], $this->validationMessage());
        if ($validator->fails()) {
            return $this->validationResponse($validator->errors());
        }
        $camera = Camera::find($request->id);
        $camera->deletedby_id = auth()->user()->id;
        $camera->update();
        $camera->delete();
        return $this->success([], ResponseMessage::DELETE, 200);
    }
}
