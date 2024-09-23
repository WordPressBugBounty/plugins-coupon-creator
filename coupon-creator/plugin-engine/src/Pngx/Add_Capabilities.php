<?php

/**
 * Class Pngx__Add_Capabilities
 *
 * Adds Capabilities for a CPT
 */
class Pngx__Add_Capabilities {
	/**
	 * Get Defualt Roles.
	 *
	 * @since 4.0.0
	 *
	 * @return array<string> $roles An array of default roles.
	 */
	public function get_default_roles() {
		$roles = [
			get_role( 'administrator' ),
			get_role( 'editor' ),
			get_role( 'author' ),
			get_role( 'contributor' ),
			get_role( 'subscriber' ),
		];

		return $roles;
	}

	/**
	 * Remove Role Capabilities.
	 *
	 * @since 4.0.0
	 *
	 * @param string $capability_type A capability type to remove.
	 *
	 * @return array<string> $caps An array of capability types by role.
	 */
	public function get_capabilities( $capability_type ) {
		$caps = [];

		$singular = $capability_type;
		if ( substr( $capability_type, - 1 ) === 's' ) {
			$singular = substr($singular, 0, -1);
		}
		$plural   = $capability_type;
		if ( substr( $capability_type, - 1 ) !== 's' ) {
			$plural .= 's';
		}

		//Administrator
		$caps['administrator'] = array(
			"read_{$singular}",
			"read_private_{$plural}",
			"edit_{$singular}",
			"edit_{$plural}",
			"edit_private_{$plural}",
			"edit_published_{$plural}",
			"edit_others_{$plural}",
			"publish_{$plural}",
			"delete_{$singular}",
			"delete_{$plural}",
			"delete_private_{$plural}",
			"delete_published_{$plural}",
			"delete_others_{$plural}",
		);
		//Editor
		$caps['editor'] = array(
			"read_{$singular}",
			"read_private_{$plural}",
			"edit_{$singular}",
			"edit_{$plural}",
			"edit_private_{$plural}",
			"edit_published_{$plural}",
			"edit_others_{$plural}",
			"publish_{$plural}",
			"delete_{$singular}",
			"delete_{$plural}",
			"delete_private_{$plural}",
			"delete_published_{$plural}",
			"delete_others_{$plural}",
		);
		//Author
		$caps['author'] = array(
			"edit_{$singular}",
			"read_{$singular}",
			"delete_{$singular}",
			"delete_{$plural}",
			"edit_{$plural}",
			"publish_{$plural}",
			"edit_published_{$plural}",
			"delete_published_{$plural}",
		);
		//Contributor
		$caps['contributor'] = array(
			"edit_{$singular}",
			"read_{$singular}",
			"delete_{$singular}",
			"delete_{$plural}",
			"edit_{$plural}",

		);
		//Subscriber
		$caps['subscriber'] = array(
			"read_{$singular}",
		);

		return $caps;
	}

	/**
	 * Add Role Capabilities.
	 *
	 * @since 4.0.0
	 *
	 * @param string $capability_type A capability type to remove.
	 */
	public function add_capabilities( $capability_type ) {
		$caps = $this->get_capabilities( $capability_type );
		$roles = $this->get_default_roles();

		//Add Capabilities to Role if Role Exists
		foreach ( $roles as $role ) {

			$role_check = '';
			if ( is_object( $role ) ) {
				$role_check = get_role( $role->name );
			}

			if ( ! empty( $role_check ) ) {
				foreach ( $caps[ $role->name ] as $cap ) {
					$role->add_cap( $cap );
				}
			}
		}

		//Set Option to Prevent this from Running Again
		update_option( $capability_type . '_capabilities_register', date( 'l jS \of F Y h:i:s A' ) );
	}

	/**
	 * Remove Role Capabilities.
	 *
	 * @since 4.0.0
	 *
	 * @param string $capability_type A capability type to remove.
	 */
	public function remove_capabilities( $capability_type ) {
		$caps = $this->get_capabilities( $capability_type );
		$roles = $this->get_default_roles();

		//Remove Capabilities to Role if Role Exists
		foreach ( $roles as $role ) {
			$role_check = '';
			if ( is_object( $role ) ) {
				$role_check = get_role( $role->name );
			}

			if ( ! empty( $role_check ) ) {
				foreach ( $caps[ $role->name ] as $cap ) {
					$role->remove_cap( $cap );
				}
			}
		}
	}
}