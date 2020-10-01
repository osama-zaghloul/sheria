<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController as BaseController;
use Illuminate\Http\Request;
use Validator;
use DB;
use App\notification;
use App\setting;
use App\contact;
use App\slider;
use App\item;
use App\item_image;
use App\member;
use App\City;
use App\maincategory;
use Settings;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use LaravelFCM\Facades\FCM;

class appsettingController  extends BaseController
{
    public function settingindex(Request $request)
    {

        $jsonarr              = array();
        $setting              = setting::all();
        $jsonarr['info']      = $setting;
        return $this->sendResponse('success', $jsonarr);
    }

    public function contactus(Request $request)
    {
        $newcontact          = new contact();
        $newcontact->name    = $request->name;
        $newcontact->email   = $request->email;
        $newcontact->message = $request->message;
        $newcontact->save();
        $errormessage =  'تم ارسال الرسالة بنجاح';
        return $this->sendResponse('success', $errormessage);
    }

    public function categories(Request $request)
    {
        $cats = maincategory::all();
        return $this->sendResponse('success', $cats);
    }

    public function items(Request $request)
    {

        $listitems = array();
        $items = item::where('suspensed', 0)->where('category_id', $request->category_id)->orderBy('id', 'desc')->get();
        if ($items && count($items) > 0) {
            foreach ($items as $item) {
                $image     = item_image::where('item_id', $item->id)->first();

                array_push(
                    $listitems,
                    array(
                        "id"           => $item->id,
                        'image'        => $image,
                        'title'        => $item->artitle,
                        'discount'     => $item->discountprice,
                        'details'      => $item->details,
                    )
                );
            }

            return $this->sendResponse('success', $listitems);
        } else {
            return $this->sendResponse('success', 'لا يوجد منتجات في هذا القسم ');
        }
    }
}