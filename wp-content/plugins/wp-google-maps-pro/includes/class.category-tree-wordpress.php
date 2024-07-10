<?php

namespace WPGMZA;

class CategoryTreeWordPress extends CategoryTree
{
	public function __construct()
	{
		CategoryTree::__construct();
		
		$this->id = 0;
		$this->category_name = __('All Categories', 'wp-google-maps');
		
		$this->build($this);
	}
	
	private function build($parent)
	{
		$args = array(
			"hide_empty"		=> false,
			"orderby"			=> "name",
			"order"				=> "ASC",
			"parent"			=> $parent->id
		);
		
		if($categories = get_categories($args))
		{
			foreach($categories as $category)
			{
				$node = new CategoryTreeNode();
				
				$node->id				= $category->term_id;
				$node->category_name	= $category->name;
				$node->marker_count		= $category->count;
				
				$node->parent			= $parent;
				$parent->children		[]= $node;
				
				$this->build($node);
			}
		}
	}
	
	public function getManyToManyMarkerIDFieldName()
	{
		return "object_id";
	}
	
	public function getCategoryIDFieldName()
	{
		return "term_taxonomy_id";
	}
	
	public function getManyToManyTableName()
	{
		global $wpdb;
		return "{$wpdb->prefix}term_relationships";
	}

	public function applyFilteringClauseToQuery($query, $categories)
	{
		global $wpdb;
		
		if(empty($categories))
			return;
		
		if(is_int($categories))
			$categories = array($categories);
		
		$placeholders	= implode(',', array_fill(0, count($categories), '%d'));
		
		if(empty($categories))
			return;
		
		$categoryIDs	= array();
		
		foreach($categories as $category)
		{
			$categoryIDs[]	= $category;
			$node			= $this->getChildByID($category);
			
			if(!$node)
				continue;
			
			foreach($node->getDescendants() as $descendant)
				$categoryIDs[] = $descendant->id;
		}
		
		$imploded	= implode(',', array_unique($categoryIDs));
		$queries	= array();
		
		$operator						= $this->getFilteringOperator();
		$markerIDFieldName				= $this->getMarkerIDFieldName($query);
		$manyToManyMarkerIDFieldName	= $this->getManyToManyMarkerIDFieldName();
		$categoryIDFieldName			= $this->getCategoryIDFieldName();
		$manyToManyTableName			= $this->getManyToManyTableName();
		
		for($i = 0; $i < count($categoryIDs); $i++)
		{
			$nativeInclusion = "id IN (SELECT marker_id FROM {$wpdb->prefix}wpgmza_markers_has_categories WHERE category_id = %d)";

			$queries[] = "
				$markerIDFieldName IN 
				(
					SELECT $manyToManyMarkerIDFieldName
					FROM $manyToManyTableName
					WHERE $categoryIDFieldName = %d
				) OR $nativeInclusion
			";
			
			$query->params[] = $categoryIDs[$i];
			$query->params[] = $categoryIDs[$i];
		}
		
		$query->where['categories'] = "(" . implode(" $operator ", $queries) . ")";
	}
}