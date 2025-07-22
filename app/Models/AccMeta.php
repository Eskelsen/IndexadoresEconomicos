<?php

# Account :: mf_accmeta: id,acc_id,subsystem,meta_key,value

	# [tmp] 2025-07-21 Monday: work on it

function accmetaSet($acc_id,$subsystem,$key,$value){
	$mid = selectRow('mf_accmeta', 'id,value', "WHERE acc_id='$acc_id' AND subsystem='$subsystem' AND meta_key='$key';");
	if (empty($mid['id'])) {
		$values = [
			'acc_id'	=> $acc_id,
			'subsystem'	=> $subsystem,
			'meta_key' 	=> $key,
			'value' 	=> $value
		];
		return insert('mf_accmeta', $values);
	}
	if (sha1($value)==sha1($mid['value'])) {
		return true;
	}
	return update('mf_accmeta', ['value' => $value], "acc_id='$acc_id' AND subsystem='$subsystem' AND meta_key='$key';");
}

function accmetaGet($acc_id,$subsystem,$key){
	$mid = selectRow('mf_accmeta', 'id,value', "WHERE acc_id='$acc_id' AND subsystem='$subsystem' AND meta_key='$key';");
	return empty($mid['id']) ? false : $mid['value'];
}

function accmetaGetByKey($value){
	return selectRow('mf_accmeta', '*', "WHERE value='$value';");
}

function accmetaLabel($acc_id = false){
	$cond = ($acc_id) ? "WHERE acc_id='$acc_id';" : '';
    if (!$cols = selectAll('mf_accmeta','meta_key,value',$cond)) {
        return null;
    }
    foreach ($cols as $row) {
        $n[$row['meta_key']] = $row['value'];
    }
    return $n ?? null;
}
