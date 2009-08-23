<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * permission helper functions
 *
 * @package	Vunsy
 * @subpackage	Vunsy
 * @category	helper file
 * @author	Emad Elsaid
 * @link	http://github.com/blazeeboy/vunsy
 */
if ( ! function_exists('perm_array')){
	function perm_array()
	{
		$CI =& get_instance();
		return array(
			'opers' => array(
						'='=>'==',
						'>=='=>'>=',
						'<=='=>'<=',
						'!=='=>'!=',
						'not'=>'!'
			),
			'boolVars' => array(
								'root'=> intval($CI->vunsy->user->is_root()).'=1',
								'logged'=>intval($CI->vunsy->user->logged()).'=1',
								'guest'=>intval($CI->vunsy->user->is_guest()).'=1'
			),
			'vars' => array(
								'level'=>$CI->session->userdata('level'),
								'user'=>$CI->session->userdata('id'),
								'section'=>$CI->vunsy->section->id,
								'mode'=>"'".$CI->session->userdata('mode')."'",
			)
		);
	}
}
if ( ! function_exists('perm_chck')){
	
	function perm_chck($perms=''){
		
		$CI =& get_instance();
		// if the user is root then the result would be TRUE
		if($CI->session->userdata('level')==-1) return TRUE;
		
		$perm_vars = perm_array();
		
		$perms = str_replace( array_keys($perm_vars['boolVars']), array_values($perm_vars['boolVars']), $perms );
		$vars_reg = implode('|', array_keys($perm_vars['vars']));
		$matches = array();
		
		preg_match( "/(?:(?(3)\s*\b(and|or|not)\b\s+)(?:(not)\s+)?({$vars_reg}|0|1)\s*([<>]=?|!?=)\s*(\d+(?:\.\d*)?|\.\d+|'[^']*'))+/", $perms, $matches );
	
		$matches = @$matches[0];
		//no matches found (BAD OR EMPTY $perm)
		if( empty($matches) ) return FALSE;
		
		$matches = str_replace( array_keys($perm_vars['opers']), array_values($perm_vars['opers']), $matches );
		$matches = str_replace( array_keys($perm_vars['vars']), array_values($perm_vars['vars']), $matches );
		
		$result = FALSE;
		eval('$result= ('.$matches.');');
		return $result;
	}
}
