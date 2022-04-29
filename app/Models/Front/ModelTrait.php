<?php

namespace App\Models\Front;

/**
 * Trait Model.
 */
trait ModelTrait
{

    protected  $lc_limit = 'all'; // all or interger
    protected  $lc_paginate = 0; // 0: dont paginate,
    protected  $lc_sort = [];
    protected  $lc_moreWhere = []; // more wehere
    protected  $lc_random = 0; // 0: no random, 1: random
    protected  $lc_keyword = ''; // search search product
    protected  $lc_min_price = ''; // search min price
    protected  $lc_max_price = ''; // search max price
    protected  $lc_attribute = ''; // search attribute product

    /**
     * Set price beetween
     *
     */
    public function setPriceBetween($min,$max) {
        $this->lc_max_price = $max;
        $this->lc_min_price = $min;
        return $this;
    }
    /**
     * Set value limit
     * @param   [string]  $limit 
     */
    public function setLimit($limit) {
        if ($limit === 'all') {
            $this->lc_limit = $limit;
        } else {
            $this->lc_limit = (int)$limit;
        }
        return $this;
    }

    /**
     * Set value sort
     * @param   [array]  $sort ['field', 'asc|desc']
     */
    public function setSort(array $sort) {
        if (is_array($sort)) {
            $this->lc_sort[] = $sort;
        }
        return $this;
    }

    /**
     * Add more where
     * @param   [array]  $moreWhere 
     */
    public function setMoreWhere(array $moreWhere) {
        if (is_array($moreWhere)) {
            if (count($moreWhere) == 2) {
                $where[0] = $moreWhere[0];
                $where[1] = '=';
                $where[2] = $moreWhere[1];
            } elseif (count($moreWhere) == 3) {
                $where = $moreWhere;
            }
            if (count($where) == 3) {
                $this->lc_moreWhere[] = $where;
            }
        }
        return $this;
    }

    /**
     * Enable paginate mode
     *  0 - no paginate
     */
    public function setPaginate() {
        $this->lc_paginate = 1;
        return $this;
    }

    /**
     * Set random mode
     */
    public function setRandom() {
        $this->lc_random = 1;
        return $this;
    }
    
    /**
     * Set keyword search
     * @param   [string]  $keyword 
     */
    public function setKeyword($keyword) {
        if (trim($keyword)) {
            $this->lc_keyword = trim($keyword);
        }
        return $this;
    }

    /**
     * Set attribute search
     * @param   [string]  $keyword 
     */
    public function setAttributes($code) {
        if (trim($code)) {
            $this->lc_attribute = trim($code);
        }
        return $this;
    }
     /**
     * Get Sql
     */
    public function getSql() {
        $query = $this->buildQuery();
        if (!$this->lc_paginate) {
            if ($this->lc_limit !== 'all') {
                $query = $query->limit($this->lc_limit);
            }
        }
		return $query = $query->toSql();
    }

     /**
     * Get data
     * @param   [array]  $action 
     */
    public function getData(array $action = []) {
        $query = $this->buildQuery();
        if (!empty($action['query'])) {
            return $query;
        }
        if ($this->lc_paginate) {
            $data =  $query->paginate(($this->lc_limit === 'all') ? 20 : $this->lc_limit);
        } else {
            if ($this->lc_limit !== 'all') {
                $query = $query->limit($this->lc_limit);
            }
            $data = $query->get();
                
            if (!empty($action['keyBy'])) {
                $data = $data->keyBy($action['keyBy']);
            }
            if (!empty($action['groupBy'])) {
                $data = $data->groupBy($action['groupBy']);
            }

        }
        return $data;
    }

    /**
     * Get full data
     *
     * @return  [type]  [return description]
     */
    public function getFull() {
        if (method_exists($this, 'getDetail')) {
            return $this->getDetail($this->id);
        } else {
            return $this;
        }

    }

}
