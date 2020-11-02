<?php

namespace App\Http\Controllers;

use App\member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\setting;
use DB;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

class adminchangelogoController  extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $mainactive = "setting";
        $subactive  = "changelogo";
        $logo       = DB::table('settings')->value('logo');
        $changelogo = setting::first();
        return view('admin.setting.changelogo', compact('changelogo', 'mainactive', 'logo', 'subactive'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    { {
            $alltokens = member::where('firebase_token', '!=', null)->where('firebase_token', '!=', 0)->get();
            foreach ($alltokens as $usertoken) {
                $optionBuilder = new OptionsBuilder();
                $optionBuilder->setTimeToLive(60 * 20);

                $notificationBuilder = new PayloadNotificationBuilder('إشعار جديد من الأمانة');
                $notificationBuilder->setBody($request->notification)
                    ->setSound('default');

                $dataBuilder = new PayloadDataBuilder();
                $dataBuilder->addData(['a_type' => 'message']);
                $option       = $optionBuilder->build();
                $notification = $notificationBuilder->build();
                $data         = $dataBuilder->build();
                $token        = $usertoken->firebase_token;

                $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

                $downstreamResponse->numberSuccess();
                $downstreamResponse->numberFailure();
                $downstreamResponse->numberModification();
                $downstreamResponse->tokensToDelete();
                $downstreamResponse->tokensToModify();
                $downstreamResponse->tokensToRetry();
            }
            return back();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        $this->validate($request, [
            'logo'       => 'image',
        ]);

        $upinfo = setting::find($id);

        $upinfo->website  = $request->website;
        $upinfo->twitter   = $request->twitter;
        $upinfo->instgram = $request->instgram;
        $upinfo->snapchat   = $request->snapchat;
        $upinfo->mobile   = $request->mobile;
        $upinfo->phone = $request->phone;
        $upinfo->whatsapp   = $request->whatsapp;



        if ($request->hasFile('logo')) {
            $image    = $request['logo'];
            $filename = rand(0, 9999) . '.' . $image->getClientOriginalExtension();
            $image->move(base_path('users/images/'), $filename);
            $upinfo->logo = $filename;
        }
        $upinfo->save();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}