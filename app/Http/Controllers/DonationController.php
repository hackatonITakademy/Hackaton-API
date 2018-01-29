<?php

namespace App\Http\Controllers;

use App\Donation;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

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
        if ($request->user_id === null) {
            return new Response(array(
                'message' => 'You have to be logged for donate.',
                'status_code' => Response::HTTP_FORBIDDEN,
            ), Response::HTTP_FORBIDDEN);
        }

        if (!isset($request->amount) || $request->amount === null) {
            return new Response(array(
               'message' => 'The amount can\'t be empty',
               'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        if (!isset($request->currency_id) || $request->currency_id === null) {
            return new Response(array(
                'message' => 'The currency can\'t be empty',
                'status_code' => Response::HTTP_UNPROCESSABLE_ENTITY,
            ), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $donation = new Donation();
        $donation->amount = $request->amount;
        $donation->user_id = $request->user_id;
        $donation->currency_id = $request->currency_id;

        $donation->save();

        return new Response($donation->toArray(), 201);
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
