<?php

namespace App\Http\Controllers\Product;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product\ConfigurableOption;
use App\Models\Product\ConfigurableOptionGroup;

class ConfigurableOptionGroupController extends Controller
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
        return view('products.configurable_options.groups.index', compact('configurableOptionGroups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
        return view('products.configurable_options.groups.create');

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
        $request->validate([
            'name' => 'required',
            'description' => 'required',
        ]);

        ConfigurableOptionGroup::create($request->all());

        return back()->with('success', '可配置选项成功添加。');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product\ConfigurableOptionGroup  $configurableOptionGroup
     * @return \Illuminate\Http\Response
     */
    public function show(ConfigurableOptionGroup $configurableOptionGroup)
    {
        //
        return view('products.configurable_options.groups.edit', compact('configurableOptionGroup'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Product\ConfigurableOptionGroup  $configurableOptionGroup
     * @return \Illuminate\Http\Response
     */
    public function edit(ConfigurableOptionGroup $configurableOptionGroup)
    {
        //

        // load option
        // $configurableOptionGroup->load('configurableOption');
        
        return view('products.configurable_options.groups.edit', compact('configurableOptionGroup'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product\ConfigurableOptionGroup  $configurableOptionGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, ConfigurableOptionGroup $configurableOptionGroup)
    {
        //

        $configurableOptionGroup->update($request->all());
        return back()->with('success', '可配置选项成功更新。');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product\ConfigurableOptionGroup  $configurableOptionGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(ConfigurableOptionGroup $configurableOptionGroup)
    {
        //
        $configurableOptionGroup->delete();
        return back()->with('success', '可配置选项成功删除。');
    }
}
