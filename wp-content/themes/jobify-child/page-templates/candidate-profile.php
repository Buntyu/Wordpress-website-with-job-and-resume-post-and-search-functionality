<?php 
/* Template Name: Candidate Profile */ 

get_header(); 

wp_enqueue_script('arc-colorbox','https://cdnjs.cloudflare.com/ajax/libs/jquery.colorbox/1.6.3/jquery.colorbox-min.js');
wp_enqueue_style('arc-colorbox-style',  get_stylesheet_directory_uri().'/css/colorbox.css');

if(!current_user_can('administrator')){
    echo "<h3 style='margin:100px 0px;text-align:center;'>You need to login as Administrator to view this page </h3>";
    get_footer();
    die();
}

include_once get_stylesheet_directory().'/old-archipro/class-archpiro-main.php';
include_once get_stylesheet_directory().'/old-archipro/arrays.php';
include_once get_stylesheet_directory().'/old-archipro/functions.php';
include_once get_stylesheet_directory().'/old-archipro/states.php';
include_once get_stylesheet_directory().'/old-archipro/arrays_interviews.php';

$arc_obj = new archipro_main();
$user_type = 'c';
$candidate = $arc_obj->get_candidate_by_id($_GET['id']);
/*if(!$candidate){
    echo "<h3 style='margin:100px 0px;text-align:center;'>Invalid Candidate </h3>";
    get_footer();
    die();
}*/
$c_user = $arc_obj->get_user_by_id($candidate->UserID);
$user_notes = $arc_obj->get_user_notes($candidate->UserID, $c_user->Type);
$user_portfolios = $arc_obj->get_user_portfolios($candidate->UserID);
$user_refs = $arc_obj->get_user_references($candidate->UserID);
$user_tests = $arc_obj->get_user_tests($candidate->UserID);

$user_id = $candidate->UserID;
//print_r($user_notes);

//print_r($candidate);

$mydb = new wpdb('citysca1_db1','PLE+C.!N~)Um','citysca1_db1','146.66.67.202');
//$mydb = new wpdb('root','','archipro_core','localhost');
$candidate = $mydb->get_results("select * from CANDIDATES where id=".  intval($_GET['id']));

if($candidate){
    $candidate = $candidate[0];
}

$work_countries = arc_get_work_permit_countries($candidate->Countries, $arr_countries);
$synopsis=nl2br(htmlspecialchars($candidate->Synopsis));
$resume=nl2br(htmlspecialchars($candidate->Resume));
$positiontype=(int)$candidate->PositionType;
$expyears=(int)$candidate->ExpYears;
$prevemployers=(int)$candidate->PrevEmployers;
if($prevemployers<=0) $prevemployers="";
$salaryrate=(int)$candidate->SalaryRate;
$salarybase=(int)$candidate->SalaryBase;
$relocate=(int)$candidate->Relocate;
$destination=$candidate->Destination;

$degree=(int)$candidate->Degree;
$degreemajor=(int)$candidate->DegreeMajor;
$degreesource=$candidate->DegreeSource;
$gradyear=(int)$candidate->GradYear;
if($gradyear<=0) $gradyear="";

$associations=$candidate->Associations;
$sex=(int)$candidate->Sex;
$eth=(int)$candidate->Eth;
$age=(int)$candidate->Age;


//echo "<pre>";
//print_r($candidate);
//echo "</pre>";

?>

<header class="page-header">
        <!--<h1 class="page-title"><?php the_title(); ?></h1>-->
</header>

<div id="primary" class="content-area">
    <div id="content" class="container">
<table border="0" width="720" cellspacing="0" cellpadding="2">
<tbody>
    <tr>
<td align="left" valign="middle"><h2>Candidate Profile</h2></td>
<td align="right" valign="middle"><div style="float: right;">[ <a href="/candidatesearchresults">Back To Results</a> ]&nbsp;&nbsp;&nbsp;[ <a href="/candidatesearch/?edit_search=1">Edit Search</a> ]&nbsp;&nbsp;&nbsp;[ <a href="/candidatesearch">New Search</a> ]</div></td>
</tr>
<!--*****Here are the links to candidate specific data e.g. Portfolio, Tests/Interviews, Phone Interviews, Notes, and References*****-->
<tr>
<td colspan="2" align="right" valign="middle">
    <div style="float: right;">
        [ <a class="pop-links-toggle" href="#portfolio">portfolio</a> ]&nbsp;&nbsp;&nbsp;[ <a class="pop-links-toggle" href="#tests">Tests/Interviews</a> ]&nbsp;&nbsp;&nbsp;[ <a class="pop-links-toggle" href="#notes">Notes</a> ]&nbsp;&nbsp;&nbsp;[ <a class="pop-links-toggle" href="#references">References</a> ]
    </div>
</td>
</tr>
</tbody>
</table>
<!--notes content-->
<?php include_once get_stylesheet_directory().'/old-archipro/partial-templates/notes.php'; ?>
<!--notes content end-->
<!--portfolio content-->
<?php include_once get_stylesheet_directory().'/old-archipro/partial-templates/portfolio.php'; ?>
<!--portfolio content end-->
<!--portfolio content-->
<?php include_once get_stylesheet_directory().'/old-archipro/partial-templates/references.php'; ?>
<!--portfolio content end-->
<!--portfolio content-->
<?php include_once get_stylesheet_directory().'/old-archipro/partial-templates/tests.php'; ?>
<!--portfolio content end-->
<table>
<tbody id="arc-profile">
<tr>
<td class="td_borderbottom" colspan="2" align="left"><span class="t2">&nbsp;<b>Personal Information</b></span></td>
</tr>
<tr>
    <td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="4" /></td>
</tr>
<tr>
<td class="c1" align="right" valign="top" nowrap="nowrap" style="padding-left: 12px;"><b>First Name:</b></td>
<td class="t1" align="left" valign="top" nowrap="nowrap">
<table border="0" width="540" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td class="t1" valign="top" width="40%">
<div style="width: 220px;"><?php echo $candidate->FirstName; ?></div></td>
<td class="c1" align="right" valign="top" nowrap="nowrap" width="40%"><b>Last Name:</b></td>
<td class="t1" align="left" valign="top" width="20%">
<div style="width: 200px;"><?php echo $candidate->LastName; ?></div></td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td class="c1" align="right" valign="top" nowrap="nowrap" style="padding-left: 12px;"><b>Email:</b></td>
<td class="t1" align="left" valign="top" nowrap="nowrap">
<table border="0" width="540" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td class="t1" valign="top" width="40%">

<div style="width: 220px;"><?php echo $candidate->Email; ?></div></td>
<td class="c1" align="right" valign="top" nowrap="nowrap" width="40%"><b>Phone:</b></td>
<td class="t1" align="left" valign="top" width="20%">
<div style="width: 200px;"><?php echo $candidate->Phone; ?></div></td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td class="c1" align="right" valign="top" nowrap="nowrap">&nbsp;<b>City:</b></td>
<td class="t1" align="left" valign="top" nowrap="nowrap">
<table border="0" width="540" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td class="t1" valign="top" width="40%">
<div style="width: 220px;"><?php echo $candidate->City; ?></div></td>
<td class="c1" align="right" valign="top" nowrap="nowrap" width="40%">&nbsp;<b>State/Province:</b></td>
<td class="t1" align="left" valign="top" width="20%">
<div style="width: 200px;"><?php echo $arr_states[$candidate->State]; ?></div></td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td class="c1" align="right" valign="top" nowrap="nowrap">&nbsp;<b>Zip/Postal Code:</b></td>
<td align="left" valign="top" nowrap="nowrap">
<table border="0" width="540" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td class="t1" valign="top" width="40%">
<div style="width: 220px;"><?php echo $candidate->Zip; ?></div></td>
<td class="c1" align="right" valign="top" nowrap="nowrap" width="40%">&nbsp;<b>Country:</b></td>
<td class="t1" align="left" valign="top" width="20%">
<div style="width: 200px;"><?php echo $arr_countries[$candidate->Country]; ?></div></td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="4" /></td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="8" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<table border="0" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td class="c1" align="left" valign="top">
<div style="padding: 0px 12px 0px 0px; margin-left: -7px;"><b>In Which Countries are you legally permitted to work?</b></div></td>
<td class="t1" align="left" valign="top"><?php echo $work_countries; ?></td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="4" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<div style="padding: 0px 12px 0px 0px; margin-left: 2px;"><b>Do you require a H1B Visa?</b>&nbsp;&nbsp;<span class="t1"><?php echo $candidate->H1B>0?'Yes':'No'; ?></span></div></td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="12" /></td>
</tr>
<tr>
<td class="td_borderbottom" colspan="2" align="left"><span class="t2">&nbsp;<b>Professional Experience</b></span></td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="8" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td class="c1" align="right" valign="top" width="15%">
<div style="padding: 0px 12px 0px 0px; margin-left: -7px;"><b>Synopsis:</b></div></td>
<td class="t1" align="left" valign="top" width="85%">
<div style="padding-right: 12px;">
<?php echo $synopsis; ?>
</div></td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="8" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td class="c1" align="right" valign="top" width="15%">
<div style="padding: 0px 12px 0px 0px; margin-left: -7px;"><b>Resume:</b></div></td>
<td class="t1" align="left" valign="top" width="85%">
<div style="padding-right: 12px;"><?php echo $resume; ?></div></td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="8" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<div style="padding: 0px 12px 0px 0px; margin-left: 4px;"><b>Are you interested in Permanent or Contracting positions?</b>&nbsp;&nbsp;<span class="t1"><?php echo $arr_positiontype[$positiontype]; ?></span></div></td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="2" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<div style="padding: 0px 12px 0px 0px; margin-left: 4px;"><b>Years of relevant experience:</b>&nbsp;&nbsp;<span class="t1"><?php echo $arr_expyears[$expyears]; ?></span></div></td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="2" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<div style="padding: 0px 12px 0px 0px; margin-left: 4px;"><b>How many employers have you worked for in the previous 24 months?</b>&nbsp;&nbsp;<span class="t1"><?php echo htmlspecialchars($prevemployers==''?'-':$prevemployers); ?></span></div></td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="4" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td class="c1" align="left" valign="top" width="40%">
<div style="padding: 0px 12px 0px 0px; margin-left: -7px;"><b>Salary Desired:</b>&nbsp;&nbsp;<span class="t1"><?php echo ($salaryrate>0?$arr_salaryrate[$salaryrate]:'Open'); ?></span></div></td>
<td class="c1" align="left" valign="top" width="60%">
<div style="padding-right: 12px;"><b>Pay based on:</b>&nbsp;&nbsp;<span class="t1"><?php echo $arr_salarybase[$salarybase]; ?></span></div></td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="8" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<div style="padding: 0px 12px 0px 0px; margin-left: 4px;"><b>Willing to Relocate?</b>&nbsp;&nbsp;<span class="t1"><?php echo $arr_relocate[$relocate]; ?></span></div></td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="2" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<div style="padding: 0px 12px 0px 0px; margin-left: 4px;"><b>Prefered relocation destination(s):</b>&nbsp;&nbsp;<span class="t1"><?php echo htmlspecialchars($destination==''?'-':$destination); ?></span></div></td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="12" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td class="c1" align="right" valign="top" width="15%">
<div style="padding: 0px 12px 0px 0px; margin-left: -7px;"><b>Position:</b></div></td>
<td class="t1" align="left" valign="top" width="85%">
<div style="padding-right: 12px;"><?php echo arc_get_all_postions_string($candidate->Positions, $arr_position); ?></div></td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="8" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td class="c1" align="right" valign="top" width="15%">
<div style="padding: 0px 12px 0px 0px; margin-left: -7px;"><b>Industry:</b></div></td>
<td class="t1" align="left" valign="top" width="85%">
    <div style="padding-right: 12px;"><?php echo arc_get_industries($candidate->Industries, $arr_industry) ?></div></td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="8" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td class="c1" align="right" valign="top" width="15%">
<div style="padding: 0px 12px 0px 0px; margin-left: -7px;"><b>Project Type:</b></div></td>
<td class="t1" align="left" valign="top" width="85%">
    <div style="padding-right: 12px;"><?php echo arc_get_project_types($candidate->ProjectTypes, $arr_projecttype); ?></div></td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="12" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td class="c1" align="left" valign="top">
<div style="padding: 0px 12px 0px 0px; margin-left: -7px;"><b>Of the project-areas listed below, please indicate those in which you have worked by placing a check in the relevant boxes. For those in which you are experienced, please indicate your role in relevant projects as well.</b></div></td>
</tr>
<tr>
<td class="t1" align="left" valign="top">
    <div style="padding: 6px 12px 0 12px;"><?php echo arc_get_roles($candidate->Roles, $arr_projectarea, $arr_role) ?></div></td>
</tr>
</tbody>
</table>
</td>
</tr>
<!--<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="12" /></td>
</tr>-->
<!--<tr>
<td class="td_borderbottom" colspan="2" align="left"><span class="t2">�<b>Employment History</b></span></td>
</tr>-->
<!--<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="8" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<div style="padding: 0 12px;">[ Available for <a href="/jboard/employer-registration/">members</a> ]</div></td>
</tr>-->
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="12" /></td>
</tr>
<tr>
<td class="td_borderbottom" colspan="2" align="left"><span class="t2">&nbsp;<b>Education</b></span></td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="8" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<div style="padding: 0px 12px 0px 0px; margin-left: 4px;"><b>Please indicate the highest degree of education you have attained:</b>&nbsp;&nbsp;<span class="t1"><?php echo $arr_degree[$degree]; ?></span></div></td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="4" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<div style="padding: 0px 12px 0px 0px; margin-left: 4px;"><b>If you have a degree, please indicate your major:</b>&nbsp;&nbsp;<span class="t1"><?php echo $arr_degreemajor[$degreemajor]; ?></span></div></td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="4" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<div style="padding: 0px 12px 0px 0px; margin-left: 4px;"><b>From what university/college did you receive your highest degree?</b>&nbsp;&nbsp;<span class="t1"><?php echo htmlspecialchars($degreesource); ?></span></div></td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="4" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<div style="padding: 0px 12px 0px 0px; margin-left: 4px;"><b>In what year did you graduate?</b>&nbsp;&nbsp;<span class="t1"><?php echo htmlspecialchars($gradyear); ?></span></div></td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="12" /></td>
</tr>
<tr>
<td class="td_borderbottom" colspan="2" align="left"><span class="t2">&nbsp;<b>Professional Registration</b></span></td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="8" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td class="c1" align="left" valign="top">
<div style="padding: 0px 12px 0px 0px; margin-left: -7px;"><b>Please indicate which licenses you currently hold and location of licenses:</b></div></td>
</tr>
<tr>
<td class="t1" align="left" valign="top">
    <div style="padding: 6px 12px 0 12px;"><?php echo arc_get_licenses($candidate->NCARB, $candidate->Licenses, $arr_license); ?></div></td>
</tr>
</tbody>
</table>
</td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="4" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<div style="padding: 0px 12px 0px 0px; margin-left: 4px;"><b>Indicate which professional associations you are affiliated with:</b>&nbsp;&nbsp;<span class="t1"><?php echo htmlspecialchars($associations==''?'-':$associations); ?></span></div></td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="12" /></td>
</tr>
<tr>
<td class="td_borderbottom" colspan="2" align="left"><span class="t2">&nbsp;<b>Equal Employment Opportunity Survey</b></span></td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="8" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<div style="padding: 0px 12px 0px 0px; margin-left: 4px;"><b>Please indicate your sex:</b>&nbsp;&nbsp;<span class="t1"><?php echo $arr_sex[$sex]; ?></span></div></td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="4" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<div style="padding: 0px 12px 0px 0px; margin-left: 4px;"><b>Please indicate the ethnic group with which you identify most:</b>&nbsp;&nbsp;<span class="t1"><?php echo $arr_eth[$eth]; ?></span></div></td>
</tr>
<tr>
<td colspan="2"><img src="<?php echo get_stylesheet_directory_uri(); ?>/images/space.gif" alt="" width="1" height="4" /></td>
</tr>
<tr>
<td class="c1" colspan="2" align="left" valign="middle">
<div style="padding: 0px 12px 0px 0px; margin-left: 4px;"><b>Please indicate your age range:</b>&nbsp;&nbsp;<span class="t1"><?php echo $arr_age[$age]; ?></span></div></td>
</tr>
</tbody>
</table>

    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        $prev_id = '';
        jQuery('.arc-extra-links').hide();
        jQuery(".pop-links-toggle").click(function(){
            $id = jQuery(this).attr('href');
            jQuery('.arc-extra-links').hide();
            $id = $id.replace("#", "");
            if($prev_id != $id){
                jQuery('#arc-'+$id).show();
            }else{
                $id = '';
            }
            $prev_id = $id;
        })
        jQuery('.arc-test-view-link,.arc-ref-view-link').click(function(){
            $element = jQuery(this).attr('href');
            $html = jQuery($element).clone();
            console.log($html);
            jQuery.colorbox({html:$html,width:"80%", height:"80%",overlayClose:false});
        })
    })
</script>

<?php get_footer(); ?>