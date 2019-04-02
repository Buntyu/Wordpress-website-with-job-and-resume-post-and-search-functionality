<?php



$select_state='<option value=""> -- Select US State --

<option value="AL">Alabama

<option value="AK">Alaska

<option value="AZ">Arizona

<option value="AR">Arkansas

<option value="CA">California

<option value="CO">Colorado

<option value="CT">Connecticut

<option value="DE">Delaware

<option value="DC">DC

<option value="FL">Florida

<option value="GA">Georgia

<option value="HI">Hawaii

<option value="ID">Idaho

<option value="IL">Illinois

<option value="IN">Indiana

<option value="IA">Iowa

<option value="KS">Kansas

<option value="KY">Kentucky

<option value="LA">Louisiana

<option value="ME">Maine

<option value="MD">Maryland

<option value="MA">Massachusetts

<option value="MI">Michigan

<option value="MN">Minnesota

<option value="MS">Mississippi

<option value="MO">Missouri

<option value="MT">Montana

<option value="NE">Nebraska

<option value="NV">Nevada

<option value="NH">New Hampshire

<option value="NJ">New Jersey

<option value="NM">New Mexico

<option value="NY">New York

<option value="NC">North Carolina

<option value="ND">North Dakota

<option value="OH">Ohio

<option value="OK">Oklahoma

<option value="OR">Oregon

<option value="PA">Pennsylvania

<option value="RI">Rhode Island

<option value="SC">South Carolina

<option value="SD">South Dakota

<option value="TN">Tennessee

<option value="TX">Texas

<option value="UT">Utah

<option value="VT">Vermont

<option value="VA">Virginia

<option value="WA">Washington

<option value="WV">West Virginia

<option value="WI">Wisconsin

<option value="WY">Wyoming



<option value="">-- Select Canada Province --

<option value="ALB">Alberta

<option value="BRC">British Columbia

<option value="PIE">Prince Edward Island

<option value="MAN">Manitoba

<option value="NEW">New Brunswick

<option value="NOV">Nova Scotia

<option value="ONT">Ontario

<option value="QUE">Quebec

<option value="SAS">Saskatchewan

<option value="LAB">Newfoundland/Labrador

<option value="NWT">Northwest Territories

<option value="YUK">Yukon Territory



<option value="-">-- OTHER - Non US/Canada --';



// Array


$arr_states[""] = "Any";
$arr_states["AL"]="Alabama";

$arr_states["AK"]="Alaska";

$arr_states["AZ"]="Arizona";

$arr_states["AR"]="Arkansas";

$arr_states["CA"]="California";

$arr_states["CO"]="Colorado";

$arr_states["CT"]="Connecticut";

$arr_states["DE"]="Delaware";

$arr_states["DC"]="DC";

$arr_states["FL"]="Florida";

$arr_states["GA"]="Georgia";

$arr_states["HI"]="Hawaii";

$arr_states["ID"]="Idaho";

$arr_states["IL"]="Illinois";

$arr_states["IN"]="Indiana";

$arr_states["IA"]="Iowa";

$arr_states["KS"]="Kansas";

$arr_states["KY"]="Kentucky";

$arr_states["LA"]="Louisiana";

$arr_states["ME"]="Maine";

$arr_states["MD"]="Maryland";

$arr_states["MA"]="Massachusetts";

$arr_states["MI"]="Michigan";

$arr_states["MN"]="Minnesota";

$arr_states["MS"]="Mississippi";

$arr_states["MO"]="Missouri";

$arr_states["MT"]="Montana";

$arr_states["NE"]="Nebraska";

$arr_states["NV"]="Nevada";

$arr_states["NH"]="New Hampshire";

$arr_states["NJ"]="New Jersey";

$arr_states["NM"]="New Mexico";

$arr_states["NY"]="New York";

$arr_states["NC"]="North Carolina";

$arr_states["ND"]="North Dakota";

$arr_states["OH"]="Ohio";

$arr_states["OK"]="Oklahoma";

$arr_states["OR"]="Oregon";

$arr_states["PA"]="Pennsylvania";

$arr_states["RI"]="Rhode Island";

$arr_states["SC"]="South Carolina";

$arr_states["SD"]="South Dakota";

$arr_states["TN"]="Tennessee";

$arr_states["TX"]="Texas";

$arr_states["UT"]="Utah";

$arr_states["VT"]="Vermont";

$arr_states["VA"]="Virginia";

$arr_states["WA"]="Washington";

$arr_states["WV"]="West Virginia";

$arr_states["WI"]="Wisconsin";

$arr_states["WY"]="Wyoming";



$arr_states["ALB"]="Alberta";

$arr_states["BRC"]="British Columbia";

$arr_states["PIE"]="Prince Edward Island";

$arr_states["MAN"]="Manitoba";

$arr_states["NEW"]="New Brunswick";

$arr_states["NOV"]="Nova Scotia";

$arr_states["ONT"]="Ontario";

$arr_states["QUE"]="Quebec";

$arr_states["SAS"]="Saskatchewan";

$arr_states["LAB"]="Newfoundland/Labrador";

$arr_states["NWT"]="Northwest Territories";

$arr_states["YUK"]="Yukon Territory";



//$arr_states["-"]=" -- Non US/Canada --";





$select_country='



<option value="US">UNITED STATES</option>

<option value="AS">American Samoa</option>

<option value="AD">Andorra</option>

<option value="AG">Antigua and Barbuda</option>



<option value="AR">Argentina</option>

<option value="AM">Armenia</option>

<option value="AW">Aruba</option>

<option value="AU">Australia</option>

<option value="AT">Austria</option>

<option value="AZ">Azerbaijan</option>

<option value="BS">Bahamas</option>

<option value="BH">Bahrain</option>

<option value="BD">Bangladesh</option>



<option value="BB">Barbados</option>

<option value="BY">Belarus</option>

<option value="BE">Belgium</option>

<option value="BZ">Belize</option>

<option value="BM">Bermuda</option>

<option value="BO">Bolivia</option>

<option value="BW">Botswana</option>

<option value="BR">Brazil</option>

<option value="BN">Brunei Darussalam</option>



<option value="BG">Bulgaria</option>

<option value="CA">Canada</option>

<option value="KY">Cayman Islands</option>

<option value="CL">Chile</option>

<option value="CN">China</option>

<option value="CO">Colombia</option>

<option value="CK">Cook Islands</option>

<option value="CR">Costa Rica</option>

<option value="HR">Croatia</option>



<option value="CY">Cyprus</option>

<option value="CZ">Czech Republic</option>

<option value="DK">Denmark</option>

<option value="DM">Dominica</option>

<option value="DO">Dominican Republic</option>

<option value="TP">East Timor</option>

<option value="EC">Ecuador</option>

<option value="EG">Egypt</option>

<option value="SV">El Salvador</option>



<option value="EE">Estonia</option>

<option value="FJ">Fiji</option>

<option value="FI">Finland</option>

<option value="FR">France</option>

<option value="GF">French Guiana</option>

<option value="PF">French Polynesia</option>

<option value="GM">Gambia</option>

<option value="GE">Georgia</option>

<option value="DE">Germany</option>



<option value="GH">Ghana</option>

<option value="GI">Gibraltar</option>

<option value="GR">Greece</option>

<option value="GD">Grenada</option>

<option value="GP">Guadeloupe</option>

<option value="GU">Guam</option>

<option value="GT">Guatemala</option>

<option value="HT">Haiti</option>

<option value="HN">Honduras</option>



<option value="HK">Hong Kong</option>

<option value="HU">Hungary</option>

<option value="IS">Iceland</option>

<option value="IN">India</option>

<option value="ID">Indonesia</option>

<option value="IL">Israel</option>

<option value="IT">Italy</option>

<option value="JM">Jamaica</option>

<option value="JP">Japan</option>



<option value="JO">Jordan</option>

<option value="KZ">Kazakstan</option>

<option value="KE">Kenya</option>

<option value="KW">Kuwait</option>

<option value="LV">Latvia</option>

<option value="LB">Lebanon</option>

<option value="LS">Lesotho</option>

<option value="LT">Lithuania</option>

<option value="LU">Luxembourg</option>



<option value="MO">Macau</option>

<option value="MK">Macedonia</option>

<option value="MY">Malaysia</option>

<option value="MT">Malta</option>

<option value="MQ">Martinique</option>

<option value="MU">Mauritius</option>

<option value="MX">Mexico</option>

<option value="MD">Moldova</option>

<option value="MS">Montserrat</option>



<option value="MA">Morocco</option>

<option value="MZ">Mozambique</option>

<option value="NA">Namibia</option>

<option value="NP">Nepal</option>

<option value="NL">Netherlands</option>

<option value="AN">Netherlands Antilles</option>

<option value="NC">New Caledonia</option>

<option value="NZ">New Zealand</option>

<option value="NI">Nicaragua</option>



<option value="NO">Norway</option>

<option value="OM">Oman</option>

<option value="PK">Pakistan</option>

<option value="PA">Panama</option>

<option value="PG">Papua New Guinea</option>

<option value="PY">Paraguay</option>

<option value="PE">Peru</option>

<option value="PH">Philippines</option>

<option value="PL">Poland</option>



<option value="PT">Portugal</option>

<option value="PR">Puerto Rico</option>

<option value="QA">Qatar</option>

<option value="RE">Reunion</option>

<option value="RO">Romania</option>

<option value="RU">Russia</option>

<option value="KN">Saint Kitts and Nevis</option>

<option value="LC">Saint Lucia</option>

<option value="VC">Saint Vincent and The Grenadines</option>



<option value="ZZ">Saipan</option>

<option value="SA">Saudi Arabia</option>

<option value="SN">Senegal</option>

<option value="CS">Serbia and Montenegro</option>

<option value="SC">Seychelles</option>

<option value="SG">Singapore</option>

<option value="SK">Slovakia</option>

<option value="SI">Slovenia</option>

<option value="SB">Solomon Islands</option>



<option value="ZA">South Africa</option>

<option value="KR">South Korea</option>

<option value="ES">Spain</option>

<option value="LK">Sri Lanka</option>

<option value="SZ">Swaziland</option>

<option value="SE">Sweden</option>

<option value="CH">Switzerland</option>

<option value="TW">Taiwan</option>

<option value="TH">Thailand</option>



<option value="TO">Tonga</option>

<option value="TT">Trinidad and Tobago</option>

<option value="TN">Tunisia</option>

<option value="TR">Turkey</option>

<option value="UG">Uganda</option>

<option value="UA">Ukraine</option>

<option value="AE">United Arab Emirates</option>

<option value="GB">United Kingdom</option>



<option value="UY">Uruguay</option>

<option value="VU">Vanuatu</option>

<option value="VE">Venezuela</option>

<option value="VN">Vietnam</option>

<option value="VG">Virgin Islands, British</option>

<option value="VI">Virgin Islands, U.S.</option>';





// Countries array


$arr_countries[""]="Any";
$arr_countries["US"]="UNITED STATES";

$arr_countries["AS"]="American Samoa";

$arr_countries["AD"]="Andorra";

$arr_countries["AG"]="Antigua and Barbuda";



$arr_countries["AR"]="Argentina";

$arr_countries["AM"]="Armenia";

$arr_countries["AW"]="Aruba";

$arr_countries["AU"]="Australia";

$arr_countries["AT"]="Austria";

$arr_countries["AZ"]="Azerbaijan";

$arr_countries["BS"]="Bahamas";

$arr_countries["BH"]="Bahrain";

$arr_countries["BD"]="Bangladesh";



$arr_countries["BB"]="Barbados";

$arr_countries["BY"]="Belarus";

$arr_countries["BE"]="Belgium";

$arr_countries["BZ"]="Belize";

$arr_countries["BM"]="Bermuda";

$arr_countries["BO"]="Bolivia";

$arr_countries["BW"]="Botswana";

$arr_countries["BR"]="Brazil";

$arr_countries["BN"]="Brunei Darussalam";



$arr_countries["BG"]="Bulgaria";

$arr_countries["CA"]="Canada";

$arr_countries["KY"]="Cayman Islands";

$arr_countries["CL"]="Chile";

$arr_countries["CN"]="China";

$arr_countries["CO"]="Colombia";

$arr_countries["CK"]="Cook Islands";

$arr_countries["CR"]="Costa Rica";

$arr_countries["HR"]="Croatia";



$arr_countries["CY"]="Cyprus";

$arr_countries["CZ"]="Czech Republic";

$arr_countries["DK"]="Denmark";

$arr_countries["DM"]="Dominica";

$arr_countries["DO"]="Dominican Republic";

$arr_countries["TP"]="East Timor";

$arr_countries["EC"]="Ecuador";

$arr_countries["EG"]="Egypt";

$arr_countries["SV"]="El Salvador";



$arr_countries["EE"]="Estonia";

$arr_countries["FJ"]="Fiji";

$arr_countries["FI"]="Finland";

$arr_countries["FR"]="France";

$arr_countries["GF"]="French Guiana";

$arr_countries["PF"]="French Polynesia";

$arr_countries["GM"]="Gambia";

$arr_countries["GE"]="Georgia";

$arr_countries["DE"]="Germany";



$arr_countries["GH"]="Ghana";

$arr_countries["GI"]="Gibraltar";

$arr_countries["GR"]="Greece";

$arr_countries["GD"]="Grenada";

$arr_countries["GP"]="Guadeloupe";

$arr_countries["GU"]="Guam";

$arr_countries["GT"]="Guatemala";

$arr_countries["HT"]="Haiti";

$arr_countries["HN"]="Honduras";



$arr_countries["HK"]="Hong Kong";

$arr_countries["HU"]="Hungary";

$arr_countries["IS"]="Iceland";

$arr_countries["IN"]="India";

$arr_countries["ID"]="Indonesia";

$arr_countries["IL"]="Israel";

$arr_countries["IT"]="Italy";

$arr_countries["JM"]="Jamaica";

$arr_countries["JP"]="Japan";



$arr_countries["JO"]="Jordan";

$arr_countries["KZ"]="Kazakstan";

$arr_countries["KE"]="Kenya";

$arr_countries["KW"]="Kuwait";

$arr_countries["LV"]="Latvia";

$arr_countries["LB"]="Lebanon";

$arr_countries["LS"]="Lesotho";

$arr_countries["LT"]="Lithuania";

$arr_countries["LU"]="Luxembourg";



$arr_countries["MO"]="Macau";

$arr_countries["MK"]="Macedonia";

$arr_countries["MY"]="Malaysia";

$arr_countries["MT"]="Malta";

$arr_countries["MQ"]="Martinique";

$arr_countries["MU"]="Mauritius";

$arr_countries["MX"]="Mexico";

$arr_countries["MD"]="Moldova";

$arr_countries["MS"]="Montserrat";



$arr_countries["MA"]="Morocco";

$arr_countries["MZ"]="Mozambique";

$arr_countries["NA"]="Namibia";

$arr_countries["NP"]="Nepal";

$arr_countries["NL"]="Netherlands";

$arr_countries["AN"]="Netherlands Antilles";

$arr_countries["NC"]="New Caledonia";

$arr_countries["NZ"]="New Zealand";

$arr_countries["NI"]="Nicaragua";



$arr_countries["NO"]="Norway";

$arr_countries["OM"]="Oman";

$arr_countries["PK"]="Pakistan";

$arr_countries["PA"]="Panama";

$arr_countries["PG"]="Papua New Guinea";

$arr_countries["PY"]="Paraguay";

$arr_countries["PE"]="Peru";

$arr_countries["PH"]="Philippines";

$arr_countries["PL"]="Poland";



$arr_countries["PT"]="Portugal";

$arr_countries["PR"]="Puerto Rico";

$arr_countries["QA"]="Qatar";

$arr_countries["RE"]="Reunion";

$arr_countries["RO"]="Romania";

$arr_countries["RU"]="Russia";

$arr_countries["KN"]="Saint Kitts and Nevis";

$arr_countries["LC"]="Saint Lucia";

$arr_countries["VC"]="Saint Vincent and The Grenadines";



$arr_countries["ZZ"]="Saipan";

$arr_countries["SA"]="Saudi Arabia";

$arr_countries["SN"]="Senegal";

$arr_countries["CS"]="Serbia and Montenegro";

$arr_countries["SC"]="Seychelles";

$arr_countries["SG"]="Singapore";

$arr_countries["SK"]="Slovakia";

$arr_countries["SI"]="Slovenia";

$arr_countries["SB"]="Solomon Islands";



$arr_countries["ZA"]="South Africa";

$arr_countries["KR"]="South Korea";

$arr_countries["ES"]="Spain";

$arr_countries["LK"]="Sri Lanka";

$arr_countries["SZ"]="Swaziland";

$arr_countries["SE"]="Sweden";

$arr_countries["CH"]="Switzerland";

$arr_countries["TW"]="Taiwan";

$arr_countries["TH"]="Thailand";



$arr_countries["TO"]="Tonga";

$arr_countries["TT"]="Trinidad and Tobago";

$arr_countries["TN"]="Tunisia";

$arr_countries["TR"]="Turkey";

$arr_countries["UG"]="Uganda";

$arr_countries["UA"]="Ukraine";

$arr_countries["AE"]="United Arab Emirates";

$arr_countries["GB"]="United Kingdom";



$arr_countries["UY"]="Uruguay";

$arr_countries["VU"]="Vanuatu";

$arr_countries["VE"]="Venezuela";

$arr_countries["VN"]="Vietnam";

$arr_countries["VG"]="Virgin Islands, British";

$arr_countries["VI"]="Virgin Islands, U.S.";



?>