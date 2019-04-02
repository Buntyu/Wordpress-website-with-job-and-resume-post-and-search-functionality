<table id="arc-portfolio" class="arc-extra-links">
<tbody>
    <tr>
        <td colspan="5" class="color_dark">&nbsp;&nbsp;<b>Portfolio:</b></td>
    </tr>
    <tr>
        <td><b>Date</b></td>
        <td><b>Project Name</b></td>
        <td><b>Link</b></td>
        <td><b>File Url</b></td>
        <td><b>Description</b></td>
    </tr>
    <?php 
        foreach ($user_portfolios as $portfolio) { 
            $linkname = $portfolio->LinkName!=''?$portfolio->LinkName:$portfolio->LinkURL;
            if($portfolio->LinkURL != ''){
                if(strpos($portfolio->LinkURL, 'http') || strpos($portfolio->LinkURL, 'https')){
                    //Do nothing
                }else{
                    $portfolio->LinkURL = 'http://'.$portfolio->LinkURL;
                }
            }
    ?>
<tr>
    <td>
        <?php echo date("m/d/Y",$portfolio->AddedDate); ?>
    </td>
    <td>
        <?= $portfolio->ProjectName; ?>
    </td>
    <td>
        <?php if(!empty($portfolio->LinkURL)){ ?>
        <a target="_blank" href="<?= $portfolio->LinkURL ?>"><?= $linkname ?></a>
        <?php } ?>
    </td>
    <td>
        <?php if(!empty($portfolio->FileURL)){ ?>
        <a target="_blank" href="http://archipro.com/recruit/portfolio/<?= $portfolio->FileURL; ?>"><?= $portfolio->FileURL; ?></a>
        <?php } ?>
    </td>
    <td><?= $portfolio->Description; ?></td>
</tr>
<?php } ?>
    <tr>
        <td colspan="5" class="color_dark"></td>
</tr>
</tbody>
</table>