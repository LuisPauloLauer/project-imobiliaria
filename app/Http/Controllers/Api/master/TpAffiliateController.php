<?php

namespace App\Http\Controllers\api\master;

use App\Http\Controllers\Controller;
use App\Http\Requests\master\TpAffiliatesFormRequest;
use App\Http\Resources\master\TpAffiliateResource;
use App\Laravue\Models\master\TpAffiliate;
use App\Library\PermissionsLibrary;
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
        $verifyPermission = PermissionsLibrary::verifyPermissionMaster();
        if ($verifyPermission['enable'])
        {
            $searchParams = $request->all();
            $tpAffiliateQuery = TpAffiliate::query();
            $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
            $status = Arr::get($searchParams, 'status', '');
            $keyword = Arr::get($searchParams, 'keyword', '');

            if (!empty($status)) {
                $tpAffiliateQuery->where('status', '=', $status);
            }

            if (!empty($keyword)) {
                $tpAffiliateQuery->where('name', 'LIKE', '%' . $keyword . '%');
            }

            return TpAffiliateResource::collection($tpAffiliateQuery->paginate($limit));

        } else {
            return $verifyPermission['response'];
        }
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
        $verifyPermission = PermissionsLibrary::verifyPermissionMaster();
        if ($verifyPermission['enable'])
        {
            $tpAffiliate = new TpAffiliate();
            $tpAffiliate->status = 'S';
            $tpAffiliate->name = $request->name;
            $tpAffiliate->description = $request->description;

            try {
                $tpAffiliate->save();

                return new TpAffiliateResource($tpAffiliate);

            } catch (\Exception $exception) {
                return response()->json(['error' => 'Erro ('.$exception->getCode().') ao salvar o tipo de afiliado'], 500);
            }

        } else {
            return $verifyPermission['response'];
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
     * @param  \App\Laravue\Models\TpAffiliate  $tpaffiliate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, TpAffiliate $tpaffiliate)
    {
        $verifyPermission = PermissionsLibrary::verifyPermissionMaster();
        if ($verifyPermission['enable'])
        {
            if ($tpaffiliate === null) {
                return response()->json(['error' => 'Tipo de afiliado não existe'], 404);
            }

            $tpaffiliate->name = $request->name;
            $tpaffiliate->description = $request->description;

            try {
                $tpaffiliate->save();
            } catch (\Exception $exception) {
                return response()->json(['error' => 'Erro ('.$exception->getCode().') ao alterar o tipo de afiliado'], 500);
            }

        } else {
            return $verifyPermission['response'];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Laravue\Models\TpAffiliate  $tpAffiliate
     * @return \Illuminate\Http\Response
     */
    public function destroy(TpAffiliate $tpaffiliate)
    {
        $verifyPermission = PermissionsLibrary::verifyPermissionMaster();
        if ($verifyPermission['enable'])
        {
            if ($tpaffiliate === null) {
                return response()->json(['error' => 'Tipo de afiliado não existe'], 404);
            }

            try {
                $tpaffiliate->delete();
            } catch (\Exception $ex) {
                return response()->json(['error' => $ex->getMessage()], 403);
            }

            return response()->json(null, 204);
        } else {
            return $verifyPermission['response'];
        }
    }

    public function changeStatus(Request $request, TpAffiliate $tpaffiliate)
    {
        $verifyPermission = PermissionsLibrary::verifyPermissionMaster();
        if ($verifyPermission['enable'])
        {
            if ($tpaffiliate === null) {
                return response()->json(['error' => 'Tipo de afiliado não existe'], 404);
            }

            $tpaffiliate->status = $request->status;

            try {
                $tpaffiliate->save();
            } catch (\Exception $exception) {
                return response()->json(['error' => 'Erro ('.$exception->getCode().') ao alterar status do tipo de afiliado'], 500);
            }
        } else {
            return $verifyPermission['response'];
        }
    }

    /**
     * Display a listing of the user resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response|ResourceCollection
     */
    public function listWithParams(Request $request)
    {

        // return TpAffiliate::where('status', '=', 'S')->get();

        $verifyPermission = PermissionsLibrary::verifyPermissionMaster();
        if ($verifyPermission['enable'])
        {
            $searchParams = $request->all();
            $status = Arr::get($searchParams, 'status', '');
            $tpAffiliateQuery = TpAffiliate::query();

            if (!empty($status)) {
                $tpAffiliateQuery->where('status', '=', $status);
            }

            //try {
            //    return new JsonResponse([
            //        'listtpaffiliate' => TpAffiliateResource::collection($tpAffiliateQuery),
            //    ]);
            //} catch (\Exception $ex) {
            //    response()->json(['error' => $ex->getMessage()], 403);
            //}

            return TpAffiliateResource::collection($tpAffiliateQuery->get());

        } else {
            return $verifyPermission['response'];
        }
    }
}
