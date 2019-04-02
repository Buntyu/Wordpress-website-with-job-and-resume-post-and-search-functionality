<?php 
/* Template Name: Candidate Search */ 

get_header();

if(!current_user_can('administrator')){
    echo "<h3 style='margin:100px 0px;text-align:center;'>You need to login as Administrator to view this page </h3>";
    get_footer();
    die();
}

include_once get_stylesheet_directory().'/old-archipro/arrays.php';
include_once get_stylesheet_directory().'/old-archipro/states.php';
include_once get_stylesheet_directory().'/old-archipro/functions.php';

if(isset($_GET['edit_search'])){
    $search = get_transient('arc_candidate_search_admin');
}else{
    $search = array(
    'keyword' => '',
    'first_name' => '',
    'last_name' => '',
    'position' => '',
    'industry' => '',
    'projecttype' => '',
    'positiontype' => '',
    'expyears' => '',
    'degree' => '',
    'freshness' => '',
    'city' =>'',
    'state' =>'',
    'zip' =>'',
    'dist' => '',
    'country' => '',
    'comp' => ''
    );
}

?>

<header class="page-header">
        <!--<h1 class="page-title"><?php the_title(); ?></h1>-->
</header>

<div id="primary" class="content-area">
    <div id="content" class="container">

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td align="left" valign="middle">
<h2>Candidate Search</h2>
</td>
</tr>
</tbody>
</table>
<form action="../candidatesearchresults" method="post" name="fsearch">
<p style="text-align: left;"><input name="action" type="hidden" value="show" /></p>

<table class="formtable" border="0" width="640" cellspacing="0" cellpadding="2">
<tbody>
<tr>
    <td class="color_dark" colspan="3" align="left"><span class="t1">&nbsp;<b>Keyword Search</b></span></td>
</tr>
<tr>
<td colspan="3"></td>
</tr>
<tr>
<td class="t1" align="left" valign="middle" nowrap="nowrap">&nbsp;<b>Search For:</b></td>
<td align="left" valign="middle"><input style="width: 200px;" maxlength="128" name="keyword" size="52" type="text" value="<?php echo $search['keyword']; ?>" /></td>
<td class="t1" align="left" valign="middle"></td>
</tr>
<tr>
<td colspan="3"></td>
</tr>
<!--Name Search start-->
<tr>
    <td class="color_dark" colspan="3" align="left"><span class="t1">&nbsp;<b>Name Search</b></span></td>
</tr>
<tr>
<td colspan="3"></td>
</tr>
<tr>
<td class="t1" align="left" valign="middle" nowrap="nowrap">&nbsp;<b>Name:</b></td>
<td style="width: 350px;" align="left" valign="middle"><input style="width: 150px;" placeholder="first name" maxlength="128" name="first_name" size="52" type="text" value="<?php echo $search['first_name']; ?>" />
<input style="width: 150px;" placeholder="last name" maxlength="128" name="last_name" size="52" type="text" value="<?php echo $search['last_name']; ?>" />
</td>
<td class="t1" align="left" valign="middle"></td>
</tr>
<tr>
<td colspan="3"></td>
</tr>
<!--Name search end-->
<tr>
<td class="color_dark" colspan="3" align="left"><span class="t1">&nbsp;<b>Resume Characteristics</b></span></td>
</tr>
<tr>
<td colspan="3"></td>
</tr>
<tr>
<td class="t1" align="left" valign="middle" nowrap="nowrap">&nbsp;<b>Position:</b></td>
<td align="left" valign="middle">
<?php echo arc_create_select($arr_position, 'position',$search['position']); ?>
</td>
<td class="c0" align="left" valign="middle">
<div class="c0" style="padding: 2px 8px 2px 8px;">Select the position that applies to the resume you are looking for.</div></td>
</tr>
<tr>
<td colspan="3"></td>
</tr>
<tr>
<td class="t1" align="left" valign="middle" nowrap="nowrap">&nbsp;<b>Industry:</b></td>
<td align="left" valign="middle">
<?php echo arc_create_select($arr_industry, 'industry',$search['industry']); ?>
</td>
<td class="t1" align="left" valign="middle">
<div class="c0" style="padding: 2px 8px 2px 8px;">Select the industry that applies to the resume you are looking for.</div></td>
</tr>
<tr>
<td colspan="3"></td>
</tr>
<tr>
<td class="t1" align="left" valign="middle" nowrap="nowrap">&nbsp;<b>Project Type:</b></td>
<td align="left" valign="middle">
<?php echo arc_create_select($arr_projecttype, 'projecttype',$search['projecttype']); ?>    
</td>
<td class="t1" align="left" valign="middle">
<div class="c0" style="padding: 2px 8px 2px 8px;">Select the category that applies to the resume you are looking for.</div></td>
</tr>
<tr>
<td colspan="3"></td>
</tr>
<tr>
<td class="t1" align="left" valign="top" nowrap="nowrap">&nbsp;<b>Employment Type:</b></td>
<td class="t1" align="left" valign="top">
<table border="0" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td class="t1" valign="middle"><input id="positiontype1" style="padding: 1px 2px;" <?= $search['positiontype']==1?'checked="checked"':'';  ?> name="positiontype" type="radio" value="1" /></td>
<td class="t1" align="left" valign="middle"><label style="padding: 1px 2px 1px 6px;" for="positiontype1">Either</label></td>
</tr>
<tr>
<td class="t1" valign="middle"><input id="positiontype2" style="padding: 1px 2px;" <?= $search['positiontype']==2?'checked="checked"':'';  ?> name="positiontype" type="radio" value="2" /></td>
<td class="t1" align="left" valign="middle"><label style="padding: 1px 2px 1px 6px;" for="positiontype2">Permanent</label></td>
</tr>
<tr>
<td class="t1" valign="middle"><input id="positiontype3" style="padding: 1px 2px;" <?= $search['positiontype']==3?'checked="checked"':'';  ?> name="positiontype" type="radio" value="3" /></td>
<td class="t1" align="left" valign="middle"><label style="padding: 1px 2px 1px 6px;" for="positiontype3">Contractor</label></td>
</tr>
</tbody>
</table>
</td>
<td class="t1" align="left" valign="top">
<div class="c0" style="padding: 2px 8px 2px 8px;">Are you looking for someone interested in becoming an Employee or Contractor?</div></td>
</tr>
<tr>
<td colspan="3"></td>
</tr>
<tr>
<td class="t1" align="left" valign="top" nowrap="nowrap">&nbsp;<b>Experience:</b></td>
<td class="t1" align="left" valign="top">
<table border="0" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td class="t1" valign="middle"><input id="expyears1" style="padding: 1px 2px;" <?= $search['expyears']==1?'checked="checked"':'';  ?> name="expyears" type="radio" value="1" /></td>
<td class="t1" align="left" valign="middle"><label style="padding: 1px 2px 1px 6px;" for="expyears1">Less than 3 years</label></td>
</tr>
<tr>
<td class="t1" valign="middle"><input id="expyears2" style="padding: 1px 2px;" <?= $search['expyears']==2?'checked="checked"':'';  ?> name="expyears" type="radio" value="2" /></td>
<td class="t1" align="left" valign="middle"><label style="padding: 1px 2px 1px 6px;" for="expyears2">3 to 6 years</label></td>
</tr>
<tr>
<td class="t1" valign="middle"><input id="expyears3" style="padding: 1px 2px;" <?= $search['expyears']==3?'checked="checked"':'';  ?> name="expyears" type="radio" value="3" /></td>
<td class="t1" align="left" valign="middle"><label style="padding: 1px 2px 1px 6px;" for="expyears3">6 to 8 years</label></td>
</tr>
<tr>
<td class="t1" valign="middle"><input id="expyears4" style="padding: 1px 2px;" <?= $search['expyears']==4?'checked="checked"':'';  ?> name="expyears" type="radio" value="4" /></td>
<td class="t1" align="left" valign="middle"><label style="padding: 1px 2px 1px 6px;" for="expyears4">8 to 10 years</label></td>
</tr>
<tr>
<td class="t1" valign="middle"><input id="expyears5" style="padding: 1px 2px;" <?= $search['expyears']==5?'checked="checked"':'';  ?> name="expyears" type="radio" value="5" /></td>
<td class="t1" align="left" valign="middle"><label style="padding: 1px 2px 1px 6px;" for="expyears5">More than 10 years</label></td>
</tr>
</tbody>
</table>
</td>
<td class="t1" align="left" valign="top">
<div class="c0" style="padding: 2px 8px 2px 8px;">What minimum level of experience are you looking for?</div></td>
</tr>
<tr>
<td colspan="3"></td>
</tr>
<tr>
<td class="t1" align="left" valign="top" nowrap="nowrap">&nbsp;<b>Higher Education:</b></td>
<td class="t1" align="left" valign="top">
<table border="0" cellspacing="0" cellpadding="0">
<tbody>
<tr>
<td class="t1" valign="middle"><input id="degree1" style="padding: 1px 2px;" <?= $search['degree']==1?'checked="checked"':'';  ?> name="degree" type="radio" value="1" /></td>
<td class="t1" align="left" valign="middle"><label style="padding: 1px 2px 1px 6px;" for="degree1">High School</label></td>
</tr>
<tr>
<td class="t1" valign="middle"><input id="degree2" style="padding: 1px 2px;" <?= $search['degree']==2?'checked="checked"':'';  ?> name="degree" type="radio" value="2" /></td>
<td class="t1" align="left" valign="middle"><label style="padding: 1px 2px 1px 6px;" for="degree2">Some College</label></td>
</tr>
<tr>
<td class="t1" valign="middle"><input id="degree3" style="padding: 1px 2px;" <?= $search['degree']==3?'checked="checked"':'';  ?> name="degree" type="radio" value="3" /></td>
<td class="t1" align="left" valign="middle"><label style="padding: 1px 2px 1px 6px;" for="degree3">Technical Degree</label></td>
</tr>
<tr>
<td class="t1" valign="middle"><input id="degree4" style="padding: 1px 2px;" <?= $search['degree']==4?'checked="checked"':'';  ?> name="degree" type="radio" value="4" /></td>
<td class="t1" align="left" valign="middle"><label style="padding: 1px 2px 1px 6px;" for="degree4">Bachelor's Degree</label></td>
</tr>
<tr>
<td class="t1" valign="middle"><input id="degree5" style="padding: 1px 2px;" <?= $search['degree']==5?'checked="checked"':'';  ?> name="degree" type="radio" value="5" /></td>
<td class="t1" align="left" valign="middle"><label style="padding: 1px 2px 1px 6px;" for="degree5">Master's Degree</label></td>
</tr>
<tr>
<td class="t1" valign="middle"><input id="degree6" style="padding: 1px 2px;" <?= $search['degree']==6?'checked="checked"':'';  ?> name="degree" type="radio" value="6" /></td>
<td class="t1" align="left" valign="middle"><label style="padding: 1px 2px 1px 6px;" for="degree6">Ph.D.</label></td>
</tr>
</tbody>
</table>
</td>
<td class="t1" align="left" valign="top">
<div class="c0" style="padding: 2px 8px 2px 8px;">What is the minimum higher education level you are looking for?</div></td>
</tr>
<tr>
<td colspan="3"></td>
</tr>
<tr>
<td class="t1" align="left" valign="middle" nowrap="nowrap">&nbsp;<b>Freshness:</b></td>
<td align="left" valign="middle">
<?php echo arc_create_select($arr_freshness, 'freshness',$search['freshness']); ?>
</td>
<td class="t1" align="left" valign="middle"></td>
</tr>
<tr>
<td colspan="3"></td>
</tr>
<tr>
<td class="color_dark" colspan="3" align="left"><span class="t1">&nbsp;<b>Target Location</b></span></td>
</tr>
<tr>
<td colspan="3"></td>
</tr>
<tr>
<td class="t1" align="left" valign="middle" nowrap="nowrap">&nbsp;<b>City:</b></td>
<td align="left" valign="middle"><input style="width: 200px;" maxlength="128" name="city" size="52" type="text" value="<?php echo $search['city']; ?>" /></td>
<td class="c0" align="left" valign="middle">
<div class="c0" style="padding: 2px 8px 2px 8px;">All geographic search criteria is optional.</div></td>
</tr>
<tr>
<td colspan="3"></td>
</tr>
<tr>
<td class="t1" align="left" valign="middle" nowrap="nowrap">&nbsp;<b>State/Province:</b></td>
<td align="left" valign="middle">
    <?php echo arc_create_select($arr_states, 'state',$search['state']); ?>
</td>
<td class="c0" align="left" valign="middle"></td>
</tr>
<tr>
<td colspan="3"></td>
</tr>
<tr>
<td class="t1" align="left" valign="middle" nowrap="nowrap">&nbsp;<b>Zip:</b></td>
<td align="left" valign="middle" nowrap="nowrap"><input style="width: 200px;" maxlength="12" name="zip" size="52" type="text" value="<?php echo $search['zip']; ?>" />
<!--    <select style="width: 100px;" name="dist">
<option value="10">10 miles</option>
<option value="25">25 miles</option>
<option value="50">50 miles</option>
<option value="75">75 miles</option>
<option value="100">100 miles</option>
</select>-->
</td>
<td class="c0" align="left" valign="middle"></td>
</tr>
<tr>
<td colspan="3"></td>
</tr>
<tr>
<td class="t1" align="left" valign="middle" nowrap="nowrap">&nbsp;<b>Country:</b></td>
<td align="left" valign="middle">
    <?php echo arc_create_select($arr_countries, 'country',$search['country']); ?>
    </td>
<td class="c0" align="left" valign="middle"></td>
</tr>
<tr>
<td colspan="3"></td>
</tr>
<tr>
<td class="color_dark" colspan="3" align="left"><span class="t1">&nbsp;<b>Compensation</b></span></td>
</tr>
<tr>
<td colspan="3"></td>
</tr>
<tr>
<td style="text-align: left;" colspan="2" align="right" valign="top">
<?php echo arc_create_select($arr_compensation, 'comp',$search['comp']); ?>
    </td>
<td class="t1" align="left" valign="top">
<div class="c0" style="padding: 2px 8px; text-align: left;">Please select the pay ranges that you are interested in seeing. If you do not select any pay ranges, the results will show all applicable resumes without regard to pay range.</div></td>
</tr>
</tbody>
</table>
<div class="searchreset">
<div class="item-1"><input name="but_search" type="submit" value=" Search " /></div>
<div class="item-2"><input id="reset_frm" name="but_reset" type="submit" value=" Reset " /></div>
</div>
</form>

        </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#reset_frm').click(function(){
            jQuery("form[name='fsearch'] option").removeAttr('selected');
            jQuery("form[name='fsearch'] input[type='text']").val('');
            jQuery("form[name='fsearch'] input[type='radio']").removeAttr('checked');
            return false;
        })
    })
</script>
<?php get_footer(); ?>