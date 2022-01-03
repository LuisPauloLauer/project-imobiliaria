<?php

namespace App\Http\Controllers\api\master;

use App\Http\Controllers\Controller;
use App\Http\Requests\master\TpAffiliatesFormRequest;
use App\Http\Resources\TpAffiliateResource;
use App\Laravue\Models\TpAffiliate;
use App\Library\FilesControl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Arr;

class TpAffiliateController extends Controller
{
    const ITEM_PER_PAGE = 15;

    /**
     * Display a listing of the user resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|ResourceCollection
     */
    public function index(Request $request)
    {
        $searchParams = $request->all();
        $tpAffiliateQuery = TpAffiliate::query();
        $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
        //$role = Arr::get($searchParams, 'role', '');
        $keyword = Arr::get($searchParams, 'keyword', '');

        //if (!empty($role)) {
        //    $tpAffiliateQuery->whereHas('roles', function($q) use ($role) { $q->where('name', $role); });
        //}

        if (!empty($keyword)) {
            $tpAffiliateQuery->where('name', 'LIKE', '%' . $keyword . '%');
        }

        return TpAffiliateResource::collection($tpAffiliateQuery->paginate($limit));
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
    public function store(TpAffiliatesFormRequest $request)
    {
        $tpAffiliate = new TpAffiliate();
        $tpAffiliate->status = 'S';
        $tpAffiliate->name = $request->name;
        $tpAffiliate->description = $request->description;

        try {
            $tpAffiliate->save();
        } catch (\Exception $exception) {
            return response()->json(['error' => 'Erro ('.$exception->getCode().') ao salvar o tipo de afiliado'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Laravue\Models\TpAffiliate  $tpAffiliate
     * @return \Illuminate\Http\Response
     */
    public function show(TpAffiliate $tpAffiliate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Laravue\Models\TpAffiliate  $tpAffiliate
     * @return \Illuminate\Http\Response
     */
    public function edit(TpAffiliate $tpAffiliate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Laravue\Models\TpAffiliate  $tpAffiliate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TpAffiliate $tpAffiliate)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Laravue\Models\TpAffiliate  $tpAffiliate
     * @return \Illuminate\Http\Response
     */
    public function destroy(TpAffiliate $tpaffiliate)
    {
        try {
            $tpaffiliate->delete();
        } catch (\Exception $ex) {
            return response()->json(['error' => $ex->getMessage()], 403);
        }

        return response()->json(null, 204);
    }
}
