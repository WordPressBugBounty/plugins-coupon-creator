<?php

class Pngx__Documentation__Swagger__Image_Size_Definition_Provider
	implements Pngx__Documentation__Swagger__Provider_Interface {

	/**
	 * Returns an array in the format used by Swagger 2.0.
	 *
	 * While the structure must conform to that used by v2.0 of Swagger the structure can be that of a full document
	 * or that of a document part.
	 * The intelligence lies in the "gatherer" of informations rather than in the single "providers" implementing this
	 * interface.
	 *
	 * @link http://swagger.io/
	 *
	 * @return array An array description of a Swagger supported component.
	 */
	public function get_documentation() {
		$documentation = array(
			'type'       => 'object',
			'properties' => array(
				'width' => array(
					'type' => 'integer',
					'description' => __( 'The image width in pixels in the specified size', 'plugin-engine' ),
				),
				'height' => array(
					'type' => 'integer',
					'description' => __( 'The image height in pixels in the specified size', 'plugin-engine' ),
				),
				'mime-type' => array(
					'type' => 'string',
					'description' => __( 'The image mime-type', 'plugin-engine' ),
				),
				'url' => array(
					'type' => 'string',
					'format' => 'uri',
					'description' => __( 'The link to the image in the specified size on the site', 'plugin-engine' ),
				),
			),
		);

		/**
		 * Filters the Swagger documentation generated for an image size in the PNGX REST API.
		 *
		 * @param array $documentation An associative PHP array in the format supported by Swagger.
		 *
		 * @link http://swagger.io/
		 */
		$documentation = apply_filters( 'pngx_rest_swagger_image_size_documentation', $documentation );

		return $documentation;
	}
}