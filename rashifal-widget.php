<?php
class Aj_Rashifal_Widget extends WP_Widget
{
  public function __construct() 
  {
    parent::__construct('aj-rashifal',__('&#2310;&#2332;&#2325;&#2379; &#2352;&#2366;&#2358;&#2367;&#2347;&#2354;', 'aj-rashifal'),array( 'description' =>'' ));
    add_action( 'wp_footer',              array($this,'wp_footer'));
  }

  public function widget( $args, $instance )
  {
    $site_url                             = 'http://api.aajako.com/rashifal/?type=json&wp_ver='.AJ_RASHIFAL_VER;
    
    $f                                    = '';
    if(!$instance['ajax_loading']){
      $ch                                 = curl_init();
      curl_setopt($ch, CURLOPT_URL, $site_url);
      curl_setopt($ch, CURLOPT_HEADER, 0);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
      curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'].'#wp_ver='.AJ_RASHIFAL_VER);
      $f                                  = curl_exec($ch);
      curl_close($ch);
      $d                                  = json_decode($f);
      $f                                  = (bool)$f;
    }
    ?>
    <div class="aj-box">
      <?php if($instance['showtitle']){ ?>
      <div class="aj-title"><?php echo $instance['title'];?></div>
      <div class="aj-divider"></div>
      <?php } ?>
      <?php if($f && $d->notice){ ?>
      <div class="aj-notice"><?php echo $d->notice; ?></div>
      <?php } ?>
      <div class="aj-content" style="height:<?php echo is_numeric($instance['height'])?$instance['height'].'px':$instance['height']; ?>">
      <?php if($f && !$instance['ajax_loading']): $j=0; foreach( $d->content as $n=>$c ){ 
        if(!$instance['fullcontent'] )
        {
          if(function_exists('mb_strlen')) 
            $ln                           = mb_strlen($c,'UTF-8');
          else
            $ln                           = strlen($c);
          if($ln>120)
          {
            $sp                           = strpos($c,' ',100);
            $c                            = substr_replace($c,'<span class="aj-show">',$sp,0) . '</span><a class="aj-show-more" href="javascript:void(0);"><span>..&#2309;&#2333; &#2348;&#2338;&#2368;</span><span style="display:none;"> &#x25B2;</span></a>';
          }
        }
         
        ?>
        <div class="aj-c-body"><strong><?php echo $n; ?>:</strong><i style="background-position:0px -<?php echo ($d->stars[$j]-1)*11; ?>px" class="aj-star luck-<?php echo $d->stars[$j]; ?>"></i><br /> <?php echo $c; ?></div>
        <div class="aj-hr"></div>
      <?php ++$j; } else: ?>
        <div class="aj-loader">
          <img src="<?php echo $GLOBALS['_aj_purl']; ?>loader.gif" height="7" width="75" />
        </div>
      <?php endif; ?>
      </div>
      <div class="aj-copy">
        <a class="ap" href="http://aajako.com/">&copy; aajako.com</a>
        <a class="get" href="https://profiles.wordpress.org/aajako">get this widget</a>
        <div class="clear"></div>
      </div>
      <div class="aj-divider btm"></div>
    </div>
    <?php
  }
   
  public function update( $new_instance, $instance )
  {
    $instance['title']                    = esc_html($new_instance['title']);
    $instance['height']                   = esc_html($new_instance['height']);
    $instance['showtitle']                = esc_html($new_instance['showtitle']);
    $instance['fullcontent']              = esc_html($new_instance['fullcontent']);
    $instance['ajax_loading']             = esc_html($new_instance['ajax_loading']);
    return $instance;
  }
   
  public function form( $instance )
  {
    $instance                             = wp_parse_args( $instance, array(
                                            'title'           => '&#2310;&#2332;&#2325;&#2379; &#2352;&#2366;&#2358;&#2367;&#2347;&#2354;',
                                            'height'          => 'auto',
                                            'showtitle'       => true,
                                            'fullcontent'     => false,
                                            'ajax_loading'    => false
                                            ) );
    ?>
    <p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>">Title:</label>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo esc_attr($instance['title']); ?>" style="width:70%; float:right;" />
    </p>
    <p>
    <label for="<?php echo $this->get_field_id( 'height' ); ?>">Height:</label>
    <input type="text" class="widefat" id="<?php echo $this->get_field_id( 'height' ); ?>" name="<?php echo $this->get_field_name( 'height' ); ?>" value="<?php echo esc_attr($instance['height']); ?>" style="width:70%; float:right;" />
    </p>
    <p>
    <input type="checkbox" class="widefat" id="<?php echo $this->get_field_id( 'showtitle' ); ?>" name="<?php echo $this->get_field_name( 'showtitle' ); ?>"  value="1" <?php echo $instance['showtitle']?'checked="checked"':''; ?>/>
    <label for="<?php echo $this->get_field_id( 'showtitle' ); ?>">Show title</label>
    </p>
    <p>
    <input type="checkbox" class="widefat" id="<?php echo $this->get_field_id( 'fullcontent' ); ?>" name="<?php echo $this->get_field_name( 'fullcontent' ); ?>"  value="1" <?php echo $instance['fullcontent']?'checked="checked"':''; ?>/>
    <label for="<?php echo $this->get_field_id( 'fullcontent' ); ?>">Show full content</label>
    </p>
    <p>
    <input type="checkbox" class="widefat" id="<?php echo $this->get_field_id( 'ajax_loading' ); ?>" name="<?php echo $this->get_field_name( 'ajax_loading' ); ?>"  value="1" <?php echo $instance['ajax_loading']?'checked="checked"':''; ?>/>
    <label for="<?php echo $this->get_field_id( 'ajax_loading' ); ?>">Lazy Loading (Recommended)</label>
    </p>
    <?php
  }
  
  public function wp_footer()
  {
    ?>
    <script type="text/javascript">
     (function($){
       var _c = $('.aj-content');
       if(_c.length<1) return;
       $('.aj-box').on('click','.aj-show-more',function(){
          $(this).children('span').toggle();
          $(this).prev().toggle();
         });
       
       $.post('<?php echo admin_url('admin-ajax.php'); ?>',{action:'aj_rashifal',_c:'<?php echo urlencode($_SERVER['REQUEST_URI']); ?>'},function(_r){
         _c.each(function(index, elm) {
          var t = $(this),i=0;
          t.find('.aj-loader').hide();
          $.each(_r.content,function(key,value){
            if(value.length>120)
            {
              var j = value.indexOf(' ',100);
              value = value.substring(0, j) + '<span class="aj-show">' + value.substring(j) + '</span><a class="aj-show-more" href="javascript:void(0);"><span>..&#2309;&#2333; &#2348;&#2338;&#2368;</span><span style="display:none;"> &#x25B2;</span></a>' ;
            }
            var $c = '<div class="aj-c-body"><strong>'+key+':</strong><i style="background-position:0px -'+(_r.stars[i] -1) * 11+'px" class="aj-star luck-'+_r.stars[i]+'"></i><br /> '+value+'</div><div class="aj-hr"></div>';
            t.append($c);       
            ++i;     
            })
          });
         },'json');
       
       })(jQuery);
    </script>
    <?php
  }


}
