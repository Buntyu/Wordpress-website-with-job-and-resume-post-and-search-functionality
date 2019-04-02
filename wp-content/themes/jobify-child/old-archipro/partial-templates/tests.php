<table id="arc-tests" class="arc-extra-links">
    <tbody>
    <tr>
           <td colspan="4" class="color_dark">&nbsp;&nbsp;<b>Tests:</b></td>
    </tr>
    <tr>
        <td><b>Added On</b></td>
        <td><b>Interview Name</b></td>
        <td><b>Added by</b></td>
        <td><b>View</b></td>
    </tr>
    <?php 
        foreach ($user_tests as $index=>$test) {
    ?>
    <tr>
        <td>
            <?php echo date("m/d/Y",$test->AddedDate); ?>
        <td><?php echo $arr_interviewname[$test->TestNo]; ?></td>
        <td><?php echo $test->AddedBy; ?></td>
        <td>
            <div><a class="arc-test-view-link" href="#arc-test-view-<?= $index; ?>">View Test</a></div>
        </td>
    </tr>
    <?php } ?>
    <tr>
            <td colspan="4" class="color_dark"></td>
    </tr>
    </tbody>
</table>



<?php
foreach ($user_tests as $index => $user_test) {
//$user_test = $user_tests[0];
  $s="";


  $testno=(int)$user_test->TestNo;
  if($testno>0){
    if($arr_interviewname[$testno]=='') $testno=0;
  }
  // *********** EXISTING RECORD: reading values **************
  $addedby="????";

  $addeddate="??/??/????";

  $lastby="????";

  $lastdate="??/??/????";

  $id=0;
  $msg="";

  $noer=1;

  $dt=time();


  // *********** EXISTING RECORD: filling values to form **************

  if($testno){

      $f= $user_test;

      $addedby=$f->AddedBy;
      $addeddate=$f->AddedDate?date("m/d/Y h:i a",$f->AddedDate):$addeddate;

      $lastby=$f->LastUpdBy;

      $lastdate=$f->LastUpdTime?date("m/d/Y h:i a",$f->LastUpdTime):$lastdate;

      $arr=explode("|",$f->Description);

      $arr_fval="";

      if($arr) foreach($arr as $key=>$val){

        $arr1=explode('{*}',$val);

        if($arr1[0]>0) $arr_fval[$arr1[0]]=$arr1[1];

      }



      $fld=$arr_interfields[$testno];



      if($fld) foreach($fld as $fieldindex=>$fielddefs){

        $arr=explode("|",$fielddefs);

        $required=(int)$arr[0];

        $fieldtype=(int)$arr[1];

        $params=$arr[2];

        $fieldname=$arr[3];



        switch($fieldtype){

          case 1:  // text area

            ${'field'.$fieldindex}=$arr_fval[$fieldindex];

          break;

          case 2:  // drop down box

            ${'field'.$fieldindex}=(int)$arr_fval[$fieldindex];

          break;

          case 3:  // text box

            ${'field'.$fieldindex}=$arr_fval[$fieldindex];

          break;

          case 4:  // drop downs group

            $arr_params=explode("^",$params);

            $arr_opt=explode("~",$arr_params[1]);

            $arr_opt2=explode("~",$arr_fval[$fieldindex]);

            $sx='';

            if($arr_opt) foreach($arr_opt as $key=>$val){

              ${'field'.$fieldindex.'_'.($key+1)}=$arr_opt2[$key];

            }

          break;

          case 5:  // radio group

            ${'field'.$fieldindex}=(int)$arr_fval[$fieldindex];

          break;

          case 6:  // label

          break;

        }

      }

  }

  // Generate and display screen -------------

  $s.='<h2>'.trim($first.' '.$last).'</h2>'."\r\n";


  // Test/Interview add/edit form
  $s.='<h3>'.$arr_interviewname[$testno].'</h3>';
  //$s.='<p class="t1"><b><i>'.$arr_interviewintro[$testno].'</i></b></p>';

  $s.=($msg?('<div class="err">'.$msg.'</div><br>'):'');

  $s.='





<table width="600" border=0 cellspacing=0 cellpadding=2 class="formtable">

<tr>

<td colspan=2 class="color_bar" align="right">

<div><span class="s1">';


if($id){
  $s.='Record created by <b>'.$addedby.'</b> on:&nbsp;<b>'.$addeddate.'</b><br>
Last updated by <b>'.$lastby.'</b> on:&nbsp;<b>'.$lastdate.'</b>';
}else{
  $s.='&nbsp;<br>&nbsp;';
}
  $s.='</span></div></td>
</tr>';


if($testno!=4){

  // loop: for each field in this interview
  $fld=$arr_interfields[$testno];

  if($fld) foreach($fld as $fieldindex=>$fielddefs){

    $arr=explode("|",$fielddefs);
    $required=(int)$arr[0];
    $fieldtype=(int)$arr[1];
    $params=$arr[2];
    $fieldname=$arr[3];

    switch($fieldtype){

      case 1:  // text area
        $arr_opt=explode("~",$params);
        $f_width=$arr_opt[0];
        $f_rows=$arr_opt[1];
        $s.='<tr><td colspan=2 align="center"><table width="'.$f_width.'" border=0 cellspacing=0 cellpadding=2>';
        $s.='<tr><td align="left" valign="middle" class=c1><b>'.$fieldname.'</b>&nbsp;</td></tr>';
        $s.='<tr><td align="left" valign="middle" class=t1><div style="width: '.$f_width.'px;">'.htmlspecialchars(${'field'.$fieldindex}).'</div></td></tr>';
        $s.='</table></td></tr>';
      break;



      case 2:  // drop down
        $arr_opt=explode("~",$params);
        $st1='';
        if($arr_opt) foreach($arr_opt as $key=>$val){
          if((${'field'.$fieldindex})==$key+1) $st1=htmlspecialchars($val);
        }
        $s.='<tr><td colspan=2 align="center"><table width="100%" border=0 cellspacing=0 cellpadding=2><tr>';
        $s.='<td align="right" valign="middle" class=c1><b>'.$fieldname.'</b>&nbsp;</td>';
        $s.='<td align="left" valign="middle" class=t1>'.$st1.'</td>';
        $s.='</tr></table></td></tr>';
      break;



      case 3:  // text box
        $arr_opt=explode("~",$params);
        $f_width=$arr_opt[0];
        $f_max=$arr_opt[1];
        $s.='<tr><td colspan=2 align="center"><table width="100%" border=0 cellspacing=0 cellpadding=2><tr>';
        $s.='<td align="right" valign="top" class=c1><b>'.$fieldname.'</b>&nbsp;</td>';
        $s.='<td align="left" valign="top" class=t1><div style="width:'.$f_width.'px;">'.htmlspecialchars(${'field'.$fieldindex}).'</div></td>';
        $s.='</tr></table></td></tr>';



      break;

      case 4:  // drop down groups
        $arr_params=explode("^",$params);
        $arr_val="";
        $arr_opt=explode("~",$arr_params[0]);
        $arr_opt2=explode("~",$arr_params[1]);
        $s.='<tr><td colspan="2" align="left" valign="middle" class=c1><div style="padding: 5px;"><span class=c1><b>'.$fieldname.'</b>&nbsp;</span></div></td></tr>';
        $s.='<tr><td colspan=2 align="center"><table width="90%" border=0 cellspacing=0 cellpadding=2>';
        if($arr_opt2) foreach($arr_opt2 as $key2=>$val2){
          $st1='';
          if($arr_opt) foreach($arr_opt as $key=>$val){
            if((${'field'.$fieldindex.'_'.($key2+1)})==$key+1) $st1=htmlspecialchars($val);
          }
          $s.='<tr><td align="left" valign="top" class=t1>'.$val2.'&nbsp;</td>';
          $s.='<td align="right" valign="top" class=t1 nowrap>&nbsp;<b>'.$st1.'</b></td></tr>';
        }
        $s.='</table></td></tr>';
      break;
      
      case 5:  // radio groups
        $arr_opt=explode("~",$params);
        $s.='<tr><td colspan="2" align="left" valign="middle" class=c1><div style="padding: 5px;"><span class=c1><b>'.$fieldname.'</b>&nbsp;</span></div></td></tr>';
        $s.='<tr><td colspan=2 align="center"><table width="90%" border=0 cellspacing=0 cellpadding=2>';
        if($arr_opt) foreach($arr_opt as $key2=>$val2){
          $s.='<tr><td align="left" valign="top" class=t1>'.$val2.'&nbsp;</td>';
          $s.='<td align="right" valign="top" nowrap>&nbsp;'.(((${'field'.$fieldindex})==($key2+1))?'[<b>x</b>]':'[&nbsp;&nbsp;]').'</td></tr>';
        }
        $s.='</table></td></tr>';

      break;

      case 6:  // label
        $s.='<tr>';
        $s.='<td colspan="2" align="left" valign="top" class="td_borderbottom"><span class=t2>&nbsp;<b>'.$fieldname.'</b>&nbsp;</span></td>';
        $s.='</tr>';

      break;
    }

  }
}else{  // behavioral interview -> presented differently
  // loop: for each field in this interview

  $fld=$arr_interfields[$testno];

  if($fld) foreach($fld as $fieldindex=>$fielddefs){
      
    $arr=explode("|",$fielddefs);
    $required=(int)$arr[0];
    $fieldtype=(int)$arr[1];
    $params=$arr[2];
    $fieldname=$arr[3];

    switch($fieldtype){

      case 1:  // text area
        $arr_opt=explode("~",$params);
        $f_width=$arr_opt[0];
        $f_rows=$arr_opt[1];

        $s.='<tr>';
        $s.='<td width="50%" align="right" valign="top" class=c1><b>'.$fieldname.'</b>&nbsp;</td>';
        $s.='<td width="50%" align="left"  valign="top" class=t1><div style="width: '.$f_width.'px;">'.htmlspecialchars(${'field'.$fieldindex}).'</div></td>';
        $s.='</tr>';
      break;

      case 2:  // drop down
        $arr_opt=explode("~",$params);
        $st1='';
        if($arr_opt) foreach($arr_opt as $key=>$val){
          if((${'field'.$fieldindex})==$key+1) $st1=htmlspecialchars($val);
        }

        $s.='<tr>';
        $s.='<td width="50%" align="right" valign="top" class=c1><b>'.$fieldname.'</b>&nbsp;</td>';
        $s.='<td width="50%" align="left"  valign="top" class=t1>'.$st1.'</td>';
        $s.='</tr>';

      break;

      case 3:  // text box

        $arr_opt=explode("~",$params);

        $f_width=$arr_opt[0];

        $f_max=$arr_opt[1];

        $s.='<tr>';
        $s.='<td width="50%" align="right" valign="top" class=c1><b>'.$fieldname.'</b>&nbsp;</td>';
        $s.='<td width="50%" align="left"  valign="top" class=t1><div style="width:'.$f_width.'px;">'.htmlspecialchars(${'field'.$fieldindex}).'</div></td>';
        $s.='</tr>';

      break;

      case 6:  // label
        $s.='<tr>';
        $s.='<td colspan="2" align="left" valign="top" class="td_borderbottom"><span class=t2>&nbsp;<b>'.$fieldname.'</b>&nbsp;</span></td>';
        $s.='</tr>';
      break;
    }

  }

}
$s.='</table>';

  $content=$s;
  echo '<div style="display:none"><div class="arc-test-wrapper" id="arc-test-view-'.$index.'">'.$content.'</div></div>';
}
?>