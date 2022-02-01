<?php

namespace App\Laravue\Models\master;

use Illuminate\Database\Eloquent\Model;

class Affiliate extends Model
{
    protected $table = 'affiliates';
    public $alterStatusManual = false;

    public function setZipCodeAttribute($value)
    {
        $this->attributes['zip_code'] = preg_replace('/\D+/', '', $value);
    }

    public function setNumberAttribute($value)
    {
        $this->attributes['number'] = preg_replace('/\D+/', '', $value);
    }

    public function setFoneAttribute($value)
    {
        $this->attributes['fone1'] = preg_replace('/\D+/', '', $value);
        $this->attributes['fone2'] = preg_replace('/\D+/', '', $value);
    }

    public function setStatusAttribute($value)
    {
        $this->attributes['status'] = $value;

        if($this->alterStatusManual){

            $allEstateAgency = $this->allEstateAgencyByAffiliate()->get();

            for ($i=0; $i < count($allEstateAgency); $i++ ){
                $estateAgency =  $allEstateAgency[$i];
                $estateAgency->alterStatusManual   = true;
                $estateAgency->status              = $this->attributes['status'];

                try {
                    if(!$estateAgency->save()){
                        throw new \ErrorException('Erro ao alterar o status da Imobiliária ID ('.$allEstateAgency[$i]->id.').');
                    }
                } catch (\Exception $exception) {
                    throw new \ErrorException('Erro ao alterar o status da Imobiliária ID ('.$allEstateAgency[$i]->id.').');
                } finally {
                    unset($estateAgency);
                }

            }
        }

    }

    public function pesqTpAffiliate()
    {
        return $this->belongsTo(TpAffiliate::class, 'tpaffiliate', 'id');
    }

    public function allEstateAgencyByAffiliate()
    {
        return $this->hasMany(EstateAgency::class, 'affiliate', 'id');
    }

    public function pesqEstateAgencyByAffiliate($pStatus = 'S', $pStore = null, $pOperator = '=')
    {
        $pStatus = strtoupper($pStatus);

        if(is_null($pStore)){
            return $stores = $this->allEstateAgencyByAffiliate()->where('stores.status', $pStatus);
        } else {
            return $stores = $this->allEstateAgencyByAffiliate()->where('stores.status', $pStatus)->where('stores.id', $pOperator, $pStore);
        }

    }
}
