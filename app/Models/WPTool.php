<?php

# WordPress Integration Functions

	# [tmp] 2025-07-21 Monday: work on it

function wpGetOrCreateTermId($term,$taxonomy){
    $id = wpGetTermId($term,$taxonomy);
    return ($id) ? $id : wpCreateTerm($term,$taxonomy);
}

function wpGetTermId($term,$taxonomy){
    global $table_prefix;
    
    $term_slug      = stringfy($term);
    $term_taxonomy  = $table_prefix . 'term_taxonomy';
    $terms          = $table_prefix . 'terms';

    $query  = "SELECT term_taxonomy_id FROM $term_taxonomy AS t WHERE taxonomy='$taxonomy' AND EXISTS";
    $query .= " (SELECT term_id FROM $terms WHERE term_id=t.term_id AND slug='$term_slug') LIMIT 1";
    $u = query($query);
    return $u['term_taxonomy_id'] ?? null;
}

function wpCreateTerm($term,$taxonomy){
    global $table_prefix;
    $term_slug = stringfy($term);
    # Insert term
    $values = [
        'name' => $term,
        'slug' => $term_slug
    ];
    $id = insert($table_prefix . 'terms',$values);
    # Insert taxonomy
    $values = [
        'term_id'       => $id,
        'taxonomy'      => $taxonomy,
        'description'   => '',
        'parent'        => 0,
        'count'         => 0
    ];
    return insert($table_prefix . 'term_taxonomy',$values);
}

function wpInsertTermPostRelationship($id,$term_id){
    global $table_prefix;
    $values = [
        'object_id'         => $id,
        'term_taxonomy_id'  => $term_id
    ];
    $u = insert($table_prefix . 'term_relationships',$values);
    return ($u!==false);
}

function wpGetTermPostRelationships($id){
    global $table_prefix;
    
    $terms_tax_ids = selectColumn($table_prefix . 'term_relationships','term_taxonomy_id',"WHERE object_id=?;",[$id]);
    
    if (!$terms_tax_ids) {
        return null;
    }
    
    foreach ($terms_tax_ids as $term_tax_id) {
        $tmp = selectRow($table_prefix . 'term_taxonomy','term_id,taxonomy',"WHERE term_taxonomy_id=?;",[$term_tax_id]);
        if ($tmp) {
            $term_id  = $tmp['term_id'];
            $taxonomy = $tmp['taxonomy'];
            $n[$taxonomy] = field($table_prefix . 'terms','name',"WHERE term_id=?;",[$term_id]);
        }
    }
    
    return $n ?? null;
}

function wpPostmetaInsert($id,$key,$value){
    global $table_prefix;
    $values = [
        'post_id'       => $id,
        'meta_key'      => $key,
        'meta_value'    => $value
    ];
    return insert($table_prefix . 'postmeta',$values);
}

function wpGetPostmeta($id,$key){
	global $table_prefix;
	return field($table_prefix . 'postmeta','meta_value',"WHERE post_id=? AND meta_key=?;",[$id,$key]);
}

function wpUpdatePostmeta($id,$key,$value){
	global $table_prefix;
    $values = [
        'meta_value'    => $value
    ];
	$cond = "post_id=? AND meta_key=?;";
	return update($table_prefix . 'postmeta',$values,$cond,[$id,$key]);
}

function wpGetUserByName($name){
    global $table_prefix;
    $cond = 'WHERE user_nicename=?';
    return selectRow($table_prefix . 'users','*',$cond,[$name]);
}

function wpGetField($table,$field,$cond,$values){
    global $table_prefix;
    return field($table_prefix . $table,$field,$cond,$values);
}

function getCategories(){
	$terms_id = selectColumn('wp_term_taxonomy','term_id','WHERE taxonomy="category"');
	foreach ($terms_id as $term_id) {
		$term = field('wp_terms','name','WHERE term_id=?',[$term_id]);
		$n[$term] = $term_id;
	}
	return $n ?? null;
}

function getCategoryBySlug($slug){ // inútil desde a origem, usar wpGetOrCreateTermId
	return query('SELECT t.term_id, t.name, t.slug, tt.description 
	FROM wp_terms t JOIN wp_term_taxonomy tt ON t.term_id = tt.term_id
	WHERE t.slug = "' . $slug . '" AND tt.taxonomy = "category";');
}

function getAssetById($id){
	$mid = query('SELECT id,guid FROM wp_posts WHERE ID = "' . $id . '";');
	$needle = '/uploads/';
	$data['id']  = $mid['id'] ?? null;
	$data['url'] = $mid['guid'] ?? null;
	$data['att'] = $data['url'] ? str_replace($needle,'',strstr($data['url'],$needle)) : false;
	return $data;
}

function getAssetByName($name){
	$tmp = explode('.',$name);
	$name = $tmp[0];
	$mid = query('SELECT id,guid FROM wp_posts WHERE post_type = "attachment" 
	AND post_title = "' . $name . '" AND post_mime_type LIKE "image/%";');
	$needle = '/uploads/';
	$data['id']  = $mid['id'] ?? null;
	$data['url'] = $mid['guid'] ?? null;
	$data['att'] = $data['url'] ? str_replace($needle,'',strstr($data['url'],$needle)) : false;
	return $data;
}

# 99/117/145