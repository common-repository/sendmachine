<?php

class Sm_subscribe_widget extends WP_Widget {

	function __construct() {

		$options = array('description' => __('Allow visitors to subscribe to your contact lists using this widget.', SM_LANGUAGE_DOMAIN));
		$title = __('Sendmachine list subscription', SM_LANGUAGE_DOMAIN);

		parent::__construct('sm_subscribe_widget', $title, $options);
	}

	function widget($args, $instance) {

		echo Sm_wp::instance()->list_manager->build_widget_form($args, $instance);
	}

	function update($new_instance, $old_instance) {

		$instance = $old_instance;
		$instance['title'] = $new_instance['title'];
		$instance['description'] = $new_instance['description'];

		return $instance;
	}

	function form($instance) {

		$title = isset($instance['title']) ? $instance['title'] : "";
		$description = isset($instance['description']) ? $instance['description'] : "";
		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', SM_LANGUAGE_DOMAIN) ?></label>
			<input class="widefat" type="text" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>">
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('description'); ?>"><?php _e('Description:', SM_LANGUAGE_DOMAIN) ?></label>
			<textarea class="widefat" rows="5" cols="20" id="<?php echo $this->get_field_id('description'); ?>" name="<?php echo $this->get_field_name('description'); ?>"><?php echo esc_attr($description); ?></textarea>
		</p>
		<?php
	}

}
