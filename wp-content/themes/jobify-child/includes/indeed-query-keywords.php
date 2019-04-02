<?php
// Array of keywords to manipulate the query if the search keyword is from the array.
$keywords = array(
        'architect' => array(
            'or' => 'master,senior,job captain,draftsman,designer,intern,3d,cad,bim,leed',
            'and' => '',
            'not' => 'cloud,computer,core,cyber,data,developer,device,digital,drupal,enterprise,html,ibm,ios,it,java,jquery,json,landscape,logic,mainframe,micro,ms,network,oracle,os,php,rails,sales,scripting,server,software,solutions,sql,storage,system,technology,user,ux,vmware,web'
        ),
        'landscape architect' => array(
            'or' => 'designer,xeriscape designer,horticulture,environmental,3d,cad,landscape architecture',
            'and' => '',
            'not' => 'architectural,cloud,computer,core,cyber,data,developer,device,digital,drupal,enterprise,ibm,ios,it,java,jquery,json,logic,mainframe,micro,ms,network,oracle,os,php,rails,sales,scripting,server,software,solutions,sql,storage,system,user,ux,vmware,web'
        ),
        'interior designer' => array(
            'or' => 'commercial interiors,residential interiors,bim,leed',
            'and' => '',
            'not' => 'kitchen,bath,sales,area sales manager'
        )
    );
?>
