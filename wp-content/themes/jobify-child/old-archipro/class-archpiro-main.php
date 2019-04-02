<?php

class archipro_main {
    
    private $db_creds_local = array(
        'user' => 'citysca1_admin',
        'pass' => 'Architecto1',
        'name' => 'citysca1_jobboard_v1',
        'host' => 'localhost'
    );
    
    private $db_creds = array(
        'user' => 'citysca1_db1',
        'pass' => 'PLE+C.!N~)Um',
        'name' => 'citysca1_db1',
        'host' => 'localhost'
    );
    
    private $conn;

    function __construct() {
        $this->connect_to_db();
    }
    
    function connect_to_db(){
        $whitelist = array(
            '127.0.0.1',
            '::1'
        );

        if(!in_array($_SERVER['REMOTE_ADDR'], $whitelist)){
            $db = $this->db_creds;
        }else{
            $db = $this->db_creds_local;
        }
        $this->conn = new wpdb($db['user'], $db['pass'], $db['name'], $db['host']);
    }
    
    function get_user_by_id($id){
        $sql = "select * from USERS where id=".  intval($id);
        $data = $this->conn->get_row($sql);
        return $data;
    }
    
    function get_candidate_by_id($id){
        $sql = "select * from CANDIDATES where UserID=".  intval($id);
        $data = $this->conn->get_row($sql);
        return $data;
    }
    
    function wp_prepare_candidate_search_args($search){
//$name1=explode(" ",'_candidate_name');
//$name1="$search['first_name'] $search['last_name']'";
       $first_name = $last_name = '';
//$keyword = '';
if($search['keyword'] != ''){
            $keyword = array(
			'key' => '_candidate_name',
			'value' => $search['keyword'],
                        'compare' => 'LIKE'
                    );
        }

        if($search['last_name'] != ''){
            $last_name = array(
			'key' => '_candidate_name',
			'value' => $search['last_name'],
                        'compare' => 'LIKE'
                    );
        }
        if($search['first_name'] != ''){
            $first_name = array(
                'key' => '_candidate_name',
                'value' => $search['first_name'],
                'compare' => 'LIKE'
            );
        }
	if($search['city'] != ''){
            $city = array(
                'key' => 'geolocation_city',
                'value' => $search['city'],
                'compare' => 'LIKE'
            );
        }
		if($search['state'] != ''){
            $state = array(
                'key' => 'geolocation_state_long',
                'value' => $search['state'],
                'compare' => 'LIKE'
            );
        }
		
		if($search['position'] == 0){
            $position = array(
                'key' => '_arc_position',
                'value' => '',
                'compare' => 'LIKE'
            );
        }
        else if($search['position'] != ''){
            $position = array(
                'key' => '_arc_position',
                'value' => $search['position'],
                'compare' => 'LIKE'
            );
        }
		
        if($search['industry'] != ''){
            $industry = array(
                'key' => '_arc_industry',
                'value' => $search['industry'],
                'compare' => 'LIKE'
            );
        }
$args = array(
            'post_type' => 'resume',
            'posts_per_page' => 50,
            'paged' => $search['page_no'],
            'meta_query' => array(
                'relation' => 'AND',
     array(
            'relation' => 'OR',  
             $first_name,
	    $last_name
		),
        array(
           'relation' => 'OR',
           $position,
       $industry
         ), 
      array(
           'relation' => 'OR',
	         $city,
		$state
             ),	
              $keyword
                ),
            'cache_results' => FALSE
        );
       //echo "<pre>";
       //print_r($args);
       // echo "</pre>";
        return $args;
    }
    
    function wp_get_candidates($search){
        $args = $this->wp_prepare_candidate_search_args($search);
        $posts_array = new WP_Query( $args );
		//echo "<pre>";
		//print_r($args);
		//echo "</pre>";
        return $posts_array;
    }
    
    function wp_get_candidates_count($search){
        $search['page_no'] = 1;
        $args = $this->wp_prepare_candidate_search_args($search);
        $posts_array = new WP_Query( $args );
		//echo "<pre>";
		//print_r($args);
		//echo "</pre>";
        return $posts_array->found_posts;
    }
    
    function get_user_notes($user_id,$user_type){
        $user_type = strtolower($user_type);
        if($user_type == 'c' || $user_type == 'candidate'){
            $table = 'NOTESC';
        }else{
            $table = 'NOTESE';
        }
        $sql = "select * from ".$table." where UserID=".  intval($user_id).' order by AddedDate desc';
        $data = $this->conn->get_results($sql);
        return $data;
    }
    
    function update_user_notes($note,$note_id,$user_id,$user_type){
        if($user_type == 'c' || $user_type == 'candidate'){
            $table = 'NOTESC';
        }else{
            $table = 'NOTESE';
        }
        if($note_id > 0){
            $up_data = array(
                'Description' => $note,
                'LastUpdTime' => time()
            );
            $where = array(
                'ID' => $note_id,
                'UserID' => $user_id
            );
            $this->conn->update( $table, $up_data, $where);
        }else{
            $ins_data = array(
                'Description' => $note,
                'LastUpdTime' => time(),
                'AddedDate' => time(),
                'AddedByIP' => $_SERVER['REMOTE_ADDR'],
                'UserID' => $user_id
           );
            $this->conn->insert( $table, $ins_data);
            return $this->conn->insert_id;
        }
        return TRUE;
    }
    
    function delete_user_note($note_id,$user_id,$user_type){
        if($user_type == 'c' || $user_type == 'candidate'){
            $table = 'NOTESC';
        }else{
            $table = 'NOTESE';
        }
        $where = array(
            'ID' => $note_id,
            'UserID' => $user_id
        );
        $this->conn->delete( $table, $where);
        return TRUE;
    }
    
    function get_user_portfolios($user_id){
        $sql = "select * from PORTFOLIO where UserID=".  intval($user_id);
        $data = $this->conn->get_results($sql);
        return $data;
    }
    
    function get_user_references($user_id){
        $sql = "select * from REFS where UserID=".  intval($user_id);
        $data = $this->conn->get_results($sql);
        return $data;
    }
    
    function get_user_tests($user_id){
        $sql = "select * from TESTS where UserID=".  intval($user_id);
        $data = $this->conn->get_results($sql);
        return $data;
    }
    
    function get_test_data($test){
        include_once get_stylesheet_directory().'/old-archipro/arrays_interviews.php';
        $arr=explode("|",$test->Description);
        $arr_fval=array();

        if($arr) foreach($arr as $key=>$val){
          $arr1=explode('{*}',$val);
          if($arr1[0]>0) $arr_fval[$arr1[0]]=$arr1[1];
        }
        $fld=$arr_interfields[$testno];
        if($fld) foreach($fld as $fieldindex=>$fielddefs){



    $arr=explode("|",$fielddefs);

    $required=(int)$arr[0];

    $fieldtype=(int)$arr[1];

    $params=$arr[2];

    $fieldname=$arr[3];



    switch($fieldtype){

      case 1:  // text area



        $arr_opt=explode("~",$params);

        $f_width=$arr_opt[0];

        $f_rows=$arr_opt[1];



        $s.='<tr><td colspan=2 align="center"><table width="'.$f_width.'" border=0 cellspacing=0 cellpadding=2>';

        $s.='<tr><td align="left" valign="middle" class=c1><b>'.$fieldname.'</b>&nbsp;</td></tr>';

        $s.='<tr><td align="left" valign="middle" class=t1><div style="width: '.$f_width.'px;">'.htmlspecialchars(${'field'.$fieldindex}).'</div></td></tr>';

        $s.='</table></td></tr>';



        $s.='<tr><td colspan=2><img src="images/space.gif" width=1 height=10></td></tr>';



      break;



      case 2:  // drop down



        $arr_opt=explode("~",$params);

        $st1='';

        if($arr_opt) foreach($arr_opt as $key=>$val){

          if((${'field'.$fieldindex})==$key+1) $st1=htmlspecialchars($val);

        }



        $s.='<tr><td colspan=2 align="center"><table width="100%" border=0 cellspacing=0 cellpadding=2><tr>';

        $s.='<td align="right" valign="middle" class=c1><b>'.$fieldname.'</b>&nbsp;</td>';

        $s.='<td align="left" valign="middle" class=t1>'.$st1.'</td>';

        $s.='</tr></table></td></tr>';



        $s.='<tr><td colspan=2><img src="images/space.gif" width=1 height=10></td></tr>';



      break;



      case 3:  // text box



        $arr_opt=explode("~",$params);

        $f_width=$arr_opt[0];

        $f_max=$arr_opt[1];



        $s.='<tr><td colspan=2 align="center"><table width="100%" border=0 cellspacing=0 cellpadding=2><tr>';

        $s.='<td align="right" valign="top" class=c1><b>'.$fieldname.'</b>&nbsp;</td>';

        $s.='<td align="left" valign="top" class=t1><div style="width:'.$f_width.'px;">'.htmlspecialchars(${'field'.$fieldindex}).'</div></td>';

        $s.='</tr></table></td></tr>';



        $s.='<tr><td colspan=2><img src="images/space.gif" width=1 height=10></td></tr>';



      break;



      case 4:  // drop down groups



        $arr_params=explode("^",$params);



        $arr_val="";

        $arr_opt=explode("~",$arr_params[0]);



        $arr_opt2=explode("~",$arr_params[1]);



        $s.='<tr><td colspan="2" align="left" valign="middle" class=c1><div style="padding: 5px;"><span class=c1><b>'.$fieldname.'</b>&nbsp;</span></div></td></tr>';

        $s.='<tr><td colspan=2 align="center"><table width="90%" border=0 cellspacing=0 cellpadding=2>';



        if($arr_opt2) foreach($arr_opt2 as $key2=>$val2){

          $st1='';

          if($arr_opt) foreach($arr_opt as $key=>$val){

            if((${'field'.$fieldindex.'_'.($key2+1)})==$key+1) $st1=htmlspecialchars($val);

          }

          $s.='<tr><td align="left" valign="top" class=t1>'.$val2.'&nbsp;</td>';

          $s.='<td align="right" valign="top" class=t1 nowrap>&nbsp;<b>'.$st1.'</b></td></tr>';

          $s.='<tr><td colspan=2><img src="images/space.gif" width=1 height=5></td></tr>';

        }

        $s.='</table></td></tr>';



        $s.='<tr><td colspan=2><img src="images/space.gif" width=1 height=10></td></tr>';



      break;



      case 5:  // radio groups



        $arr_opt=explode("~",$params);



        $s.='<tr><td colspan="2" align="left" valign="middle" class=c1><div style="padding: 5px;"><span class=c1><b>'.$fieldname.'</b>&nbsp;</span></div></td></tr>';

        $s.='<tr><td colspan=2 align="center"><table width="90%" border=0 cellspacing=0 cellpadding=2>';



        if($arr_opt) foreach($arr_opt as $key2=>$val2){

          $s.='<tr><td align="left" valign="top" class=t1>'.$val2.'&nbsp;</td>';

          $s.='<td align="right" valign="top" nowrap>&nbsp;'.(((${'field'.$fieldindex})==($key2+1))?'[<b>x</b>]':'[&nbsp;&nbsp;]').'</td></tr>';

          $s.='<tr><td colspan=2><img src="images/space.gif" width=1 height=5></td></tr>';

        }

        $s.='</table></td></tr>';



        $s.='<tr><td colspan=2><img src="images/space.gif" width=1 height=10></td></tr>';



      break;



      case 6:  // label



        $s.='<tr><td colspan=2><img src="images/space.gif" width=1 height=6></td></tr>';

        $s.='<tr>';

        $s.='<td colspan="2" align="left" valign="top" class="td_borderbottom"><span class=t2>&nbsp;<b>'.$fieldname.'</b>&nbsp;</span></td>';

        $s.='</tr>';

        $s.='<tr><td colspan=2><img src="images/space.gif" width=1 height=10></td></tr>';



      break;

    }





  }
      
      
    }

}
?>
