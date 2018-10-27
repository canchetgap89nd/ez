<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\models\Admin\PackageAndPayment\Package;
use App\models\Admin\PackageAndPayment\Feature;
use App\Http\Requests\Admin\CreatePackageRequest;

class PackageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $packages = Package::paginate(10);
        return view('admin.package.index', compact('packages'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $features = Feature::all();
        return view('admin.package.create', compact('features'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CreatePackageRequest $request)
    {
        $package = new Package();
        $package = $package->fill($request->all());
        $package->save();
        $package->features()->detach();
        $package->features()->attach($request->features);
        return redirect()->route('package.index')->with([
            'status' => 'success',
            'message' => 'Create Success!'
        ]);
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
        $package = Package::find($id);
        $features = Feature::all();
        $allFeature = $package->features()->get();
        $featurePackage = array();
        foreach ($allFeature as $item) {
            array_push($featurePackage, $item->id);
        }
        return view('admin.package.edit', compact('package', 'featurePackage', 'features'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CreatePackageRequest $request, $id)
    {
        $package = Package::find($id);
        $package->update($request->all());
        $package->features()->detach();
        $package->features()->attach($request->features);
        return redirect()->route('package.index')->with([
            'status' => 'success',
            'message' => 'Update Success!'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $package = Package::find($id);
        $package->features()->detach();
        $package->delete();
        return redirect()->route('package.index')->with([
            'status' => 'success',
            'message' => 'Delete Success!'
        ]);
    }
}
