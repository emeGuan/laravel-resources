<?php

namespace EmeGuan\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Resource extends JsonResource
{
    use ParameterTrait;

    private function processIncludeEntities($includeEntities, &$entities)
    {
        $entities=[];
        foreach ($includeEntities as $relation){
            $split=explode('.', $relation, 2);
            if(isset($split[1]))
                $entities[$split[0]][]=$split[1];
            else
                $entities[$split[0]]=[];
        }
    }


    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $resource=$this->resource;
        if(is_null($resource))
            return null;

        //Process parameters. Call without Collection
        if(is_null($resource->principalEntity))
            $this->processParameters();

        $fields = $resource->entities[$resource->principalEntity] ?? [];
        if (count($fields) != 0) {
            foreach ($fields as $key => $field) {
                //The fields come empty
                if (empty($key)) {
                    $resource->entities[$resource->principalEntity] = [];
                    break;
                }
                //it's a relationship
                if (is_subclass_of($this[$key], 'Illuminate\\Database\\Eloquent\\Model')) {
                    $attributes = $this[$key]->getAttributes();
                    if(array_key_exists($key, $resource->entities)){
                        $attributes = array_intersect_key($attributes, $resource->entities[$key]);
                        $this[$key]->setRawAttributes($attributes, true);
                    }
                    $resource->entities[$resource->principalEntity][$key] = $this[$key]->getAttributes();
                }
                //*, all fields, but no relationship
                if ($key==='*'){
                    $attributes=$resource->getAttributes();
                    $resource->entities[$resource->principalEntity] = $attributes;
                }
                //Normal field
                else
                    $resource->entities[$resource->principalEntity][$key] = $this[$key];
            }
        }
        //If there are no fields we assign all
        else{
            $attributes=$resource->getAttributes();
            $resource->entities[$resource->principalEntity] = $attributes;
        }
        if(count($resource->includeEntities)){
            $this->processIncludeEntities($resource->includeEntities, $includeEntities);
            foreach ($includeEntities as $key => $relation){
                if(isset($this[$key]))
                    $resource->entities[$resource->principalEntity][$key]=new Collection($this[$key], $key, $resource->entities, $relation);
            }
        }
        return $resource->entities[$resource->principalEntity];
    }
}
