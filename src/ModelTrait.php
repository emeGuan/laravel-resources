<?php

namespace EmeGuan\Resources;

trait ModelTrait
{
    /**
     * The main entity
     *
     * @var string
     */
    public $principalEntity;

    /**
     * The fields of each entity
     *
     * @var array
     */
    public $entities=[];

    /**
     * Included entities
     *
     * @var array
     */
    public $includeEntities=[];
}
