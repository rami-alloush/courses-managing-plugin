<?php

$code = get_post_meta($post->ID, 'product_code', true);

if ( empty( $code ) )
{
	$label = "Code Will Be:";
	$product_code = 'PZXXYY';
}
else
{
	$label = "Code:";
	$product_code = get_post_meta($post->ID, 'product_code', true);
}
	
?>
<table> 
    <tr valign="top">
        <th class="metabox_label_column">
            <label for="product_code"><?php echo $label;?></label>
        </th>
        <td>
            <input type="text" id="product_code" readonly="true" name="product_code" value="<?php echo $product_code;?>" />
        </td>
    </tr>
</table>