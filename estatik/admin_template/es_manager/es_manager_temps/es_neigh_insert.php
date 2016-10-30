          
<li id="<?php echo 'neigh_'.$id?>" class="">
    <label>
        <?php echo $es_neigh_title?>
        <?php if(isset($prop_neigh) && $prop_neigh==1) { ?>
            <input type="checkbox" name="es_prop_neigh[]" value="<?php echo $id?>" />
        <?php } ?>
    </label>
    <?php if(isset($prop_neigh) && $prop_neigh==1) { ?>
        <input type="text" name="neigh_distance[]" onfocus="es_neigh_prop_text(this)" value="text/number" onBlur="if(this.value == '') { this.value = 'text/number'; }" />
    <?php } ?>
    <small onclick="es_neigh_delete(this)"></small>
    <span class="es_field_loader es_neigh_loader"></span>
    <input type="hidden" class="es_neigh_id" value="<?php echo $id?>" name="es_neigh_id" />
</li>
 