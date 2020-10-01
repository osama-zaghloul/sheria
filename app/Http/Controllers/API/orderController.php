<?php

namespace App\Http\Controllers\API;

use App\Events\alertNot;
use App\Http\Controllers\API\BaseController as BaseController;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use Illuminate\Support\Facades\Auth;
use Validator;
use Illuminate\Http\Request;
use App\member;
use App\notification;
use App\item;
use App\item_image;
use App\order;
use App\setting;
use Carbon\Carbon;
use DB;


class orderController extends BaseController
{

    public function makeorder(Request $request)
    {
        $user = member::where('id', $request->user_id)->first();
        if ($user) {
            $inv_code_order = order::where('code_out', $neworder->invite_code)->where('item_id', $request->item_id)->first();
            if ($inv_code_order) {
                $neworder                = new order();
                $neworder->user_id       = $request->user_id;
                $neworder->item_id       = $request->item_id;
                $neworder->name         = $request->name;
                $neworder->identity          = $request->identity;
                $neworder->address          = $request->address;
                $neworder->phone          = $request->phone;
                $neworder->invite_code          = $request->invite_code;
                $neworder->code_out          = date('dmY') . rand(0, 999);;
                $neworder->save();



                $notification                = new notification();
                $notification->user_id       = $request->user_id;
                $notification->notification  = 'تم إنشاء طلب بطاقة جديد';
                $notification->save();



                $code_owner = member::where('id', $inv_code_order->user_id)->first();
                if ($code_owner) {

                    $points =  setting::select(['points']);
                    //increase his points
                    $code_owner->points += $points;

                    //send notification
                    $notification                = new notification();
                    $notification->user_id       = $code_owner->id;
                    $notification->notification  = 'لقد تم إستعمال الكود الخاص بك ..وقد أضيف إلى حسابك   ' . $points . 'نقاط';
                    $notification->save();

                    //send notification by firebase
                    $optionBuilder = new OptionsBuilder();
                    $optionBuilder->setTimeToLive(60 * 20);

                    $notificationBuilder = new PayloadNotificationBuilder('تم إضافة نقطة إلى حسابك');
                    $notificationBuilder->setBody($request->notification)
                        ->setSound('default');

                    $dataBuilder = new PayloadDataBuilder();
                    $dataBuilder->addData(['a_type' => 'message']);
                    $option       = $optionBuilder->build();
                    $notification = $notificationBuilder->build();
                    $data         = $dataBuilder->build();
                    $token        = $code_owner->firebase_token;

                    $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

                    $downstreamResponse->numberSuccess();
                    $downstreamResponse->numberFailure();
                    $downstreamResponse->numberModification();
                    $downstreamResponse->tokensToDelete();
                    $downstreamResponse->tokensToModify();
                    $downstreamResponse->tokensToRetry();
                }


                $usertoken = member::where('id', $request->user_id)->where('firebase_token', '!=', null)->where('firebase_token', '!=', 0)->value('firebase_token');
                if ($usertoken) {
                    $optionBuilder = new OptionsBuilder();
                    $optionBuilder->setTimeToLive(60 * 20);

                    $notificationBuilder = new PayloadNotificationBuilder('إنشاء طلب جديد');
                    $notificationBuilder->setBody($request->notification)
                        ->setSound('default');

                    $dataBuilder = new PayloadDataBuilder();
                    $dataBuilder->addData(['a_type' => 'message']);
                    $option       = $optionBuilder->build();
                    $notification = $notificationBuilder->build();
                    $data         = $dataBuilder->build();
                    $token        = $usertoken;

                    $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

                    $downstreamResponse->numberSuccess();
                    $downstreamResponse->numberFailure();
                    $downstreamResponse->numberModification();
                    $downstreamResponse->tokensToDelete();
                    $downstreamResponse->tokensToModify();
                    $downstreamResponse->tokensToRetry();
                }



                $errormessage = 'تم ارسال الطلب بنجاح';
                $msg['code'] = $neworder->code_out;
                $msg['message'] = $errormessage;
                return $this->sendResponse('success', $msg);
            } else {
                $errormessage = 'الكود غير متاح';
                return $this->sendError('success', $errormessage);
            }
        } else {
            $errormessage = 'المستخدم غير موجود';
            return $this->sendError('success', $errormessage);
        }
    }

    public function myorders(Request $request)
    {
        $user = member::where('id', $request->user_id)->first();
        if ($user) {
            $myorders = order::where('user_id', $request->user_id)->get();
            if (count($myorders) != 0) {
                $orderarr = array();
                foreach ($myorders as $myorder)
                    $item =  item::where('id', $myorder->item_id)->first();
                array_push(
                    $orderarr,
                    array(
                        "id"           => $myorder->id,
                        'name'        => $myorder->name,
                        'identity'        => $myorder->identity,
                        'address'        => $myorder->address,
                        'phone'        => $myorder->phone,
                        'identity'        => $myorder->identity,
                        'address'        => $myorder->address,
                        'invite_code'        => $myorder->invite_code,
                        'code_out'        => $myorder->code_out,
                        'item'        => $item,
                    )
                );
                return $this->sendResponse('success', $orderarr);
            } else {
                $errormessage = 'لا يوجد طلبات';
                return $this->sendError('success', $errormessage);
            }
        } else {
            $errormessage = 'هذا المستخدم غير موجود';
            return $this->sendError('success', $errormessage);
        }
    }

    // public function showorder(Request $request)
    // {
    //     $showorder = order::where('id', $request->order_id)->first();
    //     if ($showorder) {

    //         $userinfo     = member::where('id', $showorder->user_id)->first();
    //         $orderitems   = order_item::where('order_id', $showorder->id)->get();
    //         $orderdetails = array();
    //         $itemarr      = array();

    //         foreach ($orderitems as $item) {
    //             $orderitem = item::where('id', $item->item_id)->first();
    //             $image     = item_image::where('item_id', $item->item_id)->value('image');
    //             $cutting = Cutting::where('id', $item->cutting_id)->first();
    //             $weight = weight::where('id', $item->weight_id)->first();
    //             if ($weight) {
    //                 array_push(
    //                     $itemarr,
    //                     array(
    //                         "id"           => $orderitem->id,
    //                         'image'        => $image,
    //                         'title'        => $orderitem->artitle,
    //                         'price'        => $item->price,
    //                         'qty'          => $item->qty,
    //                         'cutting'      => $cutting->cutting_name,
    //                         'weight'       => $weight->weight_name,
    //                         'skin'         => $item->skin,
    //                         'packaging'    => $item->packaging,
    //                         'skin'         => $item->skin,
    //                         'minced'       => $item->minced,
    //                         'place'       => $item->place,
    //                         'date'       => $item->date,
    //                         'time'       => $item->time,
    //                         'notes'        => $item->notes,
    //                         'bowels'        => $item->bowels,
    //                         'deliverDay'        => $item->deliverDay,
    //                         'headType'        => $item->headType,
    //                     )
    //                 );
    //             } else {
    //                 array_push(
    //                     $itemarr,
    //                     array(
    //                         "id"           => $orderitem->id,
    //                         'image'        => $image,
    //                         'title'        => $orderitem->artitle,
    //                         'price'        => $item->price,
    //                         'qty'          => $item->qty,
    //                         'cutting'      => $cutting->cutting_name,
    //                         'skin'         => $item->skin,
    //                         'packaging'    => $item->packaging,
    //                         'skin'         => $item->skin,
    //                         'minced'       => $item->minced,
    //                         'place'       => $item->place,
    //                         'date'       => $item->date,
    //                         'time'       => $item->time,
    //                         'notes'        => $item->notes,
    //                         'bowels'        => $item->bowels,
    //                         'deliverDay'        => $item->deliverDay,
    //                         'headType'        => $item->headType,
    //                     )
    //                 );
    //             }
    //         }
    //         array_push(
    //             $orderdetails,
    //             array(
    //                 "id"            => $showorder->id,
    //                 "order_number"  => $showorder->order_number,
    //                 "user_id"       => $showorder->user_id,
    //                 "user_name"     => $userinfo->name,
    //                 "user_address"  => $userinfo->address,
    //                 "total"         => $showorder->total,
    //                 "status"        => $showorder->status,
    //                 "paid"          => $showorder->paid,
    //                 "created_at"    => $showorder->created_at,
    //                 "items"         => $itemarr,
    //             )
    //         );
    //         return $this->sendResponse('success', $orderdetails);
    //     } else {
    //         $errormessage = 'الطلب غير موجود';
    //         return $this->sendError('success', $errormessage);
    //     }
    // }
}