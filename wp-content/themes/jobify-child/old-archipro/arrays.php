<?php



  // Types of user



  $arr_tus[''] ='';

  $arr_tus['S']='iSatisfy';

  $arr_tus['A']='Superadmin';

  $arr_tus['D']='Administrator';

  $arr_tus['C']='Candidate';

  $arr_tus['E']='Employer';



  // CMS Version



  $version_cms = "5.2";



  // Page Types



  $arr_pagetype[0]='Simple Page';

  $arr_pagetype[1]='FAQ';

  $arr_pagetype[2]='Articles';

  $arr_pagetype[3]='Links';

  $arr_pagetype[4]='Gallery';

  $arr_pagetype[6]='Events';

  $arr_pagetype[5]='Form';

  $arr_pagetype[7]='Table';

  $arr_pagetype[8]='List';



  // Form Field Types



  $arr_fieldtype[0]='Text';

  $arr_fieldtype[1]='Text Area';

  $arr_fieldtype[2]='Check Box';

  $arr_fieldtype[3]='Drop Down Box';

  $arr_fieldtype[4]='Radio Buttons';

  $arr_fieldtype[6]='Checkbox array';





  // Archipro specific arrays ======================



  // Candidate record status



  $arr_cstatus[0]='New';

  $arr_cstatus[1]='Reviewed/Approved';

  $arr_cstatus[2]='Internal Only';

  $arr_cstatus[3]='Reviewed/Rejected';

  $arr_cstatus[4]='Inactive';





  // Where did you hear about Archipro



  $arr_wherehear[1]='Career Fair';

  $arr_wherehear[2]='College';

  $arr_wherehear[3]='Friend/Co-worker';

  $arr_wherehear[4]='Magazine Ad';

  $arr_wherehear[5]='Newspaper Ad';

  $arr_wherehear[6]='Search Engine';

  $arr_wherehear[7]='Yellow Pages';

  $arr_wherehear[8]='Web Site';

  $arr_wherehear[9]='Other';





  // Candidate interested in position



  $arr_positiontype[1]='Either';

  $arr_positiontype[2]='Permanent';

  $arr_positiontype[3]='Contractor';





  // Years experience



  $arr_expyears[1]='Less than 3 years';

  $arr_expyears[2]='3 to 6 years';

  $arr_expyears[3]='6 to 8 years';

  $arr_expyears[4]='8 to 10 years';

  $arr_expyears[5]='More than 10 years';





  // Salary desired



  $arr_salaryrate[5]='5';

  $arr_salaryrate[10]='10';

  $arr_salaryrate[15]='15';

  $arr_salaryrate[20]='20';

  $arr_salaryrate[25]='25';

  $arr_salaryrate[30]='30';

  $arr_salaryrate[35]='35';

  $arr_salaryrate[40]='40';

  $arr_salaryrate[45]='45';

  $arr_salaryrate[50]='50';

  $arr_salaryrate[55]='55';

  $arr_salaryrate[60]='60';

  $arr_salaryrate[65]='65';

  $arr_salaryrate[70]='70';

  $arr_salaryrate[75]='75';

  $arr_salaryrate[80]='80';

  $arr_salaryrate[85]='85';

  $arr_salaryrate[90]='90';

  $arr_salaryrate[100]='100';

  $arr_salaryrate[110]='110';

  $arr_salaryrate[120]='120';

  $arr_salaryrate[130]='130';

  $arr_salaryrate[140]='140';

  $arr_salaryrate[150]='150';

  $arr_salaryrate[160]='160';

  $arr_salaryrate[170]='170';

  $arr_salaryrate[180]='180';

  $arr_salaryrate[190]='190';

  $arr_salaryrate[200]='200';





  // Pay based on



  $arr_salarybase[0]='Yearly';

  $arr_salarybase[1]='Hourly';





  // Willing to relocate



  $arr_relocate[0]='No Relocation';

  $arr_relocate[1]='Within State/Province';

  $arr_relocate[2]='Within Country';

  $arr_relocate[3]='World';





  // Industry


  $arr_industry[0]='Any';

  $arr_industry[1]='Architecture';

  $arr_industry[2]='Appraisal';

  $arr_industry[3]='Construction Management';

  $arr_industry[4]='Civil Engineering';

  $arr_industry[5]='Commercial';

  $arr_industry[6]='Concept/Theme Design';

  $arr_industry[7]='Construction Estimating';

  $arr_industry[8]='Custom Residential';

  $arr_industry[9]='Design Build';

  $arr_industry[10]='Development';

  $arr_industry[11]='Drafting/Production';

  $arr_industry[12]='Education';

  $arr_industry[13]='Entertainment Facilities';

  $arr_industry[14]='Environmental Design';

  $arr_industry[15]='Event Planing';

  $arr_industry[16]='Fabric, Finishes, Equipment';

  $arr_industry[17]='Facilities Management';

  $arr_industry[18]='Furniture';

  $arr_industry[19]='Green Design';

  $arr_industry[20]='Healthcare';

  $arr_industry[21]='High Rise';

  $arr_industry[22]='Historic Preservation';

  $arr_industry[23]='Hospitality';

  $arr_industry[24]='Interior Design';

  $arr_industry[25]='Leasing';

  $arr_industry[26]='Landscape Design';

  $arr_industry[27]='Legal/Finance';

  $arr_industry[28]='Marketing/Sales';

  $arr_industry[29]='Mechanical/Electrical';

  $arr_industry[30]='Multi-Family Residential';

  $arr_industry[31]='Prefab/Modular';

  $arr_industry[32]='Property Management';

  $arr_industry[33]='Real Estate Development';

  $arr_industry[34]='Rendering Artists';

  $arr_industry[35]='Retail Display';

  $arr_industry[36]='Senior Living Facilities';

  $arr_industry[37]='Set Design';

  $arr_industry[38]='Single Family Residential';

  $arr_industry[39]='Space Planning';

  $arr_industry[40]='Structural Engineering';

  $arr_industry[41]='Town Planning';

  $arr_industry[42]='Transportation';

  $arr_industry[43]='Urban Planning';

  $arr_industry[44]='Yacht Design';





  // Project type


  

  //$arr_position[0] ='Select One';

  $arr_projecttype[0]='Any';

  $arr_projecttype[1]='Aviation';

  $arr_projecttype[2]='Commercial';

  $arr_projecttype[3]='Corrections';

  $arr_projecttype[4]='Cruise Line';

  $arr_projecttype[5]='Custom Residential';

  $arr_projecttype[6]='Design-Build';

  $arr_projecttype[7]='Industrial';

  $arr_projecttype[8]='Healthcare';

  $arr_projecttype[9]='High Rise';

  $arr_projecttype[10]='Higher Education';

  $arr_projecttype[11]='Hospitality';

  $arr_projecttype[12]='K-12 Education';

  $arr_projecttype[13]='Military';

  $arr_projecttype[14]='Multi-family Residential';

  $arr_projecttype[15]='Municipal';

  $arr_projecttype[16]='Retail';

  $arr_projecttype[17]='Restaurant';

  $arr_projecttype[18]='Single-family Residential';

  $arr_projecttype[19]='Sports Entertainment';





  // Candidate Project-Areas Roles



  $arr_role[1] ='CADD Technician';

  $arr_role[2] ='Construction Administrator';

  $arr_role[3] ='Engineer';

  $arr_role[4] ='Executive/Principal';

  $arr_role[5] ='Job Captain';

  $arr_role[6] ='Intern';

  $arr_role[7] ='Interior Designer';

  $arr_role[8] ='Landscape Architecture';

  $arr_role[9] ='Planning';

  $arr_role[10]='Project Architect';

  $arr_role[11]='Project Manager';





  // Project areas



  $arr_projectarea[1]='K-12 Education';

  $arr_projectarea[2]='Industrial';

  $arr_projectarea[3]='Higher Education';

  $arr_projectarea[4]='Commercial';

  $arr_projectarea[5]='Aviation';

  $arr_projectarea[6]='High Rise';

  $arr_projectarea[7]='Health Care';

  $arr_projectarea[8]='Single-family Residential';

  $arr_projectarea[9]='Municipal';

  $arr_projectarea[10]='Multi-family Residential';

  $arr_projectarea[11]='Correction';

  $arr_projectarea[12]='Sports Entertainment';

  $arr_projectarea[13]='Military';

  $arr_projectarea[14]='Retail';

  $arr_projectarea[15]='Design-Build';

  $arr_projectarea[16]='Hospitality';

  $arr_projectarea[17]='Custom Residential';

  $arr_projectarea[18]='Cruiseline';





  // Candidate current or most recent Positions


  //$arr_position[0] ='Any';
  $arr_position[0] ='Select One';
  
  $arr_position[1] ='Academic Faculty';

  $arr_position[2] ='Accounting/Finance';

  $arr_position[3] ='Administrative Support';

  $arr_position[4] ='Architect';

  $arr_position[5] ='Architectural Intern';

  $arr_position[6] ='Computer Aided Design';

  $arr_position[7] ='Concept Theme Design';

  $arr_position[8] ='Construction Management';

  $arr_position[9] ='Design Architect';

  $arr_position[10] ='Engineering';

  $arr_position[11] ='Facilities Management';

  $arr_position[12] ='Graphic Design';

  $arr_position[13] ='Healthcare Architect';

  $arr_position[14] ='Industrial Design';

  //$arr_position[15] ='Information Technology (IT)';

  $arr_position[16] ='Interior Designer';

  $arr_position[17] ='Interior Architect';

  $arr_position[18] ='Job Captain';

  $arr_position[19] ='Landscape Architect';

  $arr_position[20] ='Library Services/Knowledge Management';

  $arr_position[21] ='Marketing';

  $arr_position[22] ='Multi Media Rendering';

  $arr_position[23] ='Owners Representative';

  $arr_position[24] ='Planner';

  $arr_position[25] ='Project Manager';

  $arr_position[26] ='Project Architect';

  $arr_position[30] ='Sales Representative';

  $arr_position[27] ='Security Design';

  $arr_position[28] ='Specification';

  //$arr_position[29] ='Web Design';



  //$arr_position[1] ='Accountant';

  //$arr_position[2] ='Agent/Broker';

  //$arr_position[3] ='Architect';

  //$arr_position[4] ='CADD Technician';

  //$arr_position[5] ='Computer Imaging Specialist';

  //$arr_position[6] ='Construction Administrator';

  //$arr_position[7] ='Engineer';

  //$arr_position[8] ='Executive/Principal';

  //$arr_position[9] ='Graphics Designer';

  //$arr_position[10]='HR Manager';

  //$arr_position[11]='Intern';

  //$arr_position[12]='Interior Designer';

  //$arr_position[13]='IT Specialist';

  //$arr_position[14]='Job Captain';

  //$arr_position[15]='Landscape Architect';

  //$arr_position[16]='Lawyer';

  //$arr_position[17]='Marketing Specialist';

  //$arr_position[18]='Model Maker';

  //$arr_position[19]='Office Manager';

  //$arr_position[20]='Owners Representative';

  //$arr_position[21]='Planner';

  //$arr_position[22]='Project Architect';

  //$arr_position[23]='Project Manager';

  //$arr_position[24]='Sales Person';

  //$arr_position[25]='Specification Writer';

  //$arr_position[26]='Superintendent';





  // Candidate current or most recent Salary



  $arr_salary[1] ='Under $25,000';

  $arr_salary[2] ='$25,000 - $30,000';

  $arr_salary[3] ='$30,000 - $35,000';

  $arr_salary[4] ='$35,000 - $40,000';

  $arr_salary[5] ='$40,000 - $45,000';

  $arr_salary[6] ='$45,000 - $50,000';

  $arr_salary[7] ='$50,000 - $55,000';

  $arr_salary[8] ='$55,000 - $60,000';

  $arr_salary[9] ='$60,000 - $65,000';

  $arr_salary[10]='$65,000 - $70,000';

  $arr_salary[11]='$70,000 - $75,000';

  $arr_salary[12]='$75,000 - $80,000';

  $arr_salary[13]='$85,000 - $90,000';

  $arr_salary[14]='$90,000 - $95,000';

  $arr_salary[15]='$95,000 - $100,000';

  $arr_salary[16]='$100,000 and over';





  // Candidate highest degree



  $arr_degree[1]='High School';

  $arr_degree[2]='Some College';

  $arr_degree[3]='Technical Degree';

  $arr_degree[4]='Bachelor\'s Degree';

  $arr_degree[5]='Master\'s Degree';

  $arr_degree[6]='Ph.D.';





  // Candidate degree major



  $arr_degreemajor[0]='n/a';

  $arr_degreemajor[1]='Architecture';

  $arr_degreemajor[2]='Accounting';

  $arr_degreemajor[3]='Business Administration';

  $arr_degreemajor[4]='Constructioin';

  $arr_degreemajor[5]='Engineering';

  $arr_degreemajor[6]='Interior Design';

  $arr_degreemajor[7]='Landscape Architecture';

  $arr_degreemajor[8]='Law';

  $arr_degreemajor[9]='Planning';

  $arr_degreemajor[10]='Marketing';

  $arr_degreemajor[11]='Real Estate Development';

  $arr_degreemajor[12]='Other';





  // Candidate licenses



  $arr_license[1]='Architectural';

  $arr_license[2]='Interior Design';

  $arr_license[3]='Landscape Architect';

  $arr_license[4]='General Contractor';

  $arr_license[5]='Professional Engineer';





  // Sex



  $arr_sex[0]='No Answer';

  $arr_sex[1]='Male';

  $arr_sex[2]='Female';





  // Ethnic



  $arr_eth[0]='No Answer';

  $arr_eth[1]='African-American';

  $arr_eth[2]='Asian';

  $arr_eth[3]='Caucasian';

  $arr_eth[4]='Non-Black Hispanic';

  $arr_eth[5]='Other';





  // Age



  $arr_age[0]='No Answer';

  $arr_age[1]='18 to 30';

  $arr_age[2]='30 to 40';

  $arr_age[3]='40 to 50';

  $arr_age[4]='50 to 60';

  $arr_age[5]='60+';





  // Distance



  $arr_dist[10]='10 miles';

  $arr_dist[25]='25 miles';

  $arr_dist[50]='50 miles';

  $arr_dist[75]='75 miles';

  $arr_dist[100]='100 miles';



  // Compensation


  $arr_compensation[''] = 'Any';
  $arr_compensation[1]='Candidates With No Compensation Requirements';

  $arr_compensation[2]='Candidates With Hourly Based Pay Requirements';

  $arr_compensation[20]='$20,000 - $25,000 Per Year';

  $arr_compensation[25]='$25,000 - $30,000 Per Year';

  $arr_compensation[30]='$30,000 - $35,000 Per Year';

  $arr_compensation[35]='$35,000 - $40,000 Per Year';

  $arr_compensation[40]='$40,000 - $45,000 Per Year';

  $arr_compensation[45]='$45,000 - $50,000 Per Year';

  $arr_compensation[50]='$50,000 - $55,000 Per Year';

  $arr_compensation[55]='$55,000 - $60,000 Per Year';

  $arr_compensation[60]='$60,000 - $70,000 Per Year';

  $arr_compensation[70]='$70,000 - $80,000 Per Year';

  $arr_compensation[80]='$80,000 - $90,000 Per Year';

  $arr_compensation[90]='$90,000 - $100,000 Per Year';

  $arr_compensation[100]='$100,000 > Per Year';



  // Experience - job listing



  $arr_expyearsjob[1]='1 Year or more';

  $arr_expyearsjob[2]='2 Years or more';

  $arr_expyearsjob[3]='3 Years or more';

  $arr_expyearsjob[4]='4 Years or more';

  $arr_expyearsjob[5]='5 Years or more';

  $arr_expyearsjob[6]='6 Years or more';

  $arr_expyearsjob[7]='7 Years or more';

  $arr_expyearsjob[8]='8 Years or more';



  // Position type - job listing



  $arr_positiontypejob[1]='Full Time';

  $arr_positiontypejob[2]='Part Time';

  $arr_positiontypejob[3]='Contract On-Site';

  $arr_positiontypejob[4]='Contract From Home';



  // Salary rate - job listing



  $arr_salaryratejob[5]='$5,000';

  $arr_salaryratejob[10]='$10,000';

  $arr_salaryratejob[15]='$15,000';

  $arr_salaryratejob[20]='$20,000';

  $arr_salaryratejob[30]='$30,000';

  $arr_salaryratejob[40]='$40,000';

  $arr_salaryratejob[50]='$50,000';

  $arr_salaryratejob[60]='$60,000';

  $arr_salaryratejob[70]='$70,000';

  $arr_salaryratejob[80]='$80,000';

  $arr_salaryratejob[90]='$90,000';

  $arr_salaryratejob[100]='$100,000';

  $arr_salaryratejob[125]='$125,000';

  $arr_salaryratejob[150]='$150,000';

  $arr_salaryratejob[175]='$175,000';

  $arr_salaryratejob[200]='$200,000';

  $arr_salaryratejob[225]='$225,000';

  $arr_salaryratejob[250]='$250,000';

  $arr_salaryratejob[300]='Over $250,000';





  // Notes type

  $arr_notetype[1]='Phone conversation';

  $arr_notetype[2]='Placement notice';

  $arr_notetype[3]='General comment';


// Freshness
  $arr_freshness[0] = 'All';
  $arr_freshness[1] = 'Within the Last Day';
  $arr_freshness[7] = 'Within the Last 7 Days';
  $arr_freshness[30] = 'Within the Last 30 Days';
  $arr_freshness[60] = 'Within the Last 60 Days';
  $arr_freshness[90] = 'Within the Last 90 Days';
  $arr_freshness[180] = 'Within the Last 180 Days';

  $arr_cctype = array('Visa','MasterCard','American Express','Discover');





?>

