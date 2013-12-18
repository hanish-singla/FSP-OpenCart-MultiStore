<?php echo $header; ?>
<div id="content">

    <div class="row">
        <div class="span3 margin-top-10">	
            <?php echo $column_left; ?>
            <?php echo $column_right; ?>			
        </div>
        <div class="span9" style="margin-top: 10px;">
            <?php echo $content_top; ?>
            <?php if(isset($child_cat) && !empty($child_cat)) { ?>
            
        
            <div id="cat_list" class="row" style="display: block;">
                
                <ul id="cat_list_ul" class="hProductItems clearfix">
                    <?php foreach($child_cat as $childcatdata) { ?>
                    <li class="clearfix image-desc">
                        <div class="thumbnail">
                        	<a href="<?php echo $childcatdata['href']; ?>" class="thumb-info">
                            	<img src="<?php echo $childcatdata['thumb']; ?>" alt="<?php echo $childcatdata['name']; ?>" />
                                <span class="image-name"><?php echo $childcatdata['name']; ?></span>
                                <span class="image-descall">
                                	<span class="image-action" style="display:block;">
                                		<?php 
                                    	$desc=html_entity_decode($childcatdata['desc'],ENT_QUOTES,"UTF-8");
                                    
                                    	echo $desc; ?>
                                    </span>
                                </span>
                                <span class="top-count">
                                	<span class="top-count-action">
                                		<?php echo $childcatdata['prod_count']; ?>
                                    </span>
                                </span>
                            </a>
                        </div>
                    
                    </li>
                
                 <?php } ?>
                </ul>
            
            
            </div>
    
               <?php } ?>
            <?php if ($products) { ?>
            <div id="sort" class="clearfix" <?php if(!empty($child_cat)) { ?>style="display: none;" <?php }else { ?> style="display: block;" <?php } ?>>
                
                <div class="sortBy sort inline pull-right">
                    <?php echo $text_sort; ?>
                    <select onchange="location = this.value;" style="width: 148px;margin-bottom: 0;">
                        <?php


                        foreach ($sorts as $key=>$sorts) {
                        if($key<=4)
                        {
                        $sortVal = explode('-',$sorts['value']);
                        $sortStr = $sortVal[0].'&'.$sortVal[1]
                        ?>
                        <?php if ($sorts['value'] == $sort . '-' . $order) { ?>
                        <option value="<?php echo $sortStr; ?>" selected="selected"><?php echo $sorts['text']; ?></option>
                        <?php } else { ?>
                        <option value="<?php echo $sortStr; ?>"><?php echo $sorts['text']; ?></option>
                        <?php } ?>
                        <?php } }?>
                    </select>
                </div>
            </div>



            <div id="tab_list" class="row" style="display:none;">
                <ul class="listProductItems clearfix">
                    <?php foreach ($products as $product) { ?>
                    <li class=" clearfix">
                        <div class="span3">
                            <?php if ($product['thumb']) { ?>
                            <div class="thumbnail">

                                <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" alt="<?php echo $product['name']; ?>" /></a>


                            </div> <?php } ?>
                        </div>
                        <div class="span6">								

                            <div class="thumbSetting">
                                <div class="thumbTitle">
                                    <a href="<?php echo $product['href']; ?>" class="invarseColor">
                                        <?php echo $product['name']; ?>
                                    </a>
                                </div>

                                <div class="thumbPriceRate clearfix">
                                    <?php if ($product['price']) { ?>   
                                    <?php if (!$product['special']) { ?>
                                    <span> <?php echo $product['price']; ?></span>
                                    <?php } else { ?>
                                    <span><span class="strike-through"><?php echo $product['price']; ?></span> <?php echo $product['special']; ?></span>
                                    <?php } ?> 
                                    <?php } ?>
                                    <ul class="rating">
                                        <?php if ($product['rating']) { ?>
                                        <li><img src="catalog/view/theme/shopfine/image/stars-<?php echo $product['rating']; ?>.png" alt="<?php echo $product['reviews']; ?>" /></li>
                                        <?php } else { ?>
                                        <li><img src="catalog/view/theme/shopfine/image/stars-0.png" alt="<?php echo $product['reviews']; ?>" />
                                        </li>
                                        <?php } ?>
                                    </ul>
                                </div>  


                                <div class="thumbDesc">
                                    <p>
                                        <?php echo $product['description']; ?>
                                    </p>
                                </div>

                                <div class="thumbButtons">
                                    <button id="cart" onclick="addToCart('<?php echo $product['product_id']; ?>');" class="btn btn-primary btn-small" data-title="<?php echo $button_cart; ?>" data-placement="top" data-toggle="tooltip">
                                        <i class="icon-shopping-cart"></i>
                                    </button>
                                    <button id="wishlist" onclick="addToWishList('<?php echo $product['product_id']; ?>');" class="btn btn-small" data-title="<?php echo $button_wishlist; ?>" data-placement="top" data-toggle="tooltip">
                                        <i class="icon-heart"></i>
                                    </button>


                                </div>



                            </div>	
                        </div>
                    </li> 
                    <?php } ?>

                </ul>
            </div><!--end row-->


            <div id="tab_grid"  class="row" <?php if(!empty($child_cat)) { ?>style="display: none;" <?php }else { ?> style="display: block;" <?php } ?>>
                <ul class="hProductItems clearfix">
                    <?php foreach ($products as $product) { ?>
                    <li class="span3 clearfix">
                        <div class="thumbnail">
                            <?php if ($product['thumb']) { ?>
                            <a href="<?php echo $product['href']; ?>"><img src="<?php echo $product['thumb']; ?>" title="<?php echo $product['name']; ?>" alt="<?php echo $product['name']; ?>" /></a>
                            <?php } ?>
                        </div>
                        <div class="thumbSetting">
                            <div class="thumbTitle">
                                <a href="<?php echo $product['href']; ?>" class="invarseColor">
                                    <?php echo $product['name']; ?>
                                </a>
                            </div>
                            <?php if ($product['price']) { ?>
                            <div class="thumbPrice">
                                <?php if (!$product['special']) { ?>
                                <span><?php echo $product['price']; ?></span>
                                <?php } else { ?>
                                <span><span class="strike-through"><?php echo $product['price']; ?></span> <?php echo $product['special']; ?></span>
                                <?php } ?>
                            </div>
                            <?php } ?>

                            <div class="thumbButtons">
                                <button onclick="addToCart('<?php echo $product['product_id']; ?>');"  class="btn btn-primary btn-small" data-title="+<?php echo $button_cart; ?>" data-placement="top" data-toggle="tooltip">
                                    <i class="icon-shopping-cart"></i>
                                </button>
                                <button onclick="addToWishList('<?php echo $product['product_id']; ?>');" class="btn btn-small" data-title="+<?php echo $button_wishlist; ?>" data-placement="top" data-toggle="tooltip">
                                    <i class="icon-heart"></i>
                                </button>


                            </div>


                        </div>
                    </li>   <?php } ?>
                </ul>

            </div>

            <!-- Bottom pagination -->

            <div id="pagination" class="pagination pagination-right clearfix" <?php if(!empty($child_cat)) { ?>style="display: none;" <?php }else { ?> style="display: block;" <?php } ?> ><?php echo $pagination; ?></div>
            

            <!-- / -->
            <!--
            
            
              <div class="pagination pagination-right"><?php echo $pagination; ?>
              
              </div>
              <div class="showItem inline pull-left">
                                                                    <?php echo $text_limit; ?>
                                                                     <select onchange="location = this.value;">
                    <?php foreach ($limits as $limits) { ?>
                    <?php if ($limits['value'] == $limit) { ?>
                    <option value="<?php echo $limits['href']; ?>" selected="selected"><?php echo $limits['text']; ?></option>
                    <?php } else { ?>
                    <option value="<?php echo $limits['href']; ?>"><?php echo $limits['text']; ?></option>
                    <?php } ?>
                    <?php } ?>
                  </select>
                                                            </div>
            
            -->                        

            <div class="titleHeader clearfix">
                <br/>
                <h2><?php echo $heading_title; ?></h2>
            </div>
            <?php if ($thumb || $description) { ?>
            <div class="category-info">
                <?php if ($thumb) { ?>
                <div class="thumbnail"><img src="<?php echo $thumb; ?>" alt="<?php echo $heading_title; ?>" /></div>
                <?php } ?>
                <?php if ($description) { ?>
                <p><?php echo $description; ?></p>
                <?php } ?>
            </div>
            <?php } ?>

            <?php } ?>

            <?php if (!$categories && !$products) { ?>
            <br/>
            <div class="alert alert-error">
                <button type="button" class="close" data-dismiss="alert">&times;</button>
                <h4>Oh, We're So Sorry</h4>
                <?php echo $text_empty; ?></div>



            <div class="control-group">
                <div class="controls">


                    <a href="<?php echo $continue; ?>" class="btn btn-primary"><?php echo $button_continue; ?></a>
                </div>


            </div><!--end control-group-->

            <?php } ?>
            <?php echo $content_bottom; ?>
        </div>
        <!--end span9-->
    </div>
</div><!--end row--> 


<?php echo $footer; ?>