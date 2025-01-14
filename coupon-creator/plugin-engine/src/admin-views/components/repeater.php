<?php
/**
 * View: Read only field.
 *
 * Override this template in your own theme by creating a file at:
 * [your-theme]/pngx/admin-views/components/read-only.php
 *
 * See more documentation about our views templating system.
 *
 * @since   4.0.0
 *
 * @version 4.0.0
 *
 * @link    https://pngx.ink/RYIOh
 *
 * @var array<string,string> $classes_wrap    An array of classes for the read only field.
 * @var array<string,string> $classes_input   An array of classes for the input.
 * @var string               $label           The label for the hidden input.
 * @var string               $screen_reader   The screen reader instructions for the text input.
 * @var string               $id              ID of the hidden input.
 * @var string               $name            The name for the hidden input.
 * @var string               $value           The value of the ready only field.
 * @var string               $display_value   The display value of the ready only field.
 * @var string               $repeater_fields The display value of the ready only field.
 * @var Template             $template        An instance of the admin template
 */

use Pngx\Admin\Field\V2\Repeater;

?>
<div class="pngx-repeater-container" data-repeater-name="<?php echo esc_attr( $name ); ?>">
	<div id="<?php echo esc_attr( $name ); ?>-repeater-list" data-repeater-list="<?php echo esc_attr( $name ); ?>">
		<div data-repeater-item style="display: none;" class="pngx-repeater-item">
			<div class="pngx-repeater-item-handle">&#9776;</div>
			<?php foreach ( $repeater_fields as $repeater_field ) : ?>
				<?php
				$repeater_field_name  = "{$name}[{{{index}}}][{$repeater_field['id']}]";
				$repeater_field_value = '';

				if ( $repeater_field['type'] === 'wooselect' ) {
					if ( ! isset( $repeater_field['classes_select'] ) ) {
						$repeater_field['classes_select'] = [];
					}
					$repeater_field['classes_select'][] = 'pngx-dropdown-ignore';
				}

				echo Repeater::display_repeater_field( $repeater_field, $repeater_field_name, $repeater_field_value, $this );
				?>
			<?php endforeach; ?>
			<button data-repeater-delete type="button">
				<span aria-hidden="true">&times;</span>
				<span class="screen-reader-text">Delete</span>
			</button>
		</div>
		<?php foreach ( $value as $index => $item ) : ?>
			<div data-repeater-item class="pngx-repeater-item">
				<div class="pngx-repeater-item-handle">&#9776;</div>
				<?php foreach ( $repeater_fields as $repeater_field ) : ?>
					<?php
					$repeater_field_name  = "{$name}[{$index}][{$repeater_field['id']}]";
					$repeater_field_value = isset( $item[ $repeater_field['id'] ] ) ? $item[ $repeater_field['id'] ] : '';
					echo Repeater::display_repeater_field( $repeater_field, $repeater_field_name, $repeater_field_value, $this );
					?>
				<?php endforeach; ?>
				<button data-repeater-delete type="button">
					<span aria-hidden="true">&times;</span>
					<span class="screen-reader-text">Delete</span>
				</button>
			</div>
		<?php endforeach; ?>
	</div>
	<button data-repeater-create class="button-primary" type="button">Add</button>
</div>