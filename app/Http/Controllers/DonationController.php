<?php

namespace App\Http\Controllers;

use App\Donation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Validator;

class DonationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $donations = Donation::all();
        return new Response($donations->toArray(), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|integer',
            'amount' => 'required|numeric',
            'currency_id' => 'required|integer',
        ]);

        if ($validator->fails()) {
            return new Response($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $donation = new Donation();
        $donation->amount = $request->amount;
        $donation->user_id = $request->user_id;
        $donation->currency_id = $request->currency_id;

        $donation->save();

        return new Response($donation->toArray(), Response::HTTP_CREATED);
    }

    /**
     * @param $id
     *
     * @return Response
     */
    public function getByUser($id)
    {
        $donations = DB::table('donations')->where('user_id', '=', $id)->get();
        return new Response($donations->toArray(), 200);
    }
}
