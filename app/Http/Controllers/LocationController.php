<?php

namespace App\Http\Controllers;

use App\Enums\ResponseMessage;
use App\Models\Location;
use Illuminate\Http\Request;
use App\Traits\ResponseAPI;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    use ResponseAPI;
    public function index()
    {
        $location = Location::with('subscription_package')->get();
        return $this->success($location, ResponseMessage::FETCHED, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'logo' => 'required|png|jpg|jpeg|max:2048',
            'business_id' => 'nullable|exists:businesses,id',
        ]);
        $validator->sometimes('business_id', 'required', function ($input) {
            return auth()->user() && auth()->user()->roles[0]->name === 'super admin';
        });
        if ($validator->fails()) {
            return $this->validationMessage($validator->errors());
        }
        DB::beginTransaction();

        try {
            if ($request->hasFile('logo')) {
                $obj['logo'] = $request->file('logo')->store('location', 'public');
            }
            $obj = [
                'name' => $request->name,
                'owner' => $request->owner,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'logo' => $request->logo,
                'business_id' => !empty($request->business_id)?$request->business_id:auth()->user()->business_id,
                'is_active' => 1,
                'createdby_id' => auth()->user()->id
            ];
            $location = Location::create($obj);
            $data = Location::with('business')->find($location->id);
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
    public function getById($location_id)
    {
        $location = Location::with('business')->find($location_id);
        return $this->success($location, ResponseMessage::FETCHED_DETAIL, 200);
    }


    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'logo' => 'required|png|jpg|jpeg|max:2048',
            'business_id' => 'nullable|exists:businesses,id',
        ]);
        $validator->sometimes('business_id', 'required', function ($input) {
            return auth()->user() && auth()->user()->roles[0]->name === 'super admin';
        });
        if ($validator->fails()) {
            return $this->validationMessage($validator->errors());
        }
        DB::beginTransaction();

        try {
            $obj = [
                'name' => $request->name,
                'owner' => $request->owner,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'logo' => $request->logo,
                'business_id' => !empty($request->business_id)?$request->business_id:auth()->user()->business_id,
                'is_active' => 1,
                'updatedby_id' => auth()->user()->id
            ];
            $location = Location::where('id', $request->id)->update($obj);
            $data = Location::with('subscription_package')->find($request->id);
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
            'id' => 'required|exists:locations,id',
        ]);
        if ($validator->fails()) {
            return $this->validationMessage($validator->errors());
        }
        $location = Location::find($request->id);
        $location->is_active = $location->is_active == 1 ? 0 : 1;
        $location->updatedby_id = auth()->user()->id;
        $location->update();
        return $this->success($location, ResponseMessage::UPDATE_STATUS, 200);
    }
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:locations,id',
        ]);
        if ($validator->fails()) {
            return $this->validationMessage($validator->errors());
        }
        $location = Location::find($request->id);
        $location->deletedby_id = auth()->user()->id;
        $location->update();
        $location->delete();
        return $this->success([], ResponseMessage::DELETE, 200);
    }
}
