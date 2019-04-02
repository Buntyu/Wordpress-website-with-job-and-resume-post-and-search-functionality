<?php


function arc_get_all_postions_string($pos_ids,$positions_ary){
    $pos_ids_ary = explode(',', $pos_ids);
    $sel_pos = array();
    foreach($pos_ids_ary as $id){
        if(!empty($id)){
            $sel_pos[] = $positions_ary[$id];
        }
    }
    return implode(',', $sel_pos);
}

function arc_get_work_permit_countries($cntry,$arr_countries){
    $sc1=str_replace(",,",",",$cntry);
        $s_countries="";
        if(substr($sc1,0,1)==",") $sc1=substr($sc1,1);
        if(substr($sc1,strlen($sc1)-1,1)==",") $sc1=substr($sc1,0,strlen($sc1)-1);
        $countries=array();
        if($sc1!=''){
          $arr=explode(",",$sc1);
          if($arr) foreach($arr as $key=>$val){
            $countries[$key]=$val;
            $s_countries.=($s_countries==''?'':', ').$arr_countries[$val];
          }
        }
        return $s_countries;
}

function arc_get_industries($industry_ids,$arr_industry){
    $sc1=str_replace(",,",",",$industry_ids);
    $s_industries="";
        if(substr($sc1,0,1)==",") $sc1=substr($sc1,1);
        if(substr($sc1,strlen($sc1)-1,1)==",") $sc1=substr($sc1,0,strlen($sc1)-1);
        $industries=array();
        if($sc1!=''){
          $arr=explode(",",$sc1);
          if($arr) foreach($arr as $key=>$val){
            $industries[$key]=$val;
            $s_industries.=($s_industries==''?'':', ').$arr_industry[$val];
          }
        }
        return $s_industries;
}

function arc_get_project_types($project_ids,$arr_projecttype){
    $sc1=str_replace(",,",",",$project_ids);
        $s_projecttypes="";
        if(substr($sc1,0,1)==",") $sc1=substr($sc1,1);
        if(substr($sc1,strlen($sc1)-1,1)==",") $sc1=substr($sc1,0,strlen($sc1)-1);
        $projecttypes=array();
        if($sc1!=''){
          $arr=explode(",",$sc1);
          if($arr) foreach($arr as $key=>$val){
            $projecttypes[$key]=$val;
            $s_projecttypes.=($s_projecttypes==''?'':', ').$arr_projecttype[$val];
          }
        }
        return $s_projecttypes;
}

function arc_get_roles($role_ids,$arr_projectarea,$arr_role){
    $sc1=$role_ids;
        $s_roles="";
        $rol1="";
        $rol2="";
        if($sc1!=''){
          $arr=explode("|",$sc1);
          if($arr) foreach($arr as $val){
            if($val!=''){
              $arr1=explode(":",$val);
              if($arr1[0]>0 and $arr1[1]>0){
                $rol1[$arr1[0]]=1;
                $rol2[$arr1[0]]=$arr1[1];
                $s_roles.='&bull;&nbsp;'.$arr_projectarea[$arr1[0]].'&nbsp;['.$arr_role[$arr1[1]].']<br>';
              }
            }
          }
        }
        return $s_roles;
}

function arc_get_licenses($ncarb,$licenses,$arr_license){
    $s_licenses="";

        $ncarb=(int)$ncarb;

        if($ncarb) $s_licenses.='&bull;&nbsp;NCARB<br>';

        $lic1="";
        $lic2_1=array();
        $lic2_2=array();
        $lic2_3=array();
        $lic2_4=array();
        $lic2_5=array();
        $lic3="";
        $sc1=$licenses;
        if($sc1!=''){
          $arr=explode("|",$sc1);
          if($arr) foreach($arr as $val){
            if($val!=''){
              $arr1=explode(":",$val);
              if($arr1[0]>0){
                $lic1[$arr1[0]]=1;
                $lic3[$arr1[0]]=$arr1[2];
                $s_licenses.='&bull;&nbsp;'.$arr_license[$arr1[0]];
                $s_11='';
                $arr2=array();
                if($arr1[1]!=''){
                  $arr3=explode(",",$arr1[1]);
                  if($arr3) foreach($arr3 as $key3=>$val3){
                    $arr2[$key3]=$val3;
                    $s_11.=($s_11==''?'':', ').$val3;
                  }
                }
                if($arr1[2]!='') $s_11.=($s_11==''?'':', ').$arr1[2];
                if($s_11!='') $s_licenses.=' ('.$s_11.')';
                $s_licenses.='<br>';

                ${'lic2_'.$arr1[0]}=$arr2;
              }
            }
          }
        }
        return $s_licenses;
}

function arc_create_select($val_ary,$name,$selected=''){
    $select = '<select style="width: 200px;" name="'.$name.'">';
    foreach ($val_ary as $value=>$view){
        $sel = '';
        if($selected == $value){
            $sel = 'selected="selected"';
        }
        $select.= '<option '.$sel.' value="'.$value.'">'.$view.'</option>';
    }
    $select .= '</select>';
    return $select;
}



?>
