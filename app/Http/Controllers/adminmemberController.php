<?php

namespace App\Http\Controllers;

use App\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Hash;
use App\Mail\notificationmail;
use App\Mail\contactmail;
use Illuminate\Support\Facades\Mail;
use App\member;
use App\item;
use App\notification;
use App\order;
use Carbon\Carbon;
use DB;


class adminmemberController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mainactive = 'users';
        $subactive  = 'user';
        $logo       = DB::table('settings')->value('logo');
        $allusers   = member::orderBy('id', 'desc')->get();
        return view('admin.users.index', compact('mainactive', 'subactive', 'logo', 'allusers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $mainactive = 'users';
        $subactive  = 'adduser';
        $logo       = DB::table('settings')->value('logo');
        $cities = City::all();
        return view('admin.users.create', compact('mainactive', 'logo', 'subactive', 'cities'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
            'name'        => 'required',
            'email'       => 'required|unique:members',
            'city_id'       => 'required',
            'phone'       => 'required|unique:members',
            'pass'        => 'required|min:6',
            'confirmpass' => 'required|same:pass',
        ]);

        $newmember            = new member;
        $newmember->name      = $request['name'];
        $newmember->email      = $request['email'];
        $newmember->phone     = $request['phone'];
        $newmember->city_id   = $request['city_id'];
        $newmember->password  = Hash::make($request['pass']);
        $newmember->code        = date('dmY') . rand(0, 999);
        $newmember->save();
        session()->flash('success', 'تم اضافة عضو بنجاح');
        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $mainactive        = 'users';
        $subactive         = 'user';
        $logo              = DB::table('settings')->value('logo');
        $showuser          = member::find($id);
        $myorders          = order::where('user_id', $id)->orderBy('id', 'desc')->get();
        $mytotal           = 0;
        $city = City::where('id', $showuser->city_id)->first();
        return view('admin.users.show', compact('mainactive', 'subactive', 'logo', 'showuser', 'myorders', 'mytotal', 'city'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $mainactive = 'users';
        $subactive  = 'edituser';
        $logo       = DB::table('settings')->value('logo');
        $eduser     = member::find($id);
        $cities = City::all();
        return view('admin.users.edit', compact('mainactive', 'subactive', 'logo', 'eduser', 'cities'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $upmember = member::find($id);

        if (Input::has('suspensed')) {
            if ($upmember->suspensed == 0) {
                DB::table('members')->where('id', $id)->update(['suspensed' => 1]);
                session()->flash('success', 'تم تعطيل عضوية العضو بنجاح');
                return back();
            } else {
                DB::table('members')->where('id', $id)->update(['suspensed' => 0]);
                session()->flash('success', 'تم تفعيل عضوية العضو بنجاح');
                return back();
            }
        } else {
            $this->validate($request, [
                'name'        => 'required',
                'email'       => 'required|unique:members,email,' . $id,
                'phone'       => 'required|unique:members,phone,' . $id,
                'city_id'       => 'required',
                'confirmpass' => 'same:pass',
            ]);

            $upmember->name      = $request['name'];
            $upmember->email     = $request['email'];
            $upmember->phone     = $request['phone'];
            $upmember->city_id   = $request['city_id'];
            $upmember->password  = $request['pass'] ? Hash::make($request['pass']) : $upmember->password;
            $upmember->save();
            session()->flash('success', 'تم تعديل بيانات العضو بنجاح');
            return back();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        $deluser = member::find($id);
        if ($deluser) {
            $deluser->delete();
            notification::where('user_id', $deluser->id)->delete();
            $orders = order::where('user_id', $deluser->id)->get();

            foreach ($orders as $order) {
                $order->delete();
            }
            session()->flash('success', 'تم حذف العضو بنجاح');
        }
        return back();
    }

    public function deleteAll(Request $request)
    {
        $ids = $request->ids;
        $users =   DB::table("members")->whereIn('id', explode(",", $ids))->get();
        foreach ($users as $user) {
            notification::where('user_id', $user->id)->delete();
            order::where('user_id', $user->id)->delete();
            $user->delete();
        }
        return response()->json(['success' => "تم الحذف بنجاح"]);
    }
}