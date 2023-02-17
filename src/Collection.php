<?php

namespace EmeGuan\Resources;

use Illuminate\Container\Container;
use Illuminate\Http\Resources\Json\ResourceCollection;

class Collection extends ResourceCollection
{
    use ParameterTrait;

    private $principalEntity=null;
    private $entities=[];
    private $includeEntities=[];

    function __construct($resource, $principalEntity=null, $entities=[], $includeEntities=[]) {
        parent::__construct($resource);

        if(func_num_args()===1)
            $this->processParameters();
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
