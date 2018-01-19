<?php

namespace App\Http\Controllers;

use App\Report;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $reports = Report::all();
        return new Response($reports->toArray(), 200);
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
    {
        $report = Report::where('git_repository', '=', $request->git_repository)->first();

        // todo check if url exist, check if domain name is github

        if ($report instanceof Report) {
            return $this->update($request, $report);
        }

        $report = new Report();

        $report->git_repository = $request->git_repository;

        // todo create the report

        $report->save();

        if ($request->user_id !== null) {
            $report->users()->attach($request->user_id);
        }

        return new Response($report->toArray(), 201);
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
        $report = Report::find($id)->first();

        if ($request->user_id !== null) {
            $report->users()->attach($request->user_id);
        }

        // todo create the report
        // $report->filename('test')

        return new Response($report->toArray(), 201);
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

    /**
     * @param $id
     *
     * @return Response
     */
    public function getByUser($id)
    {
        $users = User::find($id)->first();
        $reports = array();
        foreach ($users->reports as $report) {
            $reports[] = $report->toArray();
        }

        return new Response($reports, 200);
    }
}
