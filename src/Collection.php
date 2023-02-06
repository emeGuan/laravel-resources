<?php

namespace EmeGuan\Resources;

use Illuminate\Container\Container;
use Illuminate\Http\Resources\Json\ResourceCollection;

class Collection extends ResourceCollection
{
    private $principalEntity=null;
    private $entities=[];
    private $includeEntities=[];

    function __construct($resource, $principalEntity=null, $entities=[], $includeEntities=[]) {
        parent::__construct($resource);

        if(func_num_args()===1){
            $request=Container::getInstance()->make('request');

            //Main entity
            $this->principalEntity=$request->path();

            //Fields
            if($request->exists('fields')){
                $entidadKeys=array_keys($request->get('fields'));
                foreach ($entidadKeys as $entidadKey){
                    $this->entities[$entidadKey]=[];
                    $atributos=$request->get('fields')[$entidadKey];
                    $atributos=explode(',', $atributos);
                    foreach ($atributos as $atributo)
                        $this->entities[$entidadKey][$atributo]=null;
                }
            }

            //Includes
            if($request->exists('include')){
                $this->includeEntities=explode(',', $request->get('include'));
            }
        }
        else{
            $this->principalEntity=$principalEntity;
            $this->entities=$entities;
            $this->includeEntities=$includeEntities;
        }
    }

    public function toArray($request){
        foreach ($this->collection as $resource){
            $resource->resource->principalEntity=$this->principalEntity;
            $resource->resource->entities=$this->entities;
            $resource->resource->includeEntities=$this->includeEntities;
        }
        return $this->collection;
    }
}
