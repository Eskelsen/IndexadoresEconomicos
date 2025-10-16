<?php

# Microframework Services

	# [tmp] 2025-07-21 Monday: work on it

function svcStatus($svc){
	if (!($data = svcActive($svc))) {
		logfy('[Services] Serviço desativado: ' . svcName($svc));
		return false;
	}
	update('mf_services',['last_access' => time()],'id=?',[$data['id']]);
	if (!$data['status']) {
		return false;
	}
	return time()>=$data['next_exec'];
}

function svcActive($svc){
	$data = selectRow('mf_services','*','WHERE service=?',[$svc]);
	if (empty($data['status'])) {
		return false;
	}
	return $data;
}

function svcRegister($svc,$label){
	if (selectRow('mf_services','id','WHERE service=?',[$svc])) {
		return false;
	}
	$values = [
		'service'     => $svc,
		'label'       => $label,
		'last_access' => time(),
		'next_exec'   => time(),
		'status'      => 1
	];
	return insert('mf_services',$values);
}

function svcUpdate($svc){
	$values = [
		'fail_count'  => 0,
		'last_access' => time(),
		'last_exec'   => time(),
		'next_exec'   => time(),
		'log_status'  => null
	];
	return update('mf_services',$values,'service=?',[$svc]);
}

function svcFail($svc, $msg = 'Indisponível'){
	$data = selectRow('mf_services','*','WHERE service=?',[$svc]);
	if (empty($data)) {
		return false;
	}
	$fail_count = $data['fail_count'] + 1;
	$status = ($fail_count<=6) ? 1 : 0;
	$values = [
		'fail_count'  => $fail_count,
		'last_access' => time(),
		'last_exec'   => time(),
		'next_exec'   => time() + (($fail_count) * (5 * 60)),
		'log_status'  => $msg,
		'status'  	  => $status
	];
	if (!$status) {
		logfy('<span class="text-warning">' . $status_label . ' desativado (automaticamente).</span>');
	}
	update('mf_services',$values,'service=?',[$svc]);
	return $status;
}

function svcEnable($svc, $status = 1){
	return svcSwitch($svc, $status);
}

function svcDisable($svc, $status = 0){
	return svcSwitch($svc, $status);
}

function svcSwitch($svc, $status){
	$values = [
		'fail_count' => 0,
		'next_exec'  => time(),
		'status' 	 => $status
	];
	return update('mf_services',$values,'service=?',[$svc]);
}

function svcName($svc){
	return field('mf_services','label','WHERE service=?',[$svc]);
}

function svcLabels(){
	return label('mf_services','service','label');
}

function svcAll(){
	return selectAll('mf_services');
}
