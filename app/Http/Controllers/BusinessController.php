<?php

namespace App\Http\Controllers;

use App\Enums\ResponseMessage;
use App\Models\Business;
use App\Models\SubscriptionPackage;
use App\Traits\ResponseAPI;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BusinessController extends Controller
{
    use ResponseAPI;
    public function index()
    {
        $business = Business::with('subscription_package')->get();
        return $this->success($business, ResponseMessage::FETCHED, 200);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'domain' => 'required|string|unique:businesses,domain|max:255|regex:/^(?!-)[A-Za-z0-9-]+(?<!-)$/',
            'owner' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'website' => 'required|string|max:255',
            'logo' => 'required|png|jpg|jpeg|max:2048',
            'subscription_package_id' => 'required|exists:subscription_packages,id',
        ]);
        if ($validator->fails()) {
            return $this->validationMessage($validator->errors());
        }
        DB::beginTransaction();

        try {
            if ($request->hasFile('logo')) {
                $obj['logo'] = $request->file('logo')->store('business', 'public');
            }
            $package = SubscriptionPackage::find($request->subscription_package_id);
            $expiryDate = Carbon::now()->add($package->duration, $package->duration_type)->format('Y-m-d');
            $obj = [
                'name' => $request->name,
                'domain' => $request->domain,
                'owner' => $request->owner,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'website' => $request->website,
                'logo' => $request->logo,
                'subscription_package_id' => $request->subscription_package_id,
                'subscription_status' => 'active',
                'subscription_end_date' => $expiryDate,
                'is_active' => 1,
                'createdby_id' => auth()->user()->id
            ];
            $business = Business::create($obj);
            $data = Business::with('subscription_package')->find($business->id);
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
    public function getById($business_id)
    {
        $business = Business::with('subscription_package')->find($business_id);
        return $this->success($business, ResponseMessage::FETCHED_DETAIL, 200);
    }


    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:businesses,id',
            'name' => 'required|string|max:255',
            // 'domain' => 'required|string|unique:businesses,domain|max:255|regex:/^(?!-)[A-Za-z0-9-]+(?<!-)$/',
            'owner' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'phone' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'website' => 'required|string|max:255',
            'logo' => 'required|png|jpg|jpeg|max:2048',
            'subscription_package_id' => 'required|exists:subscription_packages,id',
        ]);
        if ($validator->fails()) {
            return $this->validationMessage($validator->errors());
        }
        DB::beginTransaction();

        try {
            $package = SubscriptionPackage::find($request->subscription_package_id);
            $expiryDate = Carbon::now()->add($package->duration, $package->duration_type)->format('Y-m-d');
            $obj = [
                'name' => $request->name,
                // 'domain' => $request->domain,
                'owner' => $request->owner,
                'email' => $request->email,
                'phone' => $request->phone,
                'address' => $request->address,
                'website' => $request->website,
                'logo' => $request->logo,
                'subscription_package_id' => $request->subscription_package_id,
                'subscription_status' => 'active',
                'subscription_end_date' => $expiryDate,
                'is_active' => 1,
                'updatedby_id' => auth()->user()->id
            ];
            $business = Business::where('id', $request->id)->update($obj);
            $data = Business::with('subscription_package')->find($request->id);
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
            'id' => 'required|exists:businesses,id',
        ]);
        if ($validator->fails()) {
            return $this->validationMessage($validator->errors());
        }
        $business = Business::find($request->id);
        $business->is_active = $business->is_active == 1 ? 0 : 1;
        $business->updatedby_id = auth()->user()->id;
        $business->update();
        return $this->success($business, ResponseMessage::UPDATE_STATUS, 200);
    }
    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required|exists:businesses,id',
        ]);
        if ($validator->fails()) {
            return $this->validationMessage($validator->errors());
        }
        $business = Business::find($request->id);
        $business->deletedby_id = auth()->user()->id;
        $business->update();
        $business->delete();
        return $this->success([], ResponseMessage::DELETE, 200);
    }
}
