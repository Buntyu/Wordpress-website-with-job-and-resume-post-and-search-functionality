<style>
#crmWebToEntityForm p {display: none;}
.zoho-form {width: 61%;margin: auto;border: 1px solid #ccc;padding: 1%;}
#crmWebToEntityForm div {padding: 0px !important;}
.blubtn:nth-child(2) {display: none;}
.blubtn {margin-top: 3%;}
</style>
<?php

$rid = $_GET['resume_id'];
//echo $rid;

$cName = get_post_meta( $rid, '_candidate_name');
$cPhone = get_post_meta( $rid, '_candidate_phone');
$cLoc = get_post_meta( $rid, '_candidate_location');
$cCountry = get_post_meta( $rid, 'geolocation_country_long');
$cState = get_post_meta( $rid, 'geolocation_state_long');
$cTitle = get_post_meta( $rid, '_candidate_title');
$cEmail = get_post_meta( $rid, '_candidate_email');
$cContent = get_post_meta( $rid, '_resume_content');
$cUrls = get_post_meta( $rid, '_links');
$cIndustry = get_post_meta( $rid, '_arc_industry');
$cPosition = get_post_meta( $rid, '_arc_position');

$indy = $cIndustry[0];
$arr = explode(',', $indy);
$ac = count($arr);
foreach ($arr as $vals){
	if($vals == 1){$fin = "Architecture";}else if($vals == 2){$fin = "Appraisal";} else if($vals == 3){$fin = "Construction Management";} else if($vals == 4){$fin = "Civil Engineering";} else if($vals == 5){$fin = "Commercial";} else if($vals == 6){$fin = "Concept/Theme Design";} else if($vals == 7){$fin = "Construction Estimating";} else if($vals == 8){$fin = "Custom Residential";} else if($vals == 9){$fin = "Design Build";} else if($vals == 10){$fin = "Development";} else if($vals == 11){$fin = "Drafting/Production";} else if($vals == 12){$fin = "Education";} else if($vals == 13){$fin = "Entertainment Facilities";} else if($vals == 14){$fin = "Environmental Design";} else if($vals == 15){$fin = "Event Planing";} else if($vals == 16){$fin = "Fabric, Finishes, Equipment";} else if($vals == 17){$fin = "Facilities Management";} else if($vals == 18){$fin = "Furniture";} else if($vals == 19){$fin = "Green Design";} else if($vals == 20){$fin = "Healthcare";} else if($vals == 21){$fin = "High Rise";} else if($vals == 22){$fin = "Historic Preservation";} else if($vals == 23){$fin = "Hospitality";} else if($vals == 24){$fin = "Interior Design";} else if($vals == 25){$fin = "Leasing";} else if($vals == 26){$fin = "Landscape Design";} else if($vals == 27){$fin = "Legal/Finance";} else if($vals == 28){$fin = "Marketing/Sales";} else if($vals == 29){$fin = "Mechanical/Electrical";} else if($vals == 30){$fin = "Multi-Family Residential";} else if($vals == 31){$fin = "Prefab/Modular";} else if($vals == 32){$fin = "Property Management";} else if($vals == 33){$fin = "Real Estate Development";} else if($vals == 34){$fin = "Rendering Artists";} else if($vals == 35){$fin = "Retail Display";} else if($vals == 36){$fin = "Senior Living Facilities";} else if($vals == 37){$fin = "Set Design";} else if($vals == 38){$fin = "Single Family Residential";} else if($vals == 39){$fin = "Space Planning";} else if($vals == 40){$fin = "Structural Engineering";} else if($vals == 41){$fin = "Town Planning";} else if($vals == 42){$fin = "Transportation";} else if($vals == 43){$fin = "Urban Planning";} else if($vals == 44){$fin = "Yacht Design";}
	//echo $fin.'<br>';
	if($keys<$ac-1){
	$indus .= $fin.',';	
	}
	else{
		$indus .= $fin;
	}	
}

$posi = $cPosition[0];
$prr = explode(',', $posi);
$vals = $prr[0];

if($vals == 1){$pin = "Academic Faculty";}else if($vals == 2){$pin = "Accounting/Finance";} else if($vals == 3){$pin = "Administrative Support";} else if($vals == 4){$pin = "Architect";} else if($vals == 5){$pin = "Architectural Intern";} else if($vals == 6){$pin = "Computer Aided Design";} else if($vals == 7){$pin = "Architect, Concept Theme Design";} else if($vals == 8){$pin = "Construction Management";} else if($vals == 9){$pin = "Design Architect";} else if($vals == 10){$pin = "Engineering";} else if($vals == 11){$pin = "Facilities Management";} else if($vals == 12){$pin = "Architect, Graphic Design";} else if($vals == 13){$pin = "Healthcare Architect, Project Architect";} else if($vals == 14){$pin = "Industrial Design";} else if($vals == 16){$pin = "Interior Designer";} else if($vals == 17){$pin = "Interior Architect";} else if($vals == 18){$pin = "Job Captain";} else if($vals == 19){$pin = "Landscape Architect";} else if($vals == 20){$pin = "Administrative Support, Library Services/Knowledge Management";} else if($vals == 21){$pin = "Marketing";} else if($vals == 22){$pin = "Marketing, Multi Media Rendering";} else if($vals == 23){$pin = "Owners Representative";} else if($vals == 24){$pin = "Planner";} else if($vals == 25){$pin = "Project Manager";} else if($vals == 26){$pin = "Architect, Project Architect, Project Manager";} else if($vals == 27){$pin = "Security Design";} else if($vals == 28){$pin = "Marketing/Sales";} else if($vals == 30){$pin = "Sales Representative";}


/*$co = count($cUrls[0]);
foreach ($cUrls[0] as $key=>$value){
	$x=1;
	foreach($value as $k=>$v){
	$pp .= "$v";
	if($x==1){
	$pp .= '  :  ';
}
$x++;
}
if($key < $co-1){
$pp .= '  ,  ';
}}*/

$ss = $cUrls[0];
foreach ($ss[0] as $key=>$value){
	$pp = $value;
}

echo '<span style="display:none;" id="zname">'.$cName[0].'</span>';
echo '<span style="display:none;" id="zemail">'.$cEmail[0].'</span>';
echo '<span style="display:none;" id="zphone">'.$cPhone[0].'</span>';
echo '<span style="display:none;" id="zweb">'.$pp.'</span>';
echo '<span style="display:none;" id="zcity">'.$cLoc[0].'</span>';
echo '<span style="display:none;" id="zcountry">'.$cCountry[0].'</span>';
echo '<span style="display:none;" id="zstate">'.$cState[0].'</span>';
echo '<span style="display:none;" id="zaddi">'.$cContent[0].'</span>';
echo '<span style="display:none;" id="zindus">'.$indus.'</span>';
echo '<span style="display:none;" id="zposi">'.$pin.'</span>';

echo "<div class='resume-submit-msg'>";
switch ( $resume->post_status ) :
	case 'publish' :
		if ( resume_manager_user_can_view_resume( $resume->ID ) ) {
			printf( '<p class="resume-submitted">' . __( 'Your resume has been submitted successfully. To view your resume <a href="%s">click here</a>.', 'wp-job-manager-resumes' ) . '</p>', get_permalink( $resume->ID ) );
		} else {
			print( '<p class="resume-submitted">' . __( 'Your resume has been submitted successfully.', 'wp-job-manager-resumes' ) . '</p>' );
		}
	break;
	case 'pending' :
            if(is_user_logged_in()){
                print( '<p class="resume-submitted">' . __( 'Your resume has been submitted successfully. ', 'wp-job-manager-resumes' ) . 'View <a href="'.  site_url('/candidate-dashboard').'">Candidate Dashboard.</a></p>' );
            }else{
		print( '<p class="resume-submitted">' . __( 'Your resume has been submitted successfully. Please check your email for your username and temporary password.', 'wp-job-manager-resumes' ) . '</p>' );
            }
	break;
	default :
		do_action( 'resume_manager_resume_submitted_content_' . str_replace( '-', '_', sanitize_title( $resume->post_status ) ), $resume );
	break;
endswitch;
echo "</div>";

?>
<div class="zoho-form">
<h3>Please upload resume and photo again to complete the process</h3>
<script src='https://recruit.zoho.com/recruit/WebFormServeServlet?rid=9bd39adf26ae61e391d3374315055d80126c348c9224137c59fa6bbf4c84fedagid836bae33bdaf3568beca56f10bc104dd432c8e178fe8938aa6cca3cc4bec9624&script=$sYG'></script>


<script>
function sendtoZoho() {
document.getElementsByName("Last Name")[0].value = document.getElementById('zname').innerHTML;
document.getElementsByName("Email")[0].value = document.getElementById('zemail').innerHTML;
document.getElementsByName("Phone")[0].value = document.getElementById('zphone').innerHTML;
document.getElementsByName("City")[0].value = document.getElementById('zcity').innerHTML;
document.getElementsByName("Country")[0].value = document.getElementById('zcountry').innerHTML;
document.getElementsByName("State")[0].value = document.getElementById('zstate').innerHTML;
document.getElementsByName("Additional Info")[0].value = document.getElementById('zaddi').innerHTML;
document.getElementsByName("Website")[0].value = document.getElementById('zweb').innerHTML;
document.getElementsByName("Skill Set")[0].value = document.getElementById('zindus').innerHTML;
var position = document.getElementById('zposi').innerHTML;
document.getElementById("ZR_Leads_Current Job Title").value = position;
}
window.onload = sendtoZoho;

//var e = document.getElementById("ZR_Leads_Current Job Title");
//var strUser = e.options[e.selectedIndex].text;


</script>
</div>


