<?php

namespace App\Http\Controllers;

use App\Enums\ResponseMessage;
use App\Models\SubscriptionPackageFeature;
use App\Traits\ResponseAPI;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class SubscriptionPackageFeatureController extends Controller
{
    use ResponseAPI;
    public function index()
    {
        $subscription_package_features = SubscriptionPackageFeature::get();
        return $this->success(
            $subscription_package_features,
            ResponseMessage::FETCHED,
            200
        );
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'required|string'
        ]);
        if ($validator->fails()) {
            return $this->validationMessage($validator->errors());
        }
        DB::beginTransaction();

        try {
            $obj = [
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => 1,
                'createdby_id' => auth()->user()->id
            ];
            $subscription_package_feature = SubscriptionPackageFeature::create($obj);
            $data = SubscriptionPackageFeature::find($subscription_package_feature->id);
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
    public function getById($subscription_package_feature_id)
    {
        $subscription_package_feature = SubscriptionPackageFeature::find($subscription_package_feature_id);
        return $this->success($subscription_package_feature, ResponseMessage::FETCHED_DETAIL, 200);
    }


    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:subscription_package_features,id',
            'name' => 'required|string',
            'description' => 'required|string'
        ]);
        if ($validator->fails()) {
            return $this->validationMessage($validator->errors());
        }
        DB::beginTransaction();

        try {
            $obj = [
                'name' => $request->name,
                'description' => $request->description,
                'is_active' => 1,
                'updatedby_id' => auth()->user()->id
            ];
            $subscription_package_feature = SubscriptionPackageFeature::where('id', $request->id)->update($obj);
            $subscription_package_feature->features()->sync($request->features);
            $data = SubscriptionPackageFeature::find($request->id);
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
            'id' => 'required|exists:subscription_package_features,id',
        ]);
        if ($validator->fails()) {
            return $this->validationMessage($validator->errors());
        }
        $subscription_package_feature = SubscriptionPackageFeature::find($request->id);
        $subscription_package_feature->is_active = $subscription_package_feature->is_active == 1 ? 0 : 1;
        $subscription_package_feature->updatedby_id = auth()->user()->id;
        $subscription_package_feature->update();
        return $this->success($subscription_package_feature, ResponseMessage::UPDATE_STATUS, 200);
    }
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:subscription_package_features,id',
        ]);
        if ($validator->fails()) {
            return $this->validationMessage($validator->errors());
        }
        $subscription_package_feature = SubscriptionPackageFeature::find($request->id);
        $subscription_package_feature->delete();
        return $this->success([], ResponseMessage::DELETE, 200);
    }
}
