<?php

namespace App\Http\Controllers;

use App\Enums\ResponseMessage;
use App\Models\SubscriptionPackage;
use App\Traits\ResponseAPI;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubscriptionPackageController extends Controller
{
    use ResponseAPI;
    public function index()
    {
        $subscription_package = SubscriptionPackage::with('features')->get();
        return $this->success($subscription_package, ResponseMessage::FETCHED, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'duration_type' => 'required|in:week,month,year',
            'features' => 'required|array',
            'features.*' => 'required|exists:subscription_package_features,id',
        ]);
        if ($validator->fails()) {
            return $this->validationMessage($validator->errors());
        }
        DB::beginTransaction();

        try {
            $obj = [
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'duration_type' => $request->duration_type,
                'is_active' => 1,
                'createdby_id' => auth()->user()->id
            ];
            $subscription_package = SubscriptionPackage::create($obj);
            $subscription_package->features()->sync($request->features);
            $subscription_package_data = SubscriptionPackage::with('features')->find($subscription_package->id);
            DB::commit();
            return $this->success(
                $subscription_package_data,
                ResponseMessage::SAVE,
                200
            );
        } catch (Exception $e) {
            DB::rollBack();
            return $this->error($e->getMessage(), 500);
        }
    }
    public function getById($subscription_package_id)
    {
        $subscription_package = SubscriptionPackage::with('features')->find($subscription_package_id);
        return $this->success($subscription_package, ResponseMessage::FETCHED_DETAIL, 200);
    }


    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:subscription_packages,id',
            'name' => 'required|string',
            'description' => 'required|string',
            'price' => 'required|numeric',
            'duration_type' => 'required|in:week,month,year',
            'features' => 'required|array',
            'features.*' => 'required|exists:subscription_package_features,id',
        ]);
        if ($validator->fails()) {
            return $this->validationMessage($validator->errors());
        }
        DB::beginTransaction();

        try {
            $obj = [
                'name' => $request->name,
                'description' => $request->description,
                'price' => $request->price,
                'duration_type' => $request->duration_type,
                'is_active' => 1,
                'updatedby_id' => auth()->user()->id
            ];
            $subscription_package = SubscriptionPackage::where('id', $request->id)->update($obj);
            $subscription_package->features()->sync($request->features);
            $data = SubscriptionPackage::with('features')->find($request->id);
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
            'id' => 'required|exists:subscription_packages,id',
        ]);
        if ($validator->fails()) {
            return $this->validationMessage($validator->errors());
        }
        $subscription_package = SubscriptionPackage::find($request->id);
        $subscription_package->is_active = $subscription_package->is_active == 1 ? 0 : 1;
        $subscription_package->updatedby_id = auth()->user()->id;
        $subscription_package->update();
        return $this->success($subscription_package, ResponseMessage::UPDATE_STATUS, 200);
    }
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:subscription_packages,id',
        ]);
        if ($validator->fails()) {
            return $this->validationMessage($validator->errors());
        }
        $subscription_package = SubscriptionPackage::find($request->id);
        $subscription_package->deletedby_id = auth()->user()->id;
        $subscription_package->update();
        $subscription_package->delete();
        return $this->success([], ResponseMessage::DELETE, 200);
    }
}
