<?php

interface Pngx__Documentation__Swagger__Builder_Interface {
	/**
	 * Registers a documentation provider for a path.
	 *
	 * @param                                            $path
	 * @param Pngx__REST__Endpoints__READ_Endpoint_Interface $endpoint
	 */
	public function register_documentation_provider( $path, Pngx__Documentation__Swagger__Provider_Interface $endpoint );

	/**
	 * @return Pngx__Documentation__Swagger__Provider_Interface[]
	 */
	public function get_registered_documentation_providers();

	/**
	 * Registers a documentation provider for a definition.
	 *
	 * @param                                                  string $type
	 * @param Pngx__Documentation__Swagger__Provider_Interface       $provider
	 */
	public function register_definition_provider( $type, Pngx__Documentation__Swagger__Provider_Interface $provider );

	/**
	 * @return Pngx__Documentation__Swagger__Provider_Interface[]
	 */
	public function get_registered_definition_providers();
}