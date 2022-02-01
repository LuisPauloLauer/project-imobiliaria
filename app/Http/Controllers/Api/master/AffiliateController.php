<?php

namespace App\Http\Controllers\Api\master;

use App\Http\Controllers\Controller;
use App\Http\Resources\master\AffiliateResource;
use App\Laravue\Models\master\Affiliate;
use App\Library\PermissionsLibrary;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;

class AffiliateController extends Controller
{
    const ITEM_PER_PAGE = 15;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $verifyPermission = PermissionsLibrary::verifyPermissionMaster();
        if ($verifyPermission['enable'])
        {
            $searchParams = $request->all();
            $affiliateQuery = Affiliate::query();
            $limit = Arr::get($searchParams, 'limit', static::ITEM_PER_PAGE);
            $status = Arr::get($searchParams, 'status', '');
            $keyword = Arr::get($searchParams, 'keyword', '');

            if (!empty($status)) {
                $affiliateQuery->where('status', '=', $status);
            }

            if (!empty($keyword)) {
                $affiliateQuery->where('name', 'LIKE', '%' . $keyword . '%');
            }

            return AffiliateResource::collection($affiliateQuery->paginate($limit));

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
    public function store(Request $request)
    {
        $verifyPermission = PermissionsLibrary::verifyPermissionMaster();
        if ($verifyPermission['enable'])
        {
            $affiliate = new Affiliate();
            $affiliate->status = 'S';
            $affiliate->name = $request->name;
            $affiliate->description = $request->description;

            try {
                $affiliate->save();

                return new AffiliateResource($affiliate);

            } catch (\Exception $exception) {
                return response()->json(['error' => 'Erro ('.$exception->getCode().') ao salvar o afiliado'], 500);
            }

        } else {
            return $verifyPermission['response'];
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Laravue\Models\master\Affiliate  $affiliate
     * @return \Illuminate\Http\Response
     */
    public function show(Affiliate $affiliate)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Laravue\Models\master\Affiliate  $affiliate
     * @return \Illuminate\Http\Response
     */
    public function edit(Affiliate $affiliate)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Laravue\Models\master\Affiliate  $affiliate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Affiliate $affiliate)
    {
        $verifyPermission = PermissionsLibrary::verifyPermissionMaster();
        if ($verifyPermission['enable'])
        {
            if ($affiliate === null) {
                return response()->json(['error' => 'Tipo de afiliado não existe'], 404);
            }

            $affiliate->name = $request->name;
            $affiliate->description = $request->description;

            try {
                $affiliate->save();
            } catch (\Exception $exception) {
                return response()->json(['error' => 'Erro ('.$exception->getCode().') ao alterar o afiliado'], 500);
            }

        } else {
            return $verifyPermission['response'];
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Laravue\Models\master\Affiliate  $affiliate
     * @return \Illuminate\Http\Response
     */
    public function destroy(Affiliate $affiliate)
    {
        $verifyPermission = PermissionsLibrary::verifyPermissionMaster();
        if ($verifyPermission['enable'])
        {
            if ($affiliate === null) {
                return response()->json(['error' => 'afiliado não existe'], 404);
            }

            try {
                $affiliate->delete();
            } catch (\Exception $ex) {
                return response()->json(['error' => $ex->getMessage()], 403);
            }

            return response()->json(null, 204);
        } else {
            return $verifyPermission['response'];
        }
    }

    public function changeStatus(Request $request, Affiliate $affiliate)
    {
        $verifyPermission = PermissionsLibrary::verifyPermissionMaster();
        if ($verifyPermission['enable'])
        {
            if ($affiliate === null) {
                return response()->json(['error' => 'Afiliado não existe'], 404);
            }

            $affiliate->status = $request->status;

            try {
                $affiliate->save();
            } catch (\Exception $exception) {
                return response()->json(['error' => 'Erro ('.$exception->getCode().') ao alterar status do afiliado'], 500);
            }
        } else {
            return $verifyPermission['response'];
        }
    }
}
