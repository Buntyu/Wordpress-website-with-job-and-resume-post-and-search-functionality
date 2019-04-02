<?php



/* Template Name: Employer Search Results */



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
    $search = get_transient('arc_employer_search_admin');
}else{
    $search = $_POST;
}
set_transient('arc_employer_search_admin', $search);

$sql = 'select * from EMPLOYERS ';

$count_sql = 'select count(*) cnt from EMPLOYERS ';

$sql_ary = array();

if(!empty($search['keyword'])){
//    $sql_ary[] = 'Keywords like "%'.addslashes($search['keyword']).'%"';
    $sql_ary[] = 'Concat(CompanyName, " ", PrincipalName, " ", Industry, " ", ContactName, " ", ContactPosition, " ", ContactDepartment, " ", City, " ", State, " ", Zip, " ", Country) like "%'.addslashes($search['keyword']).'%"';
}

if(!empty($search['company_name'])){
    $sql_ary[] = 'CompanyName like "%'.addslashes($search['company_name']).'%"';
}

if(!empty($search['principal_name'])){
    $sql_ary[] = 'PrincipalName like "%'.addslashes($search['principal_name']).'%"';
}

if(!empty($search['industry'])){
    $sql_ary[] = 'Industry like "%'.addslashes($search['industry']).'%"';
}

if(!empty($search['contact_name'])){
    $sql_ary[] = 'ContactName like "%'.addslashes($search['contact_name']).'%"';
}

if(!empty($search['contact_position'])){
    $sql_ary[] = 'ContactPosition like "%'.addslashes($search['contact_position']).'%"';
}

if(!empty($search['department'])){
    $sql_ary[] = 'ContactDepartment like "%'.addslashes($search['department']).'%"';
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



$sql .= ' limit '.$start.','.$limit;



$mydb = new wpdb('citysca1_db1','PLE+C.!N~)Um','citysca1_db1','146.66.67.202');

//$mydb = new wpdb('root','','archipro_core','localhost');

$employers = $mydb->get_results($sql);



$record_counts = $mydb->get_row($count_sql);
$arc_obj = new archipro_main();
//print_r($mydb);



//$tables_sql = 'select * from information_schema.tables';

//$tables = $mydb->get_results($tables_sql);

//print_r($tables);

?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">

<tbody>

<tr>

<td align="left" valign="middle">

<h2>Employer Search Results</h2>

</td>

<td align="right" valign="middle">

<div style="float: right;">[ <a href="<?php echo get_site_url(); ?>/employer-search/?edit_search=1">Edit Search</a> ]&nbsp;&nbsp;&nbsp;[ <a href="<?php echo get_site_url(); ?>/employer-search">New Search</a> ]</div></td>

</tr>

</tbody>

</table>

        

<table border="0" width="100%" cellspacing="0" cellpadding="2">

<tbody>

<tr>

<td align="left" valign="bottom" nowrap="nowrap"><span class="t0">Records: <b><?php echo $start + 1; ?></b> - <b><?php echo $start+$limit<=$record_counts->cnt?$start+$limit:$record_counts->cnt; ?></b>   of <b><?php echo $record_counts->cnt; ?></b> </span></td>

<td align="right" valign="bottom">

    <span class="t0"> Pages:  

        <?php 

        $counts = intval($record_counts->cnt);

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



<!--Actual listing table-->

<table class="color_border" border="0" width="100%" cellspacing="1" cellpadding="2">

<tbody>

<tr class="color_dark">

    <td style="text-align:center;width:90px;"><span class="t1"><b><a href="<?php echo add_query_arg(array('page'=>$page_no,'sort'=>'date','sort_type'=>'asc'),  get_permalink(get_the_ID())); ?>">▲</a> Date <a href="<?php echo add_query_arg(array('page'=>$page_no,'sort'=>'date','sort_type'=>'desc'),  get_permalink(get_the_ID())); ?>">▼</a> </b></span></td>
    
    <td style="text-align:center;width:100px;" valign="middle" nowrap="nowrap"><span class="t1"><b>Data</b></span></td>

    <td align="left" valign="middle" nowrap="nowrap"><span class="t1"><b>Company Name</b>&nbsp;(click to view profile)</span></td>

    <td align="left" valign="middle" nowrap="nowrap"><span class="t1"><b>Contact</b></td>

    <td align="left" valign="middle" nowrap="nowrap"><span class="t1"><b>Industry</b></span></td>

<td style="padding-left:5px;"><span class="t1"><b><a href="<?php echo add_query_arg(array('page'=>$page_no,'sort'=>'city','sort_type'=>'asc'),  get_permalink(get_the_ID())); ?>">▲</a>City, State<a href="<?php echo add_query_arg(array('page'=>$page_no,'sort'=>'city','sort_type'=>'desc'),  get_permalink(get_the_ID())); ?>">▼</a></b></span></td>

</tr>


<?php foreach($employers as $employer){ 

    $added_date = $employer->AddedDate ? date("m/d/Y",$employer->AddedDate):'';
    $is_data_available = false;
    $user_notes = $arc_obj->get_user_notes($employer->UserID, 'E');
    if(count($user_notes) > 0){
        $is_data_available = TRUE;
    }
    ?>

<tr class="color_light">

<td style="text-align:center;"><span class="t1"><?= $added_date; ?></span></td> 

<!--Add has "Data" flag here, use symbol &#9998; to indicate only if employer has some data about them.-->
<td style="text-align:center;" valign="top"><span class="t1"><?= $is_data_available?'&#9998;':'-'; ?></span></td>

<td align="left" valign="top" nowrap="nowrap"><span class="t1"><a href="<?php echo add_query_arg( 'id', $employer->ID, get_site_url().'/employer-profile' ); ?>"><?php echo $employer->CompanyName; ?></a></span></td>

<td align="left" valign="top"><span class="t1"><?= $employer->ContactName; ?></span></td>

<td align="left" valign="top"><span class="t1"><?= $employer->Industry; ?></span></td>

<td style="text-align:left;" nowrap="nowrap"><span class="t1"><?php echo $employer->City; ?>, <?php echo $employer->State; ?></span></td>

</tr>

<?php } ?> 

</tbody>

</table>

<table border="0" width="100%" cellspacing="0" cellpadding="2">

<tbody>

<tr>

<td align="left" valign="bottom" nowrap="nowrap"><span class="t0">Records: <b><?php echo $start + 1; ?></b> - <b><?php echo $start+$limit<=$record_counts->cnt?$start+$limit:$record_counts->cnt; ?></b>   of <b><?php echo $record_counts->cnt; ?></b> </span></td>

<td align="right" valign="bottom">

    <span class="t0"> Pages:  

        <?php 

        $counts = intval($record_counts->cnt);

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

    </div>

</div>

<?php get_footer(); ?>