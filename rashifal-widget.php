<?php
class Aj_Rashifal_Widget extends WP_Widget
{
  public function __construct() 
  {
    parent::__construct('aj-rashifal',__('&#2310;&#2332;&#2325;&#2379; &#2352;&#2366;&#2358;&#2367;&#2347;&#2354;', 'aj-rashifal'),array( 'description' =>'' ));
  }

  public function widget( $args, $instance )
  {
    $site_url                             = 'http://api.aajako.com/rashifal';
    
    $ch                                   = curl_init();
    curl_setopt($ch, CURLOPT_URL, $site_url);
    curl_setopt($ch, CURLOPT_HEADER, 0);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
    $f                                    = curl_exec($ch);
    curl_close($ch);

    $d                                    = json_decode($f);
    ?>
    <div class="aj-box">
      <?php if($instance['showtitle']){ ?>
      <div class="aj-title"><?php echo $instance['title'];?></div>
      <div class="aj-divider"></div>
      <?php } ?>
      <div class="aj-content" style="height:<?php echo is_numeric($instance['height'])?$instance['height'].'px':$instance['height']; ?>">
      <?php foreach( $d as $n=>$c ){ 
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
        <div class="aj-c-body"><strong><?php echo $n; ?>:</strong> <?php echo $c; ?></div>
        <div class="aj-hr"></div>
      <?php } ?>
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
    return $instance;
  }
   
  public function form( $instance )
  {
    $instance                             = wp_parse_args( $instance, array(
                                            'title'           => '&#2310;&#2332;&#2325;&#2379; &#2352;&#2366;&#2358;&#2367;&#2347;&#2354;',
                                            'height'          => 'auto',
                                            'showtitle'       => true,
                                            'fullcontent'     => false
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
    <?php
  }
}
