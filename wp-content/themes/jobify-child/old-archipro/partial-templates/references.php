<table id="arc-references" class="arc-extra-links">
<tbody>
    <tr>
        <td colspan="6" class="color_dark">&nbsp;&nbsp;<b>References:</b></td>
    </tr>
    <tr>
        <td><b>Date</b></td>
        <td><b>Company Name</b></td>
        <td><b>Contact Name</b></td>
        <td><b>Contact Position</b></td>
        <td><b>Contact Phone</b></td>
        <td><b>View</b></td>
    </tr>
    <?php foreach ($user_refs as $index=>$ref) { ?>
    <tr>
    <td>
        <?php echo date("m/d/Y",$ref->AddedDate); ?>
    </td>
    <td><?= $ref->CompanyName; ?></td>
    <td><?= $ref->ContactName; ?></td>
    <td><?= $ref->ContactPosition; ?></td>
    <td><?= $ref->ContactPhone; ?></td>
    <td>
        <div><a class="arc-ref-view-link" href="#arc-ref-view-<?= $index; ?>">View</a></div>
    </td>
</tr>
    <?php } ?>
    <tr>
            <td colspan="6" class="color_dark"></td>
    </tr>
</tbody>
</table>

<?php
foreach ($user_refs as $index=>$ref) {
  $s="";

  $addedby="????";
  $addeddate="??/??/????";
  $lastby="????";
  $lastdate="??/??/????";
  $msg="";
  $noer=1;
  $dt=time();





  // *********** EXISTING RECORD: filling values to form **************

      $f= $ref;
      $companyname=$f->CompanyName;
      $contactname=$f->ContactName;
      $contactposition=$f->ContactPosition;
      $contactphone=$f->ContactPhone;
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



      $fld=$arr_reffields;



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

  // Generate and display screen -------------
  $s.='<h3>Reference Details</h3>';
  $s.='
<table width="600" border=0 cellspacing=0 cellpadding=2 class="formtable">
<tr>
<td colspan=2 class="color_bar" align="right">
<div><span class="s1">';

  $s.='<tr>';
  $s.='<td colspan="2" align="left" valign="top" class="td_borderbottom"><span class=t2>&nbsp;<b>Reference Contacts</b>&nbsp;</span></td>';
  $s.='</tr>';
  $s.='<tr>';
  $s.='<td width="50%" align="right" valign="middle" class=c1><b>Company:</b>&nbsp;</td>';
  $s.='<td width="50%" align="left"  valign="middle" class=t1>'.htmlspecialchars($companyname).'</td>';
  $s.='</tr>';
  $s.='<tr>';
  $s.='<td width="50%" align="right" valign="middle" class=c1><b>Contact Name:</b>&nbsp;</td>';
  $s.='<td width="50%" align="left"  valign="middle" class=t1>'.htmlspecialchars($contactname).'</td>';
  $s.='</tr>';
  $s.='<tr>';
  $s.='<td width="50%" align="right" valign="middle" class=c1><b>Contact Position:</b>&nbsp;</td>';
  $s.='<td width="50%" align="left"  valign="middle" class=t1>'.htmlspecialchars($contactposition).'</td>';
  $s.='</tr>';
  $s.='<tr>';
  $s.='<td width="50%" align="right" valign="middle" class=c1><b>Contact Phone:</b>&nbsp;</td>';
  $s.='<td width="50%" align="left"  valign="middle" class=t1>'.htmlspecialchars($contactphone).'</td>';
  $s.='</tr>';
  $s.='<tr style="height:10px;"></tr>';
  $s.='<tr>';
  $s.='<td colspan="2" align="left" valign="top" class="td_borderbottom"><span class=t2>&nbsp;<b>Phone Interview</b>&nbsp;</span></td>';
  $s.='</tr>';
  $s.='<tr style="height:10px;"></tr>';
  // loop: for each field in this interview
  $fld=$arr_reffields;



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

$s.='</table>';
  $content=$s;
    echo '<div style="display:none"><div class="arc-ref-wrapper" id="arc-ref-view-'.$index.'">'.$content.'</div></div>';
} 
?>