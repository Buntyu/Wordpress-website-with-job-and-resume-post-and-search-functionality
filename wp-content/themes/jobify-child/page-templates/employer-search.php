<?php

/* Template Name: Employer Search */ 

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
    $search = get_transient('arc_employer_search_admin');
}else{
    $search = array(
        'keyword' => '',
        'company_name' => '',
        'principal_name' => '',
        'industry' => '',
        'contact_name' => '',
        'contact_position' => '',
        'department' => '',
        'city' =>'',
        'state' =>'',
        'zip' =>'',
        'country' => '',
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
<h2>Employer Search</h2>
</td>
</tr>
</table>
<form action="<?php echo get_site_url(); ?>/employer-search-results" method="post" name="fsearch">
    <p style="text-align: left;"><input name="action" type="hidden" value="show" /></p>
    <table class="formtable" border="0" width="640" cellspacing="0" cellpadding="2">
        <tbody>
            <!--KeyWord search start-->
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
            <!--keyword search end-->
            <!--employer profile search-->
            <tr>
                <td class="color_dark" colspan="3" align="left"><span class="t1">&nbsp;<b>Employer characteristics</b></span></td>
            </tr>
            <tr>
            <td colspan="3"></td>
            </tr>
            <tr>
            <td class="t1" align="left" valign="middle" nowrap="nowrap">&nbsp;<b>Company Name:</b></td>
            <td align="left" valign="middle">
                <input style="width: 200px;" name="company_name" type="text" value="<?php echo $search['company_name']; ?>" />
            </td>
            <td class="c0" align="left" valign="middle"></td>
            </tr>
            <tr>
            <td class="t1" align="left" valign="middle" nowrap="nowrap">&nbsp;<b>Principals Name:</b></td>
            <td align="left" valign="middle">
                <input style="width: 200px;" name="principal_name" type="text" value="<?php echo $search['principal_name']; ?>" />
            </td>
            <td class="c0" align="left" valign="middle"></td>
            </tr>
            <tr>
            <td class="t1" align="left" valign="middle" nowrap="nowrap">&nbsp;<b>Industry:</b></td>
            <td align="left" valign="middle">
                <input style="width: 200px;" name="industry" type="text" value="<?php echo $search['industry']; ?>" />
            </td>
            <td class="c0" align="left" valign="middle"></td>
            </tr>
            <tr>
            <td class="t1" align="left" valign="middle" nowrap="nowrap">&nbsp;<b>Contact Name:</b></td>
            <td align="left" valign="middle">
                <input style="width: 200px;" name="contact_name" type="text" value="<?php echo $search['contact_name']; ?>" />
            </td>
            <td class="c0" align="left" valign="middle"></td>
            </tr>
            <tr>
            <td class="t1" align="left" valign="middle" nowrap="nowrap">&nbsp;<b>Contact Position:</b></td>
            <td align="left" valign="middle">
                <input style="width: 200px;" name="contact_position" type="text" value="<?php echo $search['contact_position']; ?>" />
            </td>
            <td class="c0" align="left" valign="middle"></td>
            </tr>
            <tr>
            <td class="t1" align="left" valign="middle" nowrap="nowrap">&nbsp;<b>Department:</b></td>
            <td align="left" valign="middle">
                <input style="width: 200px;" name="department" type="text" value="<?php echo $search['department']; ?>" />
            </td>
            <td class="c0" align="left" valign="middle"></td>
            </tr>
            <!--employer profile end-->
            <!--location search start-->
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
            <!--location search end-->
        </tbody>
    </table>
    <div class="searchreset">
        <div class="item-1"><input name="but_search" type="submit" value=" Search " /></div>
        <div class="item-2"><input id="reset_frm" name="but_reset" type="submit" value=" Reset " /></div>
    </div>
</form>
</tbody>
</table>
    </div>
</div>

<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('#reset_frm').click(function(){
            jQuery("form[name='fsearch'] option").removeAttr('selected');
            jQuery("form[name='fsearch'] input[type='text']").val('');
//            jQuery("form[name='fsearch'] input[type='radio']").removeAttr('checked');
            return false;
        })
    })
</script>

<?php get_footer(); ?>
