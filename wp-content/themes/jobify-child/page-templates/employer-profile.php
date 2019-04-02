<?php

/* Template Name: Employer Profile */

get_header(); 

if(!current_user_can('administrator')){
    echo "<h3 style='margin:100px 0px;text-align:center;'>You need to login as Administrator to view this page </h3>";
    get_footer();
    die();
}

include_once get_stylesheet_directory().'/old-archipro/class-archpiro-main.php';
include_once get_stylesheet_directory().'/old-archipro/arrays.php';
include_once get_stylesheet_directory().'/old-archipro/functions.php';
include_once get_stylesheet_directory().'/old-archipro/states.php';

$mydb = new wpdb('citysca1_db1','PLE+C.!N~)Um','citysca1_db1','localhost');
//$mydb = new wpdb('root','','archipro_core','localhost');
$employer = $mydb->get_results("select * from EMPLOYERS where id=".  intval($_GET['id']));

if($employer){
    $f = $employer[0];
}

$companyname = $f->CompanyName;
$industry = $f->Industry;
$principalname = $f->PrincipalName;
$contactname = $f->ContactName;
$contactposition = $f->ContactPosition;
$contactdepartment = $f->ContactDepartment;
$address = $f->Address;
$city = $f->City;
$state = $arr_states[$f->State];
$zip = $f->Zip;
$country = $arr_countries[$f->Country];
$phone = $f->Phone;
$fax = $f->Fax;
$url = $f->URL;
$referralname = $f->ReferralName;
$projecttype = $f->ProjectType;
$estyear = $f->EstYear>0?((int)$f->EstYear):'';
$officescount = $f->OfficesCount>0?((int)$f->OfficesCount):'';
$employeescount = $f->EmployeesCount>0?((int)$f->EmployeesCount):'';

$arc_obj = new archipro_main();
$user_notes = $arc_obj->get_user_notes($f->UserID, 'E');
$user_id = $f->UserID;
$user_type = 'e';
?>
<header class="page-header">
        <!--<h1 class="page-title"><?php the_title(); ?></h1>-->
</header>

<div id="primary" class="content-area">
    <div id="content" class="container">
    <table border="0" width="720" cellspacing="0" cellpadding="2">
        <tbody>
            <tr>
                <td align="left" valign="middle"><h2>Employer Profile</h2></td>
                <td align="right" valign="middle"><div style="float: right;">[ <a href="/employer-search-results">Back To Results</a> ]&nbsp;&nbsp;&nbsp;[ <a href="/employer-search/?edit_search=1">Edit Search</a> ]&nbsp;&nbsp;&nbsp;[ <a href="/employer-search">New Search</a> ]</div></td>
            </tr>


<!--*****Here are the links to employer specific data e.g. Notes*****-->
<tr>
<td colspan="2" align="right" valign="middle"><div style="float: right;">[ <a class="pop-links-toggle" href="#notes">Notes</a> ]</td>
</tr>
</tbody>
    </table>
<!--notes content-->
<?php include_once get_stylesheet_directory().'/old-archipro/partial-templates/notes.php'; ?>
<!--notes content end-->
<table id="arc-profile">
        <tbody>
            <tr>
                <td align=right valign=top nowrap class=c1>&nbsp;<b>Company Name:</b>&nbsp;</td>
                <td align=left  valign=top class=t1><div style="width: 536px;"><?= htmlspecialchars($companyname) ?></div></td>
            </tr>
            <tr>
                <td align=right valign=top nowrap class=c1>&nbsp;<b>Principals Name:</b>&nbsp;</td>
                <td align=left  valign=top nowrap>
                    <table width="540" cellspacing="0" cellpadding="0" border="0">
                    <tr>
                        <td width="40%" valign="top" class=t1><div style="width: 220px"><?= htmlspecialchars($principalname) ?></div></td>
                        <td width="40%" align=right valign=top class=c1 nowrap>&nbsp;<b>Industry:</b>&nbsp;&nbsp;</td>
                        <td width="20%" align=left  valign=top class=t1><div style="width: 200px"><?= htmlspecialchars($industry) ?></div></td>
                    </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td align=right valign=top nowrap class=c1>&nbsp;<b>Contact Name:</b>&nbsp;</td>
                <td align=left  valign=top class=t1><div style="width: 220px"><?= htmlspecialchars($contactname) ?></div></td>
            </tr>
            <tr>
                <td align=right valign=top nowrap class=c1>&nbsp;<b>Contact Position:</b>&nbsp;</td>
                <td align=left  valign=top nowrap>
                    <table width="540" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td width="40%" valign="top" class=t1><div style="width: 220px"><?= htmlspecialchars($contactposition) ?></div></td>
                            <td width="40%" align=right valign=top class=c1 nowrap>&nbsp;<b>Department:</b>&nbsp;&nbsp;</td>
                            <td width="20%" align=left  valign=top class=t1><div style="width: 200px"><?= htmlspecialchars($contactdepartment) ?></div></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td align=right valign=top nowrap class=c1>&nbsp;<b>Address:</b>&nbsp;</td>
                <td align=left  valign=top class=t1><div style="width: 536px;"><?= htmlspecialchars($address) ?></div></td>
            </tr>
            <tr>
                <td align=right valign=top nowrap class=c1>&nbsp;<b>City:</b>&nbsp;</td>
                <td align=left  valign=top nowrap class=t1>
                    <table width="540" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td width="40%" valign="top" class=t1><div style="width: 220px"><?= htmlspecialchars($city) ?></div></td>
                            <td width="40%" align=right valign=top class=c1 nowrap>&nbsp;<b>State/Province:</b>&nbsp;&nbsp;</td>
                            <td width="20%" align=left  valign=top class=t1><div style="width: 200px;"><?= $state ?></div></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td align=right valign=top nowrap class=c1>&nbsp;<b>Zip/Postal Code:</b>&nbsp;</td>
                <td align=left  valign=top nowrap>
                    <table width="540" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td width="40%" valign="top" class=t1><div style="width: 220px"><?= htmlspecialchars($zip) ?></div></td>
                            <td width="40%" align=right valign=top class=c1 nowrap>&nbsp;<b>Country:</b>&nbsp;&nbsp;</td>
                            <td width="20%" align=left  valign=top class=t1><div style="width: 200px"><?= $country ?></div></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td align=right valign=top nowrap class=c1>&nbsp;<b>Phone:</b>&nbsp;</td>
                <td align=left  valign=top nowrap>
                    <table width="540" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                          <td width="40%" valign="top" class=t1><div style="width: 220px"><?= htmlspecialchars($phone) ?></div></td>
                          <td width="40%" align=right valign=top class=c1 nowrap>&nbsp;<b>Fax:</b>&nbsp;&nbsp;</td>
                          <td width="20%" align=left  valign=top class=t1><div style="width: 200px"><?= htmlspecialchars($fax)?></div></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td align=right valign=top nowrap class=c1>&nbsp;<b>Email:</b>&nbsp;</td>
                <td align=left  valign=top nowrap>
                    <table width="540" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td width="40%" valign="top" class=t1><div style="width: 220px"><?= htmlspecialchars($email) ?></div></td>
                            <td width="40%" align=right valign=top class=c1 nowrap>&nbsp;<b>Web Site:</b>&nbsp;&nbsp;</td>
                            <td width="20%" align=left  valign=top class=t1><div style="width: 200px"><?= htmlspecialchars($url) ?></div></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td align=right valign=top nowrap class=c1>&nbsp;<b>Referral Name:</b>&nbsp;</td>
                <td align=left  valign=top class=t1><div style="width: 220px"><?= htmlspecialchars($referralname) ?></div></td>
            </tr>
            <tr>
                <td align=right valign=top nowrap class=c1>&nbsp;<b>Type of Projects:</b>&nbsp;</td>
                <td align=left  valign=top nowrap>
                    <table width="540" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td width="40%" valign="top" class=t1><div style="width: 220px"><?= htmlspecialchars($projecttype)?></div></td>
                            <td width="50%" align=right valign=top class=c1 nowrap>&nbsp;<b>Number of Office Locations:</b>&nbsp;&nbsp;</td>
                            <td width="10%" align=left  valign=top class=t1><div style="width: 100px"><?= htmlspecialchars($officescount)?></div></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td align=right valign=top nowrap class=c1>&nbsp;<b>Year Established:</b>&nbsp;</td>
                <td align=left  valign=top nowrap>
                    <table width="540" cellspacing="0" cellpadding="0" border="0">
                        <tr>
                            <td width="40%" valign="top" class=t1><div style="width: 220px"><?= htmlspecialchars($estyear) ?></div></td>
                            <td width="50%" align=right valign=top class=c1 nowrap>&nbsp;<b>Number of Employees:</b>&nbsp;&nbsp;</td>
                            <td width="10%" align=left  valign=top class=t1><div style="width: 100px"><?= htmlspecialchars($employeescount) ?></div></td>
                        </tr>
                    </table>
                </td>
            </tr>
        </tbody>
    </table>
    </div>
</div>
    
<script type="text/javascript">
jQuery(document).ready(function(){
    jQuery('.arc-extra-links').hide();
    jQuery(".pop-links-toggle").click(function(){
        jQuery('.arc-extra-links').toggle();
    })
})
</script>

<?php get_footer(); ?>