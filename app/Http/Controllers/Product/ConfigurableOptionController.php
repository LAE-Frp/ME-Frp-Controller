<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\ConfigurableOption;
use App\Models\Product\ConfigurableOptionGroup;
use Illuminate\Http\Request;

class ConfigurableOptionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $configurableOptionGroups = ConfigurableOptionGroup::all();
        return view('products.configurable_options.index', compact('configurableOptionGroups'));
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
    public function store(Request $request, ConfigurableOptionGroup $configurableOptionGroup)
    {
        //
        $request->validate([
            'name' => 'required',
            'display_name' => 'required',
            'description' => 'required',
            'is_hidden' => 'boolean',
            'type' => 'string|in:text,dropdown,boolean,quantity,checkbox',


            // 验证 regex 是否是正则表达式
            'regex' => 'string|regex:/^\/.*\/[gimuy]*$/',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product\ConfigurableOption  $configurableOption
     * @return \Illuminate\Http\Response
     */
    public function show(ConfigurableOption $configurableOption)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product\ConfigurableOption  $configurableOption
     * @return \Illuminate\Http\Response
     */
    public function edit(ConfigurableOption $configurableOption)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product\ConfigurableOption  $configurableOption
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ConfigurableOption $configurableOption)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product\ConfigurableOption  $configurableOption
     * @return \Illuminate\Http\Response
     */
    public function destroy(ConfigurableOption $configurableOption)
    {
        //
    }
}
