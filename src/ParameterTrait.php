<?php

namespace EmeGuan\Resources;

use Illuminate\Container\Container;

trait ParameterTrait
{
    /**
     * Initialize Collection or Resource by reading parameters from url
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    protected function processParameters(){
        $request=Container::getInstance()->make('request');

        if($this instanceof Collection){
            if(count($this)===0)
                return;
            $resource=$this;
        }
        elseif($this instanceof Resource) {
            if (is_null($this->resource))
                return;
            $resource = $this->resource;
        }

        //Main entity
        $resource->principalEntity=str_replace('_', '', $resource->first()->getTable());

        //Fields
        if($request->exists('fields')){
            $entidadKeys=array_keys($request->get('fields'));
            foreach ($entidadKeys as $entidadKey){
                $resource->entities[$entidadKey]=[];
                $atributos=$request->get('fields')[$entidadKey];
                $atributos=explode(',', $atributos);
                foreach ($atributos as $atributo)
                    $resource->entities[$entidadKey][$atributo]=null;
            }
        }

        //Includes
        if($request->exists('include')){
            $resource->includeEntities=explode(',', $request->get('include'));
        }
    }
}
