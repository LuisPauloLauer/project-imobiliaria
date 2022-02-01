<?php

namespace App\Http\Resources\master;

use App\Laravue\Models\master\TpAffiliate;
use Illuminate\Http\Resources\Json\JsonResource;

class AffiliateResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'status' => $this->status, //($this->status == "S" ? true : false),
            'tpaffiliate' => $this->tpaffiliate,
            'tpaffiliate_name' => $this->pesqTpAffiliate->name,
            'type_person' => $this->type_person,
            'corporate_name' => $this->corporate_name,
            'fantasy_name' => $this->fantasy_name,
            'zip_code' => $this->zip_code,
            'street' => $this->street,
            'number' => $this->number,
            'district' => $this->district,
            'complement' => $this->complement,
            'city' => $this->city,
            'fone1' => $this->fone1,
            'fone2' => $this->fone2,
            'cpf_or_cnpj' => ($this->type_person === 'PF' ? $this->cpf : $this->cnpj),
            'email' => $this->email,
        ];
    }
}
