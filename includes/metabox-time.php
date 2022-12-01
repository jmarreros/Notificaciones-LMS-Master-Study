<?php
// Add field time to courses

namespace dcms\notifications\includes;

class MetaboxTime{

	public function __construct(){
		add_action( 'add_meta_boxes', [$this, 'dcms_add_metabox_time'] );
		add_action( 'save_post_stm-courses', [$this, 'dcms_save_metabox_content'], 30, 2 ); // Priority 30
	}

	public function dcms_add_metabox_time(){
		add_meta_box(
			'dcms_metabox_time',
			'Hora de inicio del curso',
			[$this, 'dcms_add_metabox_content'],
			'stm-courses',
			'side'
		);
	}

	public function dcms_add_metabox_content( $post ){
		$start_time = get_post_meta( $post->ID, DCMS_NOTIF_COURSE_TIME, true );
		$start_time = date('H:i', strtotime($start_time));
		?>
		<label for="course-time" >Hora de inicio</label>
		<input id="course-time" name="course-time" type="time" value="<?= $start_time ?>" >
		<?php
	}

	public function dcms_save_metabox_content( $post_id, $post ){
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;

		$meta_date = get_post_meta( $post_id, DCM_COURSE_DATE, true );
		$start_time = $meta_date . ' ' . $_POST['course-time'];
		update_post_meta( $post_id, DCMS_NOTIF_COURSE_TIME, $start_time );
	}
}
