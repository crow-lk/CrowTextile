<?php

namespace App\Http\Controllers;

use App\Models\Roll;
use Illuminate\Http\Request;

class rollsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return 'This your index page';
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Roll $roll)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Roll $roll)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Roll $roll)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Roll $roll)
    {
        //
    }
}
