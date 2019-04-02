<?php 

/* Template Name: Candidate Search Results */ 

get_header(); 

if(!current_user_can('administrator')){
    echo "<h3 style='margin:100px 0px;text-align:center;'>You need to login as Administrator to view this page </h3>";
    get_footer();
    die();
}


include_once get_stylesheet_directory().'/old-archipro/class-archpiro-main.php';
include_once get_stylesheet_directory().'/old-archipro/arrays.php';
include_once get_stylesheet_directory().'/old-archipro/functions.php';
?>


<header class="page-header">
        <!--<h1 class="page-title"><?php the_title(); ?></h1>-->
</header>


<div id="primary" class="content-area">
    <div id="content" class="container">
<?php 
//echo "<pre>";
//print_r($_POST);
//echo "</pre>";


if(!isset($_POST) || $_POST['action'] != 'show'){
    $search = get_transient('arc_candidate_search_admin');
}else{
    $search = $_POST;
}
set_transient('arc_candidate_search_admin', $search);



$sql = 'select * from CANDIDATES ';
$count_sql = 'select count(*) cnt from CANDIDATES ';
$sql_ary = array();

//print_r($search['position']);

if(!empty($search['keyword'])){
    $sql_ary[] = 'Keywords like "%'.addslashes($search['keyword']).'%"';
}

if(!empty($search['first_name'])){
    $sql_ary[] = 'FirstName like "%'.addslashes($search['first_name']).'%"';
}

if(!empty($search['last_name'])){
    $sql_ary[] = 'LastName like "%'.addslashes($search['last_name']).'%"';
}

if(!empty($search['position'])){
    $sql_ary[] = 'FIND_IN_SET('.intval($search['position']).',Positions) > 0';
}

if(!empty($search['industry'])){
    $sql_ary[] = 'FIND_IN_SET('.intval($search['industry']).',Industries) > 0';
}

if(!empty($search['projecttype'])){
    $sql_ary[] = 'FIND_IN_SET('.intval($search['projecttype']).',ProjectTypes) > 0';
}

if(!empty($search['positiontype'])){
    $sql_ary[] = 'PositionType = '.  intval($search['positiontype']);
}

if(!empty($search['expyears'])){
    $sql_ary[] = 'ExpYears = '.  intval($search['expyears']);
}

if(!empty($search['degree'])){
    $sql_ary[] = 'Degree = '.  intval($search['degree']);
}

//Target Location Params

if(!empty($search['city'])){
    $sql_ary[] = 'City = "'. addslashes($search['city']).'"';
}

if(!empty($search['state'])){
    $sql_ary[] = 'State = "'. addslashes($search['state']).'"';
}

if(!empty($search['zip'])){
    $sql_ary[] = 'Zip = "'.  addslashes(($search['zip'])).'"';
}

if(!empty($search['country'])){
    $sql_ary[] = 'Country = "'.  addslashes($search['country']).'"';
}

//Freshness

if($search['freshness'] > 0 && $search['freshness'] <= 180){
    $time_interval = intval($search['freshness']) * 24 * 60 * 60;
    $backtime = time() - $time_interval;
    $sql_ary[] = 'AddedDate > '.$backtime;
}

//Compensation Params
if(!empty($search['comp'])){
    $sql_ary[] = 'SalaryRate = '.  intval($search['comp']);
}
if(count($sql_ary) > 0){
    $sql.= 'where ';
    $count_sql.= 'where ';
}

$sql .= implode(' and ', $sql_ary);
$count_sql .= implode(' and ', $sql_ary);



$start = 0;
$limit = 50;
$page_no = 1;
$page = get_query_var( 'page' );

if(!empty($page) && $page > 1){
    $page_no = intval($page);
    $start = ($limit*$page_no) - $limit;
}

$arc_obj = new archipro_main();
$search['page_no'] = $page_no;
$wp_candidates = $arc_obj->wp_get_candidates($search);
$wp_c_count = $arc_obj->wp_get_candidates_count($search);
$query_start = $start - $wp_c_count;
if($query_start < 0){
    $query_start = 0;
}
$query_limit = $limit - $wp_candidates->post_count;

$sort = get_query_var('sort')!=''?get_query_var('sort'):$_GET['sort'];
if($sort == 'city'){
    $sortby = 'City';
}else{
    $sortby = 'AddedDate';
}

$sort_type = get_query_var('sort_type')!=''?get_query_var('sort_type'):$_GET['sort_type'];



if(empty($sort_type)){
    $sort_type = 'desc';
}

$sql .= ' order by '.$sortby.' '.$sort_type;
$sql .= ' limit '.$query_start.','.$query_limit;
$mydb = new wpdb('citysca1_db1','PLE+C.!N~)Um','citysca1_db1','146.66.67.202');
//$mydb = new wpdb('citysca1_naur','x%=TT4P;PS]y','citysca1_db1','146.66.67.202');
//$mydb = new wpdb('root','','archipro_v1','localhost');
$candidates = $mydb->get_results($sql);

//echo "testing";
//echo "<pre>";print_r($candidates);die;

$record_counts = $mydb->get_row($count_sql);
//echo  "<pre>";
//print_r($wp_candidates);
//echo "</pre>";
//die();
?>



<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td align="left" valign="middle">
<h2>Candidate Search Results</h2>
</td>
<td align="right" valign="middle">
<div style="float: right;">[ <a href="/candidatesearch/?edit_search=1">Edit Search</a> ]&nbsp;&nbsp;&nbsp;[ <a href="/candidatesearch">New Search</a> ]</div></td>
</tr>
</tbody>
</table>

&nbsp;

<table border="0" width="100%" cellspacing="0" cellpadding="2">

<tbody>

<tr>

    <td align="left" valign="bottom" nowrap="nowrap"><span class="t0">Records: <b><?php echo $start + 1; ?></b> - <b><?php echo $start+$wp_candidates->post_count+  count($candidates); ?></b>   of <b><?php echo $record_counts->cnt+$wp_c_count; ?></b> </span></td>

<td align="right" valign="bottom">

    <span class="t0"> Pages:  

        <?php 
        $counts = intval($record_counts->cnt);
        $counts += $wp_c_count;

        $i = 1;

        while ($counts > 0) {

            if($page_no == $i){

                echo '<b>'.$i.'</b>';

            }else{

            ?>

        <a href="<?php echo add_query_arg( array('page'=>$i,'sort'=>$sort,'sort_type'=>$sort_type), get_permalink(get_the_ID() ) ); ?>"><?php echo $i; ?></a>

        <?php

            }

        $counts -= 50;

        $i++;

        } 

        ?>

        <!--<b>1</b>  <a href="<?php echo add_query_arg( 'page', '2', 'http://localhost/archipro/candidate-search-results/' ); ?>">2</a>  <a href="/jboard/trial_candidatesearch/pg/3/action/show">3</a>  <a href="/jboard/trial_candidatesearch/pg/4/action/show">4</a>  <a href="/jboard/trial_candidatesearch/pg/5/action/show">5</a>  <a href="/jboard/trial_candidatesearch/pg/6/action/show">6</a>  <a href="/jboard/trial_candidatesearch/pg/7/action/show">7</a>  <a href="/jboard/trial_candidatesearch/pg/8/action/show">8</a>  <a href="/jboard/trial_candidatesearch/pg/9/action/show">9</a>  <a href="/jboard/trial_candidatesearch/pg/10/action/show">10</a>   <a href="/jboard/trial_candidatesearch/pg/11/action/show">&gt;&gt;</a></span>-->

</td>

</tr>

</tbody>

</table>

<table class="color_border" border="0" width="100%" cellspacing="1" cellpadding="2">

<tbody>

<tr class="color_dark">

    <td style="text-align:center;width:90px;"><span class="t1"><b><a href="<?php echo add_query_arg(array('page'=>$page_no,'sort'=>'date','sort_type'=>'asc'),  get_permalink(get_the_ID())); ?>">▲</a> Date <a href="<?php echo add_query_arg(array('page'=>$page_no,'sort'=>'date','sort_type'=>'desc'),  get_permalink(get_the_ID())); ?>">▼</a> </b></span></td>
	
<td style="text-align:center;width:100px;" valign="middle" nowrap="nowrap"><span class="t1"><b>Data</b></span></td>
	
<td align="left" valign="middle" nowrap="nowrap"><span class="t1"><b>Name</b></span></td>

<td align="left" valign="middle" nowrap="nowrap"><span class="t1"><b>Position</b>  (click to view profile)</span></td>

<td align="left" valign="middle" nowrap="nowrap"><span class="t1"><b>Experience</b></span></td>

<td style="padding-left:5px;"><span class="t1"><b><a href="<?php echo add_query_arg(array('page'=>$page_no,'sort'=>'city','sort_type'=>'asc'),  get_permalink(get_the_ID())); ?>">▲</a>City, State<a href="<?php echo add_query_arg(array('page'=>$page_no,'sort'=>'city','sort_type'=>'desc'),  get_permalink(get_the_ID())); ?>">▼</a></b></span></td>

</tr>


<?php
if ( $wp_candidates->have_posts() ) {
//echo "heello";
    while ( $wp_candidates->have_posts() ) {
        $wp_candidates->the_post();
        $added_date = get_the_date('m/d/Y');
        $positions = arc_get_all_postions_string(get_post_meta(get_the_ID(),'_arc_position',true), $arr_position);
 //$namec=get_post_meta(get_the_ID(),'_candidate_name',true);
?>

<tr class="color_light">

<td style="text-align:center;"><span class="t1"><?= $added_date; ?></span></td>

<!--Add has "Data" flag here, use symbol &#9998; to indicate only if candidate has some data about them.-->
<td style="text-align:center;" valign="top"><span class="t1"><?= '&#9998;'; ?></span></td>

<td align="left" valign="top" nowrap="nowrap"><span class="t1"><?php echo get_post_meta(get_the_ID(),'_candidate_name',true); ?></span></td>

<td align="left" valign="top"><b><span class="t1"><a href="<?php echo get_the_permalink(); ?>"><?php echo !empty($positions)?$positions:get_the_ID(),'_candidate_title',true; ?> <?php // echo get_post_meta(get_the_ID(),'_candidate_title',true); ?></a></span></b></td>

<td align="left" valign="top"><span class="t1"><?php echo '-' ?></span></td>

<td style="text-align:left;" nowrap="nowrap"><span class="t1"><?php echo get_post_meta(get_the_ID(),'_candidate_location',true); ?></span></td>

</tr>
<?php
   }
    wp_reset_postdata();
} 
?>


<?php 
//if(count($candidates) >= 50 && $wp_candidates->post_count > 0){
//    $candidates = array_slice($candidates, 0 , count($candidates) - $wp_candidates->post_count);
//}
// Candidates from OLD DB

foreach($candidates as $candidate){ 

    $added_date = $candidate->AddedDate ? date("m/d/Y",$candidate->AddedDate):'';

    $positions = arc_get_all_postions_string($candidate->Positions, $arr_position);
    
    $is_data_available = false;
    $user_notes = $arc_obj->get_user_notes($candidate->UserID, 'C');
    if(count($user_notes) > 0){
        $is_data_available = TRUE;
    }else{
        $user_portfolios = $arc_obj->get_user_portfolios($candidate->UserID);
        if(count($user_portfolios) > 0){
            $is_data_available = TRUE;
        }else{
            $user_refs = $arc_obj->get_user_references($candidate->UserID);
            $user_tests = $arc_obj->get_user_tests($candidate->UserID);
            if(count($user_refs) > 0 || count($user_tests) > 0){
                $is_data_available = TRUE;
            }
        }
    }
//echo '<pre>';print_r($candidates);die;
    ?>

<tr class="color_light">

<td style="text-align:center;"><span class="t1"><?= $added_date; ?></span></td> 

<!--Add has "Data" flag here, use symbol &#9998; to indicate only if candidate has some data about them.-->
<td style="text-align:center;" valign="top"><span class="t1"><?= $is_data_available?'&#9998;':'-'; ?></span></td>

<td align="left" valign="top" nowrap="nowrap"><span class="t1"><?php echo $candidate->FirstName; ?>&nbsp;<?php echo $candidate->LastName; ?></span></td>

<td align="left" valign="top"><b><span class="t1"><a href="<?php echo add_query_arg( 'id', $candidate->ID, get_site_url().'/candidateprofile' ); ?>"><?= $positions; ?></a></span></b></td>

<td align="left" valign="top"><span class="t1"><?php echo $arr_expyears[$candidate->ExpYears]; ?></span></td>

<td style="text-align:left;" nowrap="nowrap"><span class="t1"><?php echo $candidate->City; ?>, <?php echo $candidate->State; ?></span></td>

</tr>

<?php } ?> 

</tbody>

</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">

<tbody>

<tr>

<td align="left" valign="bottom" nowrap="nowrap"><span class="t0">Records: <b><?php echo $start + 1; ?></b> - <b><?php echo $start+$wp_candidates->post_count+  count($candidates); ?></b>   of <b><?php echo $record_counts->cnt+$wp_c_count; ?></b> </span></td>

<td align="right" valign="bottom">

    <span class="t0"> Pages:  

        <?php 

        $counts = intval($record_counts->cnt);
        $counts += $wp_c_count;
        $i = 1;

        while ($counts > 0) {

            if($page_no == $i){

                echo '<b>'.$i.'</b>';

            }else{

            ?>

        <a href="<?php echo add_query_arg( array('page'=>$i,'sort'=>$sort,'sort_type'=>$sort_type), get_permalink(get_the_ID() ) ); ?>"><?php echo $i; ?></a>

        <?php

            }

        $counts -= 50;

        $i++;

        } 

        ?>

</td>

</tr>

</tbody>

</table>

<!--<table border="0" width="100%" cellspacing="1" cellpadding="2">

<tbody>

<tr>

<td align="left" valign="bottom"><span class="t0"> Pages:  <b>1</b>  <a href="/jboard/trial_candidatesearch/pg/2/action/show">2</a>  <a href="/jboard/trial_candidatesearch/pg/3/action/show">3</a>  <a href="/jboard/trial_candidatesearch/pg/4/action/show">4</a>  <a href="/jboard/trial_candidatesearch/pg/5/action/show">5</a>  <a href="/jboard/trial_candidatesearch/pg/6/action/show">6</a>  <a href="/jboard/trial_candidatesearch/pg/7/action/show">7</a>  <a href="/jboard/trial_candidatesearch/pg/8/action/show">8</a>  <a href="/jboard/trial_candidatesearch/pg/9/action/show">9</a>  <a href="/jboard/trial_candidatesearch/pg/10/action/show">10</a>   <a href="/jboard/trial_candidatesearch/pg/11/action/show">&gt;&gt;</a></span></td>

</tr>

</tbody>

</table>-->

        



        

    </div>

</div>

<?php get_footer(); ?>