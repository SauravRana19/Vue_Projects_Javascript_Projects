<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Model\User;
use App\Model\UserDevice;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Traits\SMS;
use App\Model\Order;
use App\Model\GSTOutward;
use Illuminate\Support\Facades\Mail;
use App\Model\Wallet;
use Bitly;
use App\Traits\Email;
use App\Model\AccountSettings;

class UserController extends Controller
{
    use SMS;
    use Email;
    /**
     * To get the user data
     * @created_at 10-06-2020
     * @author Bhagirath Giri
     * @return response json
     * @param user id - optional
     *
     */
    public function getUsers(Request $request)
    {
        if (!empty($request['registration']) || !empty($request['primary_mobile']) || !empty($request['device_id']) || !empty($request['email'])) {
            $users = User::where('registration', $request['registration'])
                ->orWhere('device_id', $request['device_id'])
                ->orWhere('email', $request['email'])
                ->orWhere('primary_mobile', $request['primary_mobile'])
                ->first();
        } else {
            $users = User::orderBy('registration', 'desc')->get();
        }
        if (is_null($users)) {
            return response()->json(
                [
                    'message' => 'User not found',
                    'status' => 0,
                ],
                404
            );
        } else {
            return response()->json(
                [
                    'user_data' =>  $users,
                    'status' => 1,
                ],
                200
            );
        }
    }

    /**
     * To check the availabilty of the device
     * @created_at 14-06-2020
     * @param request
     * @return response json
     * @author Bhagirath
     */
    public function checkDevice(Request $request)
    {
        if ($request['device_id']) {
            $user = User::where('device_id', $request['device_id'])->first();
            if (is_null($user)) {
                return response()->json([
                    'message' => 'Device can be registered',
                    'status' => 1
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Device already registered',
                    'user' => $user,
                    'status' => 0
                ], 400);
            }
        }
        if ($request['primary_mobile']) {
            $user = User::where('primary_mobile', $request['primary_mobile'])->first();
            if (is_null($user)) {
                return response()->json([
                    'message' => 'Phone number can be registered',
                    'status' => 1
                ], 200);
            } else {
                return response()->json([
                    'message' => 'Phone number already registered',
                    'user' => $user,
                    'status' => 0
                ], 400);
            }
        }
    }

    /**
     * To register the user
     * @created_at 10-06-2020
     * @author Bhagirath Giri
     * @return response json
     * @param request
     *
     */
    public function register(Request $request)
    {
        $now = Carbon::now();
        $todayDate = $now->toDateString();
        $rules = [
            'name' => 'max:200',
            'email' => 'email|unique:users,email|max:200',
            'primary_mobile' => 'required|unique:users,primary_mobile',
            'device_id' => 'required|unique:users,device_id',
            // 'adhar_number' => 'unique:users,adhar_number',
            // 'password' => 'required',
        ];
        $data = Validator::make($request->all(), $rules);
        if ($data->fails()) {

            return response()->json(
                [
                    'old_data' => $request->all(),
                    'message' => 'Validation Failed',
                    'errors' => $data->errors()->toArray(),
                ],
                400
            );
        }
        $data = [
            'name' =>  $request['name'] ?? "",
            'email' =>  $request['email'] ?? "",
            // 'adhar_number' => $request['adhar_number'],
            'password' =>  $request['password'] ?? "",
            'device_id' =>  $request['device_id'],
            'primary_mobile' =>  $request['primary_mobile'],
            'start_date' => $todayDate,
            'end_date' => $now->addDays(otherRewardData()->free_subscription)->toDateString(),
        ];
        // }
        //DB Transaction
        DB::beginTransaction();
        // Register User
        try {
            $user = User::create($data);
            $registrationNo = 1000 + $user->id;
            $user->registration = "QPR" . $registrationNo;
            $user->save();
            DB::commit();
        } catch (\Exception $e) {
            $user = null;
            $error_message   = $e->getMessage();
            DB::rollback();
        }
        if (is_null($user)) {
            return response()->json(
                [
                    'old_data' => $request->all(),
                    'message' => $error_message,
                    'status' => 0
                ],
                500
            );
        } else {
            $newUser = User::find($user->id);
            $message = "Dear $newUser->name, your account has been created successfully on Qartplus. \nYour registration number is $newUser->registration. \nEnjoy your " . otherRewardData()->free_subscription . " days free subscription. \nTrail Period :" . date('d-M-Y', strtotime($newUser->start_date)) . " to " . date('d-M-Y', strtotime($newUser->end_date)) . ", \nRegards,\nTeam Qartplus";
            // $this->send($newUser->primary_mobile, $message, false, 1507160933136188688);
            // if (isset($newUser->email)) {
            //     $this->sendEmail("Qartplus Registration", $message, $newUser->email, $newUser->name);
            // }
            return response()->json(
                [
                    'message' => 'User registered successfully',
                    'status' => 1,
                    'user_data' => $newUser->toArray(),
                ],
                200
            );
        }
        //----------------------
    }
    /**
     * To register the dealer / convert a customer into a dealer
     * @author Bhagirath Giri
     * @created_at 14-06-2020
     * @param Request data , $registration
     * @return response json
     */
    public function makeDealer(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'registration' => 'required',
                // 'password' => 'required',
                // 'device_id' => 'required',
            ]
        );
        if ($validator->fails())
            return response()->json(['message' => $validator->errors(), 'status' => 0], 400);

        $user = User::where(
            [
                'registration' => $request['registration'],
                // 'password' => $request['password'],
                // 'device_id' => $request['device_id'],
                'status' => 1
            ]
        )->first();
        if (!is_null($user) && $user->is_dealer != 1) {
            if (!empty($request['level_code'])) {
                $levelData = getRewardData($request['level_code']);
                if (is_null($levelData)) {
                    return response()->json(['message' => 'Invalid level code', 'status' => 0], 404);
                }
                $level_code = $levelData->code;
                $level_name = $levelData->level;
                $referals = $levelData->min_customer;
                $planPrice = $request['plan_price'] ?? $levelData->level_price;
            } else {
                $levelData = getRewardData("QPL1001");
                if (is_null($levelData)) {
                    return response()->json(['message' => 'Invalid level code', 'status' => 0], 404);
                }
                $level_code = $levelData->code;
                $level_name = $levelData->level;
                $referals = $levelData->min_customer;
                $planPrice = $request['plan_price'] ?? $levelData->level_price;
            }
            DB::beginTransaction();
            try {
                $registrationNo = str_replace("R", "D", $user->registration);
                $user->registration = $registrationNo;
                $user->dealer_type = $level_code;
                $user->dealer_level = $level_name;
                $user->is_dealer = 1;
                $user->adhar_number = $request['adhar_number'] ?? $user->adhar_number;
                $user->dob  = date('Y-m-d', strtotime($request['dob'])) ?? null;
                $user->rewarded_referral = $referals;
                $user->number_of_referals = $referals;
                $user->save();
                // GST  table entry
                if ($request['bill_flag'] && !is_null($request['gst_details'])) {
                    $data = json_decode($request['gst_details'], true);
                    $accountSettings = accountSettings();
                    $gst = new GSTOutward;
                    $gst->gstin = $data['gstin'] ?? null;
                    $gst->name = $data['name'];
                    $gst->phone_number = $data['primary_number'];
                    $gst->invoice_number = $accountSettings->last_invoice_number + 1;
                    $tax = ($accountSettings->gst_in_percentage / 100) * $planPrice;
                    $gst->total_value = $planPrice + $tax;
                    $gst->hsn_code = $accountSettings->hsn_code ?? null;
                    $gst->gst_rate = $accountSettings->gst_in_percentage;
                    $gst->taxable_value = $planPrice;
                    if ($data['state'] == $accountSettings->current_state) {
                        $gst->central_tax = $tax / 2;
                        $gst->state_tax = $tax / 2;
                    } else {
                        $gst->integrated_tax = $tax;
                    }
                    $gst->address = $data['address'] ?? null;
                    $gst->state = $data['state'] ?? null;
                    $gst->country = "India";
                    $gst->pin_code  = $data['pincode'] ?? null;
                    $gst->product_code = $level_code;
                    $gst->product_description = "Dealership Plan: $level_name ($level_code)";
                    $gst->product_price = $planPrice;
                    $gst->quantity = 1;
                    $gst->save();
                    DB::update('update account_settings set last_invoice_number = ' . $gst->invoice_number . ' where id = ?', ['1']);
                    $invoiceLink = url('download-bill/' . $gst->phone_number . '/' . $gst->invoice_number);
                    $bitlyLink = Bitly::getUrl($invoiceLink);
                }
                $order = new Order;
                $order->user_registration = $request['registration'];
                $order->payment_transaction_id = $payment_transaction_id ?? null;
                $order->product_name = $level_name . ' (Subscription Plan)';
                if ($request['is_bill']) {
                    $productPrice = $planPrice + ($planPrice * (accountSettings()->gst_in_percentage / 100));
                } else {
                    $productPrice = $planPrice;
                }
                $tax = isset($tax) ? $tax : 0;
                $order->product_price = $planPrice + $tax;
                $order->order_type = 2;
                $order->order_payment_status = $request['is_paid'] ? 3 : 4;
                $order->product_code = $level_code;
                $order->product_description = "Dealership Plan: $level_name ($level_code)";
                $order->save();
                $order->order_id = getOrderIdSuffix() . (1000 + $order->id);
               
                DB::table('orders')->where('user_registration', $request['registration'])->update(['user_registration' => $registrationNo]);
                DB::table('user_wallet')->where('user_registration', $request['registration'])->update(['user_registration' => $registrationNo]);
                DB::table('user_wallet')->where('rewarded_for', $request['registration'])->update(['rewarded_for' => $registrationNo]);
                DB::table('companies')->where('registration', $request['registration'])->update(['registration' => $registrationNo]);
                DB::table('password_resets')->where('registration', $request['registration'])->update(['registration' => $registrationNo]);
                DB::table('customer_contacts')->where('registration_number', $request['registration'])->update(['registration_number' => $registrationNo]);
                if ($request['referral_code']) {
                    // reward on the basis of the percentage
                    $rewardPrice = $levelData->reward_price == 0 ? 0 : ($planPrice * $levelData->reward_price) / 100;
                    $refferal = User::where('registration', $request['referral_code'])->first();
                    if (!empty($refferal)) {
                        $user->dealer_referal_code = $request['referral_code'];
                        $user->save();
                        if ($request['is_paid']) {
                            $refferal->number_of_dealer_referrals = $refferal->number_of_dealer_referrals + 1;
                            $refferal->wallet = $refferal->wallet + $rewardPrice;
                            $refferal->save();
                            $wallet = new Wallet;
                            $wallet->user_registration = $refferal->registration;
                            $wallet->amount = $rewardPrice;
                            $wallet->remaining = $refferal->wallet;
                            $wallet->transaction_type = 1;
                            $remark = "Reward for $user->name's ($level_name) Dealership";
                            $wallet->transaction_remark = $remark;
                            $wallet->initiated_by = "AUTO";
                            $wallet->base_amount = $planPrice;
                            if ($planPrice != 0)
                                $wallet->reward_percentage = ($rewardPrice / $planPrice) * 100;
                            $wallet->order_id = $order->order_id;
                            $wallet->rewarded_for = $user->registration ?? null;
                            $wallet->save();
                        }
                    }
                }
                if ($levelData->extra_days != 0) {
                    $dealerData = [
                        'dealer_id' => $user->registration,
                        'add_days' => $levelData->extra_days
                    ];
                    $extraDaysResponse = addExtraDayToDealer($dealerData);
                    if ($extraDaysResponse['allDone'] == false) {
                        $error_message = $extraDaysResponse['message'];
                        goto dealerError;
                    }
                    $order->old_start_date = $extraDaysResponse['oldStartDate'];
                    $order->old_end_date = $extraDaysResponse['oldEndDate'];
                    $order->new_start_date = $extraDaysResponse['newStartDate'];
                    $order->new_end_date = $extraDaysResponse['newEndDate'];
                    $order->subscription_status = $extraDaysResponse['isExpired'];
                    $order->days = $extraDaysResponse['days'];
                }
                $order->gst_details_json = !empty($request['gst_details']) ? $request['gst_details'] : null;
                $order->invoice_number = $gst->invoice_number ?? null;
                $productJson = json_encode(compact('level_code', 'level_name', 'planPrice'));
                $order->all_done = 1;
                $order->product_details = $productJson ?? null;
                $order->referral_code = $user->dealer_referal_code ?? null;
                $order->invoice_link = $invoiceLink ?? null;
                $order->bitly_link = $bitlyLink ?? null;
                $order->save();
                $accountData = AccountSettings::first();
                if(!$accountData->app_is_live){
                    $accountData->last_order_id_test = $order->order_id;    
                }else{
                  $accountData->last_order_id_live = $order->order_id;    
                }
                $accountData->save();
                $levelData->counter = $levelData->counter  + 1;
                $levelData->save();

                // Update order's table
                // --------------------

                DB::commit();
            } catch (\Exception $e) {
                $user = null;
                $error_message = $e->getMessage();
                DB::rollback();
            }
            if (is_null($user)) {
                dealerError:
                return response()->json(
                    [
                        'message' => $error_message,
                        'status' => 0,
                    ],
                    500
                );
            } else {
                $user = User::where('registration', $user->registration)->first();
                if (!empty($invoiceLink)) {
                    //$message =  "Dear $user->name,\nYou have successfully registered as $level_name dealer for QartPlus Dealership Program.\nOrder Id: $order->order_id \nYou can collect your Certificate and ID Card from Yugru Solutions.\n Invoice Link: $bitlyLink";
                    $message   = "Dear $user->name,\nYou have successfully registered as $level_name dealer for Qartplus Dealership Program.\nOrder Id: $order->order_id \nYou can collect your Certificate and ID Card from Yugru Solutions. \nInvoice Link: " . ($bitlyLink ?? $invoiceLink) . " \nRegards, \nTeam Qartplus";
                } else {
                    // $message =  "Dear $user->name,\nYou have successfully registered as $level_name dealer for QartPlus Dealership Program.\nOrder Id: $order->order_id \nYou can collect your Certificate and ID Card from Yugru Solutions.";
                    $message   = "Dear $user->name, \nYou have successfully registered as $level_name dealer for Qartplus Dealership Program. \nOrder Id: $order->order_id \nYou can collect your Certificate and IDCard from Yugru Solutions. \nRegards, \nTeam Qartplus";
                }
                $smsRevert = $this->send($user->primary_mobile, $message, true, 1507160933148585548);
                if (isset($user->email)) {
                    $this->sendEmail("Qartplus Dealership Program", $message, $user->email, $user->name);
                }
                return response()->json(
                    [
                        'message' => 'Customer converted to dealer successfully',
                        'status' => 1,
                        'dealer_data' => $user->toArray(),
                        'order_id' => $order->order_id,
                        'invoice_number' => $gst->invoice_number ?? null,
                        'invoice_link' => $invoiceLink ?? null,
                        'bitly_link' => $bitlyLink ?? null,
                        'sms_revert' => json_decode($smsRevert),
                        'grand_total' => $gst->total_value ?? null
                    ],
                    200
                );
            }
        } else {
            return response()->json(
                [
                    'message' => 'Invalid registraion number provided or the user may already be a dealer',
                    'status' => 0,
                ],
                400
            );
        }
    }
    /**
     * To register the dealer / convert a customer into a dealer
     * @author Bhagirath Giri
     * @created_at 14-06-2020
     * @param Request data , $registration
     * @return response json
     */
    public function makeCustomer(Request $request)
    {
        return false;
        $user = User::where(['registration' => $request['registration'], 'status' => 1])->first();
        if (!is_null($user)) {
            $registrationNo = str_replace("D", "R", $user->registration);
            DB::beginTransaction();
            try {
                $user->registration = $registrationNo;
                $user->is_dealer = 0;
                $user->save();
                DB::commit();
            } catch (\Exception $e) {
                $user = null;
                $error_message = $e->getMessage();
                DB::rollback();
            }
            if (is_null($user)) {
                return response()->json(
                    [
                        'message' => $error_message,
                        'status' => 0,
                    ],
                    500
                );
            } else {
                return response()->json(
                    [
                        'message' => 'Dealer converted to customer successfully',
                        'status' => 1,
                        'dealer_data' => $user->toArray(),
                    ],
                    200
                );
            }
        } else {
            return response()->json(
                [
                    'message' => 'Invalid registraion number provided',
                    'status' => 0,
                ],
                400
            );
        }
    }


    /**
     * To login the user
     * @created_at 10-06-2020
     * @author Bhagirath Giri
     * @return response json
     * @param request
     *
     */
    public function login(Request $request)
    {

        $user = User::where(
            [
                'email' => $request['email'],
                'password' => $request['password'],
            ]
        )->first();

        if (is_null($user)) {
            return response()->json(
                [
                    'message' => 'Invalid Credentials',
                    'status' => 0,
                ],
                401
            );
        } else {
            if (!$request['reseller_app']) {
                if($request['email'] != "verify@qartplus.com"){
                    if ($user->device_id != $request['device_id']) {
                        return response()->json(
                            [
                                'message' => 'This Account is registered on other Device. Contact Support to Migrate Account',
                                'status' => 0,
                            ],
                            401
                        );
                    }
                }
                $user->last_login = now();
            } else {
                $user->last_dealer_app_login = now();
            }
            if (!$user->status) {
                return response()->json(
                    [
                        'message' => 'Your account is temporary suspended by QartPlus, Please contact our support',
                        'status' => 0,

                    ],
                    401
                );
            }
            $user->save();
            return response()->json(
                [
                    'message' => 'Login successfull',
                    'status' => 1,
                    'user_data' => $user->toArray(),
                ],
                200
            );
        }
    }
    
    public function loginUser(Request $request)
    {

        $user = User::where(
            [
                'device_id' => $request['device_id'],
            ]
        )->first();

        if (is_null($user)) {
            return response()->json(
                [
                    'message' => 'Device not registered',
                    'status' => 0,
                ],
                401
            );
        } else  {
            $user = User::where(
                [
                'primary_mobile' => $request['primary_mobile'],
                ]
            )->first();
            if (is_null($user)) {
                return response()->json(
                    [
                        'message' => 'Device not registered',
                        'status' => 0,
                    ],
                    401
                );
            } else  {
                if (!$user->status) {
                    return response()->json(
                        [
                            'message' => 'Your account is temporary suspended by QartPlus, Please contact our support',
                            'status' => 0,
    
                        ],
                        401
                    );
                }
                $user->last_login = now();
                $user->save();
                return response()->json(
                    [
                        'message' => 'Login successful',
                        'status' => 1,
                        'user_data' => $user->toArray(),
                    ],
                    200
                );
            }
        }
    }
    /**
     * To change the user's password
     * @created_at 11-06-2020
     * @author Bhagirath Giri
     * @return response json
     * @param request
     *
     */
    public function changePassword(Request $request)
    {
        $user = User::where(['registration' => $request['registration'], 'device_id' => $request['device_id']])->first();
        if (!is_null($user)) {
            $oldPassword = $user->password;
            DB::beginTransaction();
            try {
                $user->password = $request['new_password'];
                $user->save();
                DB::insert('insert into password_resets (registration, old_password, new_password, created_at) values (?, ?, ?, ?)', [$user->registration, $oldPassword, $user->password, date('y-m-d h:i:s')]);
                DB::commit();
            } catch (\Exception $e) {
                $user = null;
                $error_message = $e->getMessage();
                DB::rollback();
            }
            if (is_null($user)) {
                return response()->json(
                    [
                        'message' => $error_message,
                        'status' => 0,
                    ],
                    500
                );
            } else {
                return response()->json(
                    [
                        'message' => 'Password changed successfully',
                        'status' => 1,
                    ],
                    200
                );
            }
        } else {
            return response()->json(
                [
                    'message' => 'Invalid credentails provided',
                    'status' => 0,
                ],
                400
            );
        }
    }

    /**
     * To update the user's data
     * @param Request data
     * @author Bhagirath Giri
     * @create_at 26-Aug-2020
     *
     */
    public function update(Request $request)
    {
        $userRegistration = $request['registration'] ?? null;
        if (!is_null($userRegistration)) {
            $user = User::where('registration', $userRegistration)->first();
            if (!empty($user)) {
                if (!isset($request['admin']) || $request['admin'] != 1) {
                    if ($user->primary_mobile  != $request['primary_mobile']) {
                        return response()->json(
                            [
                                'message' => "Phone number incorrect",
                                'status' => 0,
                            ],
                            400
                        );
                    }
                }
                DB::beginTransaction();
                try {
                    if (isset($request['device_name']) && isset($request['device_id'])) {
                        $userDevice = new UserDevice;
                        $userDevice->registration = $userRegistration;
                        $userDevice->old_device_id = $user->device_id;
                        $userDevice->old_device_name = $user->device_name;
                        $userDevice->save();
                    }
                    $user->device_name = $request['device_name'] ?? $user->device_name;
                    $user->adhar_number = $request['adhar_number'] ?? $user->adhar_number;
                    $user->dob  = date('Y-m-d', strtotime($request['dob'])) ?? null;
                    $user->device_name = $request['device_name'] ?? $user->device_name;
                    $user->secondary_mobile = $request['secondary_mobile'] ?? $user->secondary_mobile;
                    $user->device_id = $request['device_id'] ?? $user->device_id;
                    // $user->address = $request['address'] ?? $user->address;
                    // $user->city = $request['city'] ?? $user->city;
                    // $user->state = $request['state'] ?? $user->state;
                    // $user->country = $request['country'] ?? $user->country;
                    // $user->pin_code = $request['pin_code'] ?? $user->pin_code;
                    // $user->gstin = $request['gstin'] ?? $user->gstin;
                    $user->brand_name = $request['brand_name'] ?? $user->brand_name;
                    $user->operating_system = $request['operating_system'] ?? $user->operating_system;
                    $user->bank_account_ifsc = $request['bank_account_ifsc'] ?? $user->bank_account_ifsc;
                    $user->bank_account_holder = $request['bank_account_holder'] ?? $user->bank_account_holder;
                    $user->bank_account_number = $request['bank_account_number'] ?? $user->bank_account_number;
                    $user->fire_base_token = $request['fire_base_token'] ?? $user->fire_base_token;
                    // $user->business = $request['business'] ?? $user->business;
                    $user->save();
                    DB::commit();
                } catch (\Exception $e) {
                    $user = null;
                    $message = $e->getMessage();
                    DB::rollback();
                }
                if (is_null($user)) {
                    return response()->json([
                        'status' => 0,
                        'message' => $message,
                    ]);
                } else {
                    return response()->json([
                        'status' => 1,
                        'message' => 'Data upated successfully',
                        'user_data' => $user->toArray(),
                    ]);
                }
            } else {
                return response()->json(
                    [
                        'message' => 'User not found with this registration number',
                        'status' => 0,
                    ],
                    400
                );
            }
        } else {
            return response()->json(
                [
                    'message' => 'Please provide user registration number',
                    'status' => 0,
                ],
                400
            );
        }
    }

    /**
     *
     * To get the data of the dealer along with its refered users.
     * @author Bhagirath Giri
     * @created_at 02-09-2020
     *
     */
    public function dealerPannel($dealer)
    {
        if (!empty($dealer)) {
            $dealer = User::where(
                [
                    'registration' => $dealer,
                    'is_dealer' => 1,
                ]
            )
                ->with([
                    'dealer_customers' => function ($q) {
                        $q->select('registration', 'referal_code', 'name', 'email', 'dob', 'primary_mobile', 'secondary_mobile', 'number_of_subscriptions', 'start_date', 'end_date', 'is_paid');
                    }
                ])
                ->first();
            if (!empty($dealer)) {
                $dealer = $dealer->toArray();
                $now = Carbon::now();
                $expiredUsers = [];
                $expiringUsers = [];
                $trailUsers = [];
                $expiredTrailUsers = [];
                foreach ($dealer['dealer_customers'] as $key => $customer) {
                    $endDate = strtotime($customer['end_date']);
                    $todayDate = strtotime($now->toDateString());
                    $days = ($endDate - $todayDate) / 3600 / 24;
                    if ($days <= 0) {
                        // Expired Users
                        $customer['expiry'] = $days == 0 ? "Expired today" : "Expired " . abs($days) . " days ago";
                        $expiredUsers[] = $customer;
                    }
                    if ($days > 0 && $days <= 30) {
                        // Expiring  Users
                        $customer['expiry'] = $days == 1 ? "Expiring tomorrow" :  "Expiring in " . abs($days) . " days";
                        $expiringUsers[] = $customer;
                    }
                    // if ($customer['is_paid'] == 0) {
                    //     // Trail Users
                    //     $trailUsers[] = $customer;
                    // }
                    // if ($days > 0 && $days <= 30 && $customer['is_paid'] == 0) {
                    //     //Expriting trail user
                    //     $expiredTrailUsers[] = $customer;
                    // }
                }
                return response()->json(
                    [
                        'expired_users' => $expiredUsers,
                        'expiring_users' => $expiringUsers,
                        'all_users' => $dealer['dealer_customers'],
                        // 'trail_users' => $trailUsers,
                        // 'expired_trail_users' => $expiredTrailUsers,
                        'status' => 1,
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'message' => 'Dealer not found',
                        'status' => 0,
                    ],
                    404
                );
            }
        }
    }

    /**
     *
     *  To get those dealers whose wallet is above or equal to the minimum withdrawal amount
     *  @author Bhagirath Giri
     *  @created_at 13-09-2020
     *
     */
    public function getPayableDealers()
    {
        $minimun_amount = otherRewardData()->minimun_withdrawal_amount;
        $dealers = User::where('wallet', '>=', $minimun_amount)->orderBy('wallet', 'desc')->get()->toArray();
        return response()->json(
            [
                'dealers_data' => $dealers,
                'status' => 1,
                'total_dealers' => count($dealers),
            ],
            200
        );
    }

    /**
     *  To get the user's order details
     *  @author Bhagirath Giri
     *  @created_at 16-09-2020
     *
     */
    public function getOrders(Request $request)
    {
        $user = User::where('registration', $request['registration'])->first();
        $orderId = $request['order_id'];
        if (!is_null($orderId)) {
            $orders = Order::where('order_id', $orderId)->orderBy('id', 'DESC')->get();
            if (!is_null($orders)) {
                return response()->json(
                    [
                        'orders_count' => count($orders),
                        'orders' => $orders->toArray(),
                        'status' => 1
                    ],
                    200
                );
            } else {
                return response()->json(
                    [
                        'message' => 'Invalid Order Id',
                        'status' => 0
                    ],
                    404
                );
            }
        } elseif (!is_null($user)) {
            $registration = substr($request['registration'], 3);
            $orders = Order::where('user_registration', 'like', '%' . $registration)->orderBy('id', 'DESC')->get();
            return response()->json(
                [
                    'orders_count' => count($orders),
                    'orders' => $orders->toArray(),
                    'status' => 1
                ],
                200
            );
        } else {
            return response()->json(
                [
                    'message' => 'Invalid Registration Number',
                    'status' => 0
                ],
                404
            );
        }
    }

    public function testMail()
    {
        Mail::raw('plain text message', function ($message) {
            $message->from('yugruinfo@gmail.com', 'John Doe');
            $message->sender('yugruinfo@gmail.com', 'John Doe');
            $message->to('giribhagirath169@gmail.com', 'John Doe');
            $message->subject('Subject');
        });
    }
}